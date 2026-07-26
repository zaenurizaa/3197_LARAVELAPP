<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Event;
use App\Models\Coupon;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\EventTicketMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;

class CheckoutController extends Controller
{
    /**
     * Menampilkan form checkout / pemesanan tiket
     */
    public function create(Event $event): View
    {
        $categories = Category::all();
        return view('checkout.create', compact('event', 'categories'));
    }

    /**
     * Menyimpan data transaksi awal dan membuat token Snap Midtrans via AJAX
     * Memotong stok riil secara instan & mencatat status is_reserved pada tabel transactions
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'event_id'       => 'required|exists:events,id',
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'coupon_code'    => 'nullable|string'
        ]);

        $fonnteToken = config('services.fonnte.token', env('FONNTE_TOKEN'));

        try {
            // Gunakan DB Transaction & lockForUpdate untuk mencegah Race Condition
            $result = DB::transaction(function () use ($request, $fonnteToken) {
                
                // Lock row event agar aman dari request berbarengan
                $event = Event::where('id', $request->event_id)->lockForUpdate()->firstOrFail();

                // Cek Stok Riil
                if ($event->stock <= 0) {
                    throw new \Exception('Maaf, stok tiket baru saja habis dibeli oleh pengguna lain.');
                }

                // Kalkulasi Kupon & Dynamic Pricing Tier
                $basePrice = $event->effective_price;
                $discount  = 0;
                $couponId  = null;

                if ($request->filled('coupon_code')) {
                    $couponCode = strtoupper(trim($request->coupon_code));
                    $coupon = Coupon::where('code', $couponCode)->first();

                    if (!$coupon) {
                        throw new \Exception('Kode kupon "' . $couponCode . '" tidak ditemukan.');
                    }

                    if ($coupon->quota <= 0) {
                        throw new \Exception('Maaf, kuota penggunaan kupon "' . $couponCode . '" telah habis.');
                    }

                    if ($coupon->expires_at && now()->gt($coupon->expires_at)) {
                        throw new \Exception('Maaf, kupon "' . $couponCode . '" telah kedaluwarsa.');
                    }

                    if ($coupon->type === 'percent') {
                        $discount = ($coupon->discount_value / 100) * $basePrice;
                    } else {
                        $discount = min($basePrice, $coupon->discount_value);
                    }

                    $couponId = $coupon->id;
                    $coupon->decrement('quota');
                }

                $finalPrice = max(0, $basePrice - $discount);
                
                // Cek apakah acara gratis (harga tiket Rp 0 / flag is_free / diskon total)
                $isFreeEvent = ($basePrice == 0 || (isset($event->is_free) && $event->is_free) || $finalPrice == 0);
                $serviceFee  = $isFreeEvent ? 0 : 5000;
                $grandTotal  = $isFreeEvent ? 0 : ($finalPrice + $serviceFee);

                // =========================================================================
                // EVENT GRATIS / RP 0 (BYPASS ALUR MIDTRANS)
                // =========================================================================
                if ($isFreeEvent || $grandTotal == 0) {
                    $orderId = 'FREE-' . time() . '-' . rand(100, 999);

                    // Potong stok tiket langsung saat itu juga
                    $event->decrement('stock');

                    $transaction = Transaction::create([
                        'order_id'          => $orderId,
                        'event_id'          => $event->id,
                        'tenant_id'         => $event->tenant_id ?? $event->organizer_id ?? null,
                        'coupon_id'         => $couponId,
                        'customer_name'     => $request->customer_name,
                        'customer_email'    => $request->customer_email,
                        'customer_phone'    => $request->customer_phone,
                        'customer_whatsapp' => $request->customer_phone,
                        'total_price'       => 0,
                        'status'            => 'success',
                        'is_reserved'       => false,
                        'reserved_until'    => null,
                    ]);

                    // Kirim Mail E-Ticket secara langsung
                    try {
                        Mail::to($transaction->customer_email)->send(new EventTicketMail($transaction));
                    } catch (\Exception $e) {
                        Log::error('Gagal mengirim email E-Ticket Event Gratis: ' . $e->getMessage());
                    }

                    // Kirim Notifikasi WA via Fonnte
                    try {
                        $phoneWa = str_replace([' ', '-', '+'], '', $transaction->customer_phone);
                        if (substr($phoneWa, 0, 2) === '08') {
                            $phoneWa = '628' . substr($phoneWa, 2);
                        }

                        $msgFree = "*E-TICKET AMIKOM EVENT HUB* 🎯\n\n";
                        $msgFree .= "Halo *{$transaction->customer_name}*,\n";
                        $msgFree .= "Pemesanan tiket gratis Anda untuk event *{$event->title}* BERHASIL.\n\n";
                        $msgFree .= "▪️ *Order ID:* {$transaction->order_id}\n";
                        $msgFree .= "▪️ *Status:* BERHASIL (GRATIS)\n\n";
                        $msgFree .= "Silakan periksa kotak masuk email Anda untuk tiket resminya.\n_Amikom Event Hub Team_";

                        Http::withHeaders(['Authorization' => $fonnteToken])->asForm()->post('https://api.fonnte.com/send', [
                            'target'      => $phoneWa,
                            'message'     => $msgFree,
                            'countryCode' => '62'
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Gagal kirim WA Tiket Gratis: ' . $e->getMessage());
                    }

                    // Pembeli langsung diarahkan ke rute sukses (Bypass Midtrans)
                    return [
                        'redirect_url' => route('checkout.success', $transaction->order_id)
                    ];
                }

                // =========================================================================
                // EVENT BERBAYAR (RESERVE & POTONG STOK SEGERA)
                // =========================================================================
                $orderId = 'EVT-' . time() . '-' . rand(100, 999);

                // Potong stok riil di database agar tidak bisa dibeli orang lain
                $event->decrement('stock');

                $transaction = Transaction::create([
                    'order_id'          => $orderId,
                    'event_id'          => $event->id,
                    'tenant_id'         => $event->tenant_id ?? $event->organizer_id ?? null,
                    'coupon_id'         => $couponId,
                    'customer_name'     => $request->customer_name,
                    'customer_email'    => $request->customer_email,
                    'customer_phone'    => $request->customer_phone,
                    'customer_whatsapp' => $request->customer_phone,
                    'total_price'       => $grandTotal,
                    'status'            => 'pending',
                    'is_reserved'       => true,
                    'reserved_until'    => now()->addMinutes(15), // Kunci selama 15 menit
                ]);

                \Midtrans\Config::$serverKey    = config('midtrans.server_key');
                \Midtrans\Config::$isProduction = config('midtrans.is_production');
                \Midtrans\Config::$isSanitized  = true;
                \Midtrans\Config::$is3ds        = true;

                $params = [
                    'transaction_details' => [
                        'order_id'     => $orderId,
                        'gross_amount' => (int) $grandTotal,
                    ],
                    'customer_details' => [
                        'first_name' => substr($transaction->customer_name, 0, 30),
                        'email'      => $transaction->customer_email,
                        'phone'      => $transaction->customer_phone,
                    ],
                    'item_details' => [
                        [
                            'id'       => 'TKT-' . $event->id,
                            'price'    => (int) $finalPrice,
                            'quantity' => 1,
                            'name'     => substr($event->title, 0, 40),
                        ],
                        [
                            'id'       => 'FEE-LAYANAN',
                            'price'    => (int) $serviceFee,
                            'quantity' => 1,
                            'name'     => 'Biaya Layanan Aplikasi',
                        ]
                    ],
                    'expiry' => [
                        'start_time' => date("Y-m-d H:i:s O"),
                        'unit'       => 'minute',
                        'duration'   => 15
                    ]
                ];

                $snapToken = \Midtrans\Snap::getSnapToken($params);
                $transaction->update(['snap_token' => $snapToken]);

                $payUrl = route('checkout.payment', $transaction->order_id);

                // Kirim Notifikasi WA Pengingat Checkout Instan
                try {
                    $targetPhone = str_replace([' ', '-', '+'], '', $transaction->customer_phone);
                    if (substr($targetPhone, 0, 2) === '08') {
                        $targetPhone = '628' . substr($targetPhone, 2);
                    }

                    $msg = "🔔 *PENGINGAT PEMBAYARAN: Amikom Event Hub* 🔔\n\n";
                    $msg .= "Yth. *{$transaction->customer_name}*,\n\n";
                    $msg .= "Terima kasih telah melakukan pemesanan tiket untuk event *{$event->title}*.\n\n";
                    $msg .= "Sistem kami mendeteksi bahwa transaksi Anda belum selesai. Jika Anda tidak sengaja menutup halaman browser atau mengalami kendala koneksi, Anda dapat melanjutkan proses pembayaran secara aman melalui tautan resmi berikut:\n\n";
                    $msg .= "🔗 *Link Pembayaran:* {$payUrl}\n\n";
                    $msg .= "⚠️ *PENTING:* Mohon selesaikan pembayaran Anda dalam waktu *15 menit* sebelum sistem otomatis membatalkan pesanan Anda untuk melepaskan kuota tiket kepada calon pembeli lain.\n\n";
                    $msg .= "Jika Anda sudah berhasil melakukan pembayaran, silakan abaikan pesan ini.\n\n";
                    $msg .= "Salam hangat,\n*Amikom Event Hub Team*";

                    Http::withHeaders([
                        'Authorization' => $fonnteToken
                    ])->asForm()->post('https://api.fonnte.com/send', [
                        'target'      => $targetPhone,
                        'message'     => $msg,
                        'countryCode' => '62'
                    ]);
                } catch (\Exception $e) {
                    Log::error('Fonnte Recovery Instan Error: ' . $e->getMessage());
                }

                return [
                    'redirect_url' => $payUrl
                ];
            });

            return response()->json([
                'success'      => true,
                'redirect_url' => $result['redirect_url']
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal memproses checkout: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Menampilkan halaman pembayaran dengan modal Snap Midtrans
     */
    public function payment(string $order_id): View
    {
        $categories  = Category::all();
        $transaction = Transaction::with('event')->where('order_id', $order_id)->firstOrFail();
        return view('checkout.payment', compact('transaction', 'categories'));
    }

