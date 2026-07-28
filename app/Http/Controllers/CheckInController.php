<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\Transaction;
use App\Services\CertificateService;
use Illuminate\Http\Request;

class CheckInController extends Controller
{
    protected CertificateService $certService;

    public function __construct(CertificateService $certService)
    {
        $this->certService = $certService;
    }

    /**
     * Tampilkan halaman scanner QR untuk panitia
     */
    public function scan()
    {
        return view('checkin.scan');
    }

    /**
     * Verifikasi QR code tiket dan catat check-in
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code'         => 'required|string',
            'scanner_user' => 'nullable|string|max:100',
        ]);

        $code = trim($request->input('code'));

        // Cari transaksi berdasarkan order_id (digunakan sebagai ticket QR payload)
        $transaction = Transaction::where('order_id', $code)
                                  ->whereIn('status', ['success', 'Success', 'settlement'])
                                  ->with('event')
                                  ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => '❌ Tiket tidak ditemukan atau belum dibayar.',
            ], 404);
        }

        // 🔥 KONTROL HAK AKSES SCANNER: Pastikan jika yang memindai adalah Organizer/Tenant, tiket yang di-scan adalah event miliknya
        $user = \Illuminate\Support\Facades\Auth::guard('admin')->user() ?? \Illuminate\Support\Facades\Auth::guard('organizer')->user() ?? auth()->user();
        if ($user && $user->isOrganizer() && $transaction->tenant_id !== $user->tenant_id) {
            return response()->json([
                'success' => false,
                'message' => '❌ Akses Ditolak! Tiket ini milik event dari organisasi lain.',
            ], 403);
        }

        // Cek apakah sudah pernah check-in sebelumnya (prevent double entry)
        $existing = CheckIn::where('ticket_code', $code)->first();
        if ($existing) {
            return response()->json([
                'success'       => false,
                'message'       => 'Tiket ini sudah digunakan pada ' . $existing->checked_at->format('d M Y H:i') . '.',
                'customer_name' => $existing->attendee_name,
                'event_title'   => $transaction->event->title,
                'order_id'      => $code,
                'checked_in_at' => $existing->checked_at->format('d M Y H:i'),
            ], 409);
        }

        // Catat check-in
        $checkIn = CheckIn::create([
            'ticket_code'    => $code,
            'transaction_id' => $transaction->id,
            'attendee_name'  => $transaction->customer_name,
            'attendee_email' => $transaction->customer_email,
            'scanner_ip'     => $request->ip(),
            'scanner_user'   => $request->input('scanner_user', 'Panitia'),
            'checked_at'     => now(),
        ]);

        // Tandai status tiket di transaksi menjadi used agar sinkron di database & laporan
        $transaction->update([
            'attendance_status' => 'used',
            'status'            => 'used'
        ]);

        // 🔥 OPTIMASI ULTRA FAST SCAN: Kirim response sukses SECEPATNYA dalam 0.1 detik ke scanner panitia
        // Kita bypass/jalankan generate sertifikat setelah response dikirim ke klien agar tidak memblokir thread HTTP.
        if (function_exists('fastcgi_finish_request')) {
            // Jika berjalan di FPM (hosting/vercel)
            response()->json([
                'success'       => true,
                'message'       => 'Check-in berhasil! Sertifikat dikirim ke email peserta.',
                'customer_name' => $transaction->customer_name,
                'event_title'   => $transaction->event->title,
                'order_id'      => $code,
                'checkin_time'  => $checkIn->checked_at->format('H:i:s'),
            ], 200)->send();
            
            fastcgi_finish_request(); // Kirim data ke layar scanner langsung & matikan penantian HTTP
            
            try {
                $this->certService->generate($transaction);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Certificate generation failed after fast response: ' . $e->getMessage());
            }
            exit;
        }

        // Fallback untuk server non-FPM (lokal php artisan serve)
        try {
            // Untuk meminimalkan latensi saat test lokal, jalankan dengan aman
            $this->certService->generate($transaction);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Certificate generation failed: ' . $e->getMessage());
        }

        return response()->json([
            'success'       => true,
            'message'       => 'Check-in berhasil! Sertifikat dikirim ke email peserta.',
            'customer_name' => $transaction->customer_name,
            'event_title'   => $transaction->event->title,
            'order_id'      => $code,
            'checkin_time'  => $checkIn->checked_at->format('H:i:s'),
        ], 200);
    }
}
