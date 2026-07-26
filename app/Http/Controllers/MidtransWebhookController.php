<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\EventTicketMail;
use Illuminate\Support\Facades\Http;

class MidtransWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        \Midtrans\Config::$serverKey    = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = true;
        \Midtrans\Config::$curlOptions  = [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_HTTPHEADER     => [],
        ];

        try {
            $notification       = new \Midtrans\Notification();
            $order_id           = $notification->order_id;
            $transaction_status = $notification->transaction_status;
            $type               = $notification->payment_type;
            $fraud              = $notification->fraud_status;

            DB::transaction(function () use ($order_id, $transaction_status, $type, $fraud) {
                $transaction = Transaction::where('order_id', $order_id)->lockForUpdate()->first();

                if (!$transaction) {
                    return;
                }

                if ($transaction_status == 'capture') {
                    if ($type == 'credit_card') {
                        if ($fraud == 'challenge') {
                            $transaction->update(['status' => 'challenge']);
                        } else {
                            $this->processSuccess($transaction);
                        }
                    }
                } elseif ($transaction_status == 'settlement') {
                    $this->processSuccess($transaction);
                } elseif (in_array($transaction_status, ['pending'])) {
                    $transaction->update(['status' => 'pending']);
                } elseif (in_array($transaction_status, ['deny', 'expire', 'cancel'])) {
                    $this->processFailedAndReleaseStock($transaction, $transaction_status);
                }
            });

            return response()->json(['message' => 'Webhook berhasil diproses']);
        } catch (\Exception $e) {
            Log::error('Error Webhook Midtrans: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan internal server: ' . $e->getMessage()], 500);
        }
    }

    private function processSuccess(Transaction $transaction)
    {
        if (strtolower($transaction->status) === 'pending') {
            $transaction->update([
                'status'         => 'success',
                'is_reserved'    => false,
                'reserved_until' => null,
            ]);

            // 1. Kirim Email E-Ticket
            try {
                Mail::to($transaction->customer_email)->send(new EventTicketMail($transaction));
                Log::info('Email E-Ticket berhasil dipicu untuk Order ID: ' . $transaction->order_id);
            } catch (\Exception $e) {
                Log::error('Gagal mengirim email E-Ticket via Webhook: ' . $e->getMessage());
            }

            // 2. Kirim WhatsApp Fonnte
            try {
                $pesan = "Halo *{$transaction->customer_name}*,\n\nPembayaran tiket untuk event *{$transaction->event->title}* BERHASIL diverifikasi!\n\nOrder ID: {$transaction->order_id}\nSilakan cek inbox email Anda untuk mengunduh berkas E-Ticket resmi.\n\nSampai jumpa di lokasi!";
                $this->sendWA($transaction->customer_whatsapp ?? $transaction->customer_phone, $pesan);
            } catch (\Exception $e) {
                Log::error('Gagal mengirim WhatsApp via Webhook: ' . $e->getMessage());
            }
        }
    }

    private function processFailedAndReleaseStock(Transaction $transaction, string $status)
    {
        if (in_array(strtolower($transaction->status), ['pending', 'challenge']) || $transaction->is_reserved) {
            // Restore stok tiket (+1)
            $event = Event::where('id', $transaction->event_id)->lockForUpdate()->first();
            if ($event) {
                $event->increment('stock');
            }

            $transaction->update([
                'status'         => $status === 'expire' ? 'expired' : 'failed',
                'is_reserved'    => false,
                'reserved_until' => null,
            ]);
            Log::info("Stok tiket untuk Order ID {$transaction->order_id} berhasil dikembalikan karena status: {$status}");
        }
    }

    private function sendWA(string $target, string $message) 
    {
        try {
            $target = str_replace([' ', '-', '+'], '', $target);
            if (substr($target, 0, 2) === '08') {
                $target = '628' . substr($target, 2);
            }

            Http::withoutVerifying()->withHeaders([
                'Authorization' => config('services.fonnte.token', env('FONNTE_TOKEN'))
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target'      => $target,
                'message'     => $message,
                'countryCode' => '62'
            ]);
        } catch (\Exception $e) {
            Log::error('Fonnte HTTP Connection Error: ' . $e->getMessage());
        }
    }
}