    /**
     * Route Binding Success Check
     */
    public function success(string $order_id): View|RedirectResponse
    {
        $categories  = Category::all();
        $transaction = Transaction::where('order_id', $order_id)->firstOrFail();

        if (strtolower($transaction->status) === 'success') {
            return view('checkout.success', compact('transaction', 'categories'));
        }

        \Midtrans\Config::$serverKey    = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = true;

        try {
            $midtransStatus = (object) \Midtrans\Transaction::status($transaction->order_id);

            if (isset($midtransStatus->transaction_status) && in_array($midtransStatus->transaction_status, ['capture', 'settlement'])) {

                if (strtolower($transaction->status) === 'pending') {
                    // Update status sukses & bersihkan reservasi
                    $transaction->update([
                        'status'         => 'success',
                        'is_reserved'    => false,
                        'reserved_until' => null,
                    ]);

                    try {
                        Mail::to($transaction->customer_email)->send(new EventTicketMail($transaction));
                    } catch (\Exception $e) {
                        Log::error('Gagal mengirim email E-Ticket via Fallback Check: ' . $e->getMessage());
                    }
                }

                return view('checkout.success', compact('transaction', 'categories'));
            }

            return redirect()->route('checkout.payment', $transaction->order_id)->with('error', 'Pembayaran belum diselesaikan.');
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Gagal memverifikasi pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Memproses QR Code Check-in & Kirim E-Sertifikat Otomatis
     */
    public function processCheckIn(string $order_id): JsonResponse
    {
        $trx = Transaction::with('event')->where('order_id', $order_id)->first();

        if ($trx && $trx->attendance_status == 'pending') {

            $trx->update(['attendance_status' => 'used']);

            $this->sendEmailCertificate($trx);

            return response()->json([
                'success' => true,
                'msg'     => 'Valid! Selamat datang. E-Sertifikat kehadiran telah dikirim ke email Anda.'
            ]);
        }

        return response()->json([
            'success' => false,
            'msg'     => 'Tiket tidak valid/sudah dipakai.'
        ], 400);
    }

    /**
     * Helper Generator & Mailer PDF E-Sertifikat Kehadiran
     */
    private function sendEmailCertificate(Transaction $transaction): void
    {
        try {
            $data = [
                'name'  => $transaction->customer_name,
                'event' => $transaction->event->title
            ];

            $pdf = Pdf::loadView('pdf.certificate', $data)->setPaper('a4', 'landscape');

            Mail::send([], [], function ($message) use ($transaction, $pdf) {
                $message->to($transaction->customer_email)
                    ->subject('📜 E-Sertifikat Kehadiran Resmi AmikomEventHub')
                    ->html("Halo <strong>{$transaction->customer_name}</strong>,<br><br>Terima kasih telah menghadiri acara <strong>{$transaction->event->title}</strong>. Terlampir sertifikat digital resmi bentuk penghargaan kehadiran Anda.")
                    ->attachData($pdf->output(), "Sertifikat-{$transaction->order_id}.pdf");
            });
        } catch (\Exception $e) {
            Log::error('Sistem gagal memproses cetak E-Sertifikat PDF: ' . $e->getMessage());
        }
    }

    /**
     * Store Review
     */
    public function storeReview(Request $request): RedirectResponse
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'rating'         => 'required|integer|between:1,5',
            'comment'        => 'nullable|string|max:1000'
        ]);

