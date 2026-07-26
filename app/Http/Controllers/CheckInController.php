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
                                  ->where('status', 'success')
                                  ->with('event')
                                  ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => '❌ Tiket tidak ditemukan atau belum dibayar.',
            ], 404);
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

        // Generate dan kirim sertifikat PDF
        try {
            $certificate = $this->certService->generate($transaction);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Certificate generation failed: ' . $e->getMessage());
            $certificate = null;
        }

        return response()->json([
            'success'       => true,
            'message'       => 'Check-in berhasil! Sertifikat dikirim ke email peserta.',
            'customer_name' => $transaction->customer_name,
            'event_title'   => $transaction->event->title,
            'order_id'      => $code,
            'checkin_time'  => $checkIn->checked_at->format('d M Y H:i'),
        ], 200);
    }
}
