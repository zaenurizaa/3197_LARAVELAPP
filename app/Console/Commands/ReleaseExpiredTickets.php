<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

class ReleaseExpiredTickets extends Command
{
    /**
     * Perintah terminal: php artisan tickets:release-expired
     */
    protected $signature = 'tickets:release-expired';
    protected $description = 'Lepas reservasi stok tiket yang kedaluwarsa (>15 menit) dan belum dibayar';

    public function handle()
    {
        // Cari transaksi pending/reserved yang kedaluwarsa berdasarkan kolom reserved_until
        $expiredTransactions = Transaction::where('is_reserved', true)
            ->where('status', 'pending')
            ->where('reserved_until', '<', now())
            ->get();

        $releasedCount = 0;

        foreach ($expiredTransactions as $trx) {
            DB::transaction(function () use ($trx, &$releasedCount) {
                $transaction = Transaction::where('id', $trx->id)->lockForUpdate()->first();

                if ($transaction && $transaction->is_reserved && strtolower($transaction->status) === 'pending') {
                    // Kembalikan stok tiket (+1)
                    $event = Event::where('id', $transaction->event_id)->lockForUpdate()->first();
                    if ($event) {
                        $event->increment('stock');
                    }

                    // Update status transaksi menjadi expired & lepas reservasi
                    $transaction->update([
                        'status'         => 'expired',
                        'is_reserved'    => false,
                        'reserved_until' => null,
                    ]);
                    $releasedCount++;
                }
            });
        }

        $this->info("Berhasil merilis {$releasedCount} tiket kedaluwarsa kembali ke stok.");
    }
}