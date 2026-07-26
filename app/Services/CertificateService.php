<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateService
{
    /**
     * Generate PDF certificate for a given transaction and store it.
     * Returns the Certificate model instance.
     */
    public function generate(Transaction $transaction): Certificate
    {
        // Avoid duplicate certificate generation
        $existing = Certificate::where('transaction_id', $transaction->id)->first();
        if ($existing) {
            return $existing;
        }

        $event     = $transaction->event;
        $ticketCode = $transaction->order_id;

        $data = [
            'attendee_name' => $transaction->customer_name,
            'event_title'   => $event->title,
            'event_date'    => $event->date,
            'event_location'=> $event->location,
            'ticket_code'   => $ticketCode,
            'organizer_name'=> $event->tenant->name ?? 'AmikomEventHub',
            'issued_at'     => now(),
        ];

        // Render blade to PDF
        $pdf = Pdf::loadView('certificate.template', $data)
                  ->setPaper('A4', 'landscape');

        $filename  = 'cert_' . $ticketCode . '_' . Str::random(6) . '.pdf';
        $storagePath = 'certificates/' . $filename;

        Storage::disk('public')->put($storagePath, $pdf->output());

        $certificate = Certificate::create([
            'ticket_code'    => $ticketCode,
            'transaction_id' => $transaction->id,
            'attendee_name'  => $transaction->customer_name,
            'attendee_email' => $transaction->customer_email,
            'event_title'    => $event->title,
            'event_date'     => $event->date->format('Y-m-d'),
            'file_path'      => $storagePath,
            'issued_at'      => now(),
        ]);

        // Email certificate
        try {
            Mail::send([], [], function ($message) use ($transaction, $pdf, $filename) {
                $message->to($transaction->customer_email, $transaction->customer_name)
                        ->subject('🎓 Sertifikat Kehadiran – ' . $transaction->event->title)
                        ->html($this->buildEmailHtml($transaction))
                        ->attachData($pdf->output(), $filename, [
                            'mime' => 'application/pdf',
                        ]);
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Certificate email failed: ' . $e->getMessage());
        }

        return $certificate;
    }

    private function buildEmailHtml(Transaction $transaction): string
    {
        $name  = htmlspecialchars($transaction->customer_name);
        $event = htmlspecialchars($transaction->event->title);
        $downloadUrl = route('certificate.download', $transaction->order_id);
        
        return "
        <div style='font-family: \"Plus Jakarta Sans\", \"Segoe UI\", Helvetica, Arial, sans-serif; background-color: #f8fafc; padding: 40px 20px;'>
            <div style='max-width: 540px; margin: auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);'>
                <div style='background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); padding: 30px; text-align: center; color: #ffffff;'>
                    <span style='font-size: 40px;'>🎓</span>
                    <h2 style='margin: 10px 0 0 0; font-size: 20px; font-weight: 800; letter-spacing: 0.5px;'>Sertifikat Kehadiran Resmi</h2>
                </div>
                <div style='padding: 30px; color: #334155; line-height: 1.6;'>
                    <p style='margin-top: 0; font-size: 16px;'>Halo <strong>{$name}</strong>,</p>
                    <p style='font-size: 14px;'>Selamat! Anda telah terverifikasi menghadiri acara <strong>{$event}</strong>.</p>
                    <p style='font-size: 14px;'>Sebagai bentuk apresiasi atas partisipasi Anda, sertifikat kehadiran resmi Anda telah diterbitkan dan terlampir pada email ini.</p>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{$downloadUrl}' target='_blank' style='display: inline-block; background-color: #4f46e5; color: #ffffff; padding: 14px 28px; border-radius: 12px; font-weight: bold; text-decoration: none; font-size: 14px; box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);'>
                            Unduh Sertifikat (PDF) 📥
                        </a>
                    </div>
                    
                    <hr style='border: 0; border-top: 1px solid #f1f5f9; margin: 25px 0;' />
                    <p style='color: #94a3b8; font-size: 11px; text-align: center; margin-bottom: 0;'>
                        AmikomEventHub &bull; support@eventtiket.com &bull; Sleman, Yogyakarta
                    </p>
                </div>
            </div>
        </div>
        ";
    }
}
