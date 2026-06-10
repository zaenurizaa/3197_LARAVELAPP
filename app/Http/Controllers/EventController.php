<?php

namespace App\Http\Controllers;

use App\Models\Event;     // Mengimpor Model Event
use App\Models\Category;  // Mengimpor Model Category untuk navigasi/footer (Sesuai Modul 9.4.6)
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Menampilkan Detail Event secara Dinamis
     * DISESUAIKAN 100% dengan Modul 9.4.6 & Nama Berkas: event-detail.blade.php
     */
    public function show(Event $event)
    {
        // Me-load relasi category dan user agar tidak lambat saat dipanggil di blade (Eager Loading)
        $event->load(['category', 'user']);

        // Mengambil daftar kategori untuk keperluan menu navigasi/footer sesuai instruksi modul
        $categories = Category::all();

        // Mengarahkan langsung ke resources/views/event-detail.blade.php
        return view('event-detail', compact('categories', 'event'));
    }

    /**
     * Mengarahkan Pengguna ke Halaman Pemesanan (Checkout)
     */
    public function checkout()
    {
        // Mengarahkan ke view transaksi checkout di folder layout (sesuai kode awalmu)
        return view('layout.checkout');
    }

    /**
     * Menampilkan E-Ticket setelah Pembayaran Sukses Dikonfirmasi
     */
    public function ticket()
    {
        // Mengarahkan ke halaman e-ticket pengguna di folder layout (sesuai kode awalmu)
        return view('layout.ticket');
    }
}