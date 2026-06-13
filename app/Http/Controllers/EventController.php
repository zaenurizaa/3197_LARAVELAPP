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

        // Mengarahkan ke resources/views/layout/event-detail.blade.php
        return view('layout.event-detail', compact('categories', 'event'));
    }

    /**
     * Menampilkan E-Ticket setelah Pembayaran Sukses Dikonfirmasi
     * FIX & DINAMIS: Menangkap event_id, nama, serta order_id dari URL dan mengirimkannya ke view ticket
     */
    public function ticket(Request $request)
    {
        $eventId = $request->query('event_id');
        
        // Cari event berdasarkan id, jika tidak ketemu ambil data event pertama sebagai fallback
        $event = Event::find($eventId) ?? Event::first();

        // TANGKAP DATA NAMA DARI URL
        $namaPembeli = $request->query('nama', 'Pembeli Amikom');

        // 🔥 TANGKAP DATA ORDER ID DARI URL (Jika tidak ada, beri fallback default lama)
        $orderId = $request->query('order_id', 'TRX-30195');

        // LEMPAR VARIABEL SECARA BERSIH DAN AMAN KE VIEW
        return view('layout.ticket', compact('event', 'namaPembeli', 'orderId'));
    }
}