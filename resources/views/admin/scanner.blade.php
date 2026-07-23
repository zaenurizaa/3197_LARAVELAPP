@extends('layout.app') {{-- Sesuaikan dengan nama layout utamamu, misal: layouts.admin atau layouts.main --}}

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <h3 class="mb-3 fw-bold text-primary">Panitia Check-In Scanner</h3>
            <p class="text-muted mb-4">Arahkan kamera perangkat Anda ke QR Code yang ada pada E-Ticket peserta.</p>
            
            <div id="reader" class="mx-auto shadow-sm mb-4" style="max-width: 500px; border: 2px dashed #0d6efd !important; border-radius: 12px; overflow: hidden; background: white;"></div>
            
            <div>
                <a href="{{ route('home') }}" class="btn btn-secondary btn-sm">Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let lastScannedCode = "";

    function onScanSuccess(decodedText) {
        // Mencegah spam request berulang-ulang untuk QR yang sama
        if (decodedText === lastScannedCode) return;
        lastScannedCode = decodedText;

        // Tembak data ke rute web Laravel yang baru diganti (/web-scan/)
        fetch(`/web-scan/${decodedText}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'BERHASIL CHECK-IN!',
                        text: data.msg,
                        confirmButtonColor: '#198754'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'SCAN GAGAL',
                        text: data.msg,
                        confirmButtonColor: '#dc3545'
                    });
                }
                
                // Beri jeda 3 detik sebelum kamera boleh menscan kode baru
                setTimeout(() => { lastScannedCode = ""; }, 3000);
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'System Error',
                    text: 'Gagal terhubung ke server.'
                });
                lastScannedCode = "";
            });
    }

    // Render & Nyalakan Kamera
    let scanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
    scanner.render(onScanSuccess);
</script>
@endsection