        $trx = Transaction::findOrFail($request->transaction_id);

        if (strtolower($trx->status) !== 'success' && $trx->attendance_status !== 'used') {
            return redirect()->back()->with('error', 'Anda hanya bisa memberikan ulasan jika pembayaran telah berhasil.');
        }

        Review::create([
            'transaction_id' => $trx->id,
            'event_id'       => $trx->event_id,
            'rating'         => $request->rating,
            'comment'        => $request->comment
        ]);

        return redirect()->back()->with('success', 'Terima kasih banyak! Ulasan bintang Anda berhasil disimpan.');
    }

    /**
     * AJAX Endpoint: Validasi & Terapkan Kode Kupon / Voucher
     */
    public function applyCoupon(Request $request): JsonResponse
    {
        $request->validate([
            'event_id'    => 'required|exists:events,id',
            'coupon_code' => 'required|string',
        ]);

        $event = Event::findOrFail($request->event_id);
        $basePrice = $event->effective_price;
        $couponCode = strtoupper(trim($request->coupon_code));

        $coupon = Coupon::where('code', $couponCode)->first();

        if (!$coupon) {
            return response()->json([
                'valid'   => false,
                'message' => 'Kode kupon "' . $couponCode . '" tidak ditemukan.'
            ], 404);
        }

        if ($coupon->quota <= 0) {
            return response()->json([
                'valid'   => false,
                'message' => 'Kuota voucher "' . $couponCode . '" sudah habis.'
            ], 422);
        }

        if ($coupon->expires_at && now()->gt($coupon->expires_at)) {
            return response()->json([
                'valid'   => false,
                'message' => 'Voucher "' . $couponCode . '" telah kedaluwarsa.'
            ], 422);
        }

        if ($coupon->type === 'percent') {
            $discount = ($coupon->discount_value / 100) * $basePrice;
            $discountLabel = (int)$coupon->discount_value . '%';
        } else {
            $discount = min($basePrice, $coupon->discount_value);
            $discountLabel = 'Rp ' . number_format($coupon->discount_value, 0, ',', '.');
        }

        $finalPrice = max(0, $basePrice - $discount);
        $isFree = ($basePrice == 0 || (isset($event->is_free) && $event->is_free) || $finalPrice == 0);
        $serviceFee = $isFree ? 0 : 5000;
        $grandTotal = $isFree ? 0 : ($finalPrice + $serviceFee);

        return response()->json([
            'success'               => true,
            'valid'                 => true,
            'message'               => 'Kupon ' . $couponCode . ' (' . $discountLabel . ') berhasil dipasang!',
            'coupon_code'           => $couponCode,
            'discount_value'        => $discount,
            'discount_formatted'    => 'Rp ' . number_format($discount, 0, ',', '.'),
            'base_price'            => $basePrice,
            'base_price_formatted'  => 'Rp ' . number_format($basePrice, 0, ',', '.'),
            'final_price'           => $finalPrice,
            'final_price_formatted' => 'Rp ' . number_format($finalPrice, 0, ',', '.'),
            'service_fee'           => $serviceFee,
            'service_fee_formatted' => 'Rp ' . number_format($serviceFee, 0, ',', '.'),
            'grand_total'           => $grandTotal,
            'grand_total_formatted' => $isFree ? 'GRATIS' : 'Rp ' . number_format($grandTotal, 0, ',', '.'),
            'is_free'               => $isFree,
        ]);
    }
}