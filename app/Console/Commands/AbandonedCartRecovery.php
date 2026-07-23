<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AbandonedCartRecovery extends Command
{
    /**
     * Perintah terminal: php artisan recovery:cart
     */
    protected $signature = 'recovery:cart';
    protected $description = 'Kirim Link Pembayaran Midtrans ke WA untuk transaksi pending yang ditinggal pembeli';

    public function handle()
    {
        // Cari transaksi pending yang berumur lebih dari 5 menit namun masih ter-reserve
        $carts = Transaction::with('event')
            ->where('status', 'pending')
            ->where('is_reserved', true)
            ->where('created_at', '<=', now()->subMinutes(5))
            ->where('reserved_until', '>', now())
            ->get();

        foreach ($carts as $cart) {
            $wa = $cart->customer_whatsapp ?? $cart->customer_phone;
            
            if ($wa) {
                $wa = str_replace([' ', '-', '+'], '', $wa);
                if (substr($wa, 0, 2) === '08') {
                    $wa = '628' . substr($wa, 2);
                }

                $payUrl = route('checkout.payment', $cart->order_id);
                
                $msg = "Halo *{$cart->customer_name}*,\n\nKami melihat Anda belum menyelesaikan transaksi tiket *{$cart->event->title}*. Tiket Anda terkunci aman secara eksklusif.\n\nSelesaikan pembayaran instan di sini sebelum pesanan dibatalkan otomatis: {$payUrl}";
                
                try {
                    $response = Http::withHeaders([
                        'Authorization' => config('services.fonnte.token', env('FONNTE_TOKEN'))
                    ])->asForm()->post('https://api.fonnte.com/send', [
                        'target'      => $wa, 
                        'message'     => $msg,
                        'countryCode' => '62'
                    ]);

                    if (!$response->successful()) {
                        Log::error("Fonnte API error untuk target {$wa}: " . $response->body());
                    }
                } catch (\Exception $e) {
                    Log::error("Gagal koneksi HTTP ke Fonnte untuk target {$wa}: " . $e->getMessage());
                }
            }
        }

        $this->info('Proses pengiriman pengingat abandoned cart selesai.');
    }
}