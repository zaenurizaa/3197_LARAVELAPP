<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CertificateController extends Controller
{
    /**
     * Download / stream PDF certificate by ID
     */
    public function download(string $ticket_code): StreamedResponse
    {
        $certificate = Certificate::where('ticket_code', $ticket_code)->firstOrFail();
        abort_if(!$certificate->file_path || !Storage::disk('public')->exists($certificate->file_path), 404, 'Sertifikat tidak ditemukan.');

        $filename = 'sertifikat_' . $certificate->ticket_code . '.pdf';

        return Storage::disk('public')->download($certificate->file_path, $filename, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}
