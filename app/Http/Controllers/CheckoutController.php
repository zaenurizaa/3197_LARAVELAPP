<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Menampilkan Form Halaman Pemesanan (Guest Checkout)
     */
    public function create(Event $event)
    {
        // Mengambil daftar kategori untuk keperluan menu footer/navbar layout
        $categories = Category::all();

        return view('checkout.create', compact('event', 'categories'));
    }

    /**
     * Memproses Validasi dan Menyimpan Data Transaksi Baru ke Database
     */
    public function store(Request $request, Event $event)
    {
        // 1. Validasi Input Kredensial Pelanggan
        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
        ]);

        // 2. Cegah Check-out Jika Tiket Sudah Habis
        if ($event->stock <= 0) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Mohon maaf, tiket untuk acara ini sudah habis.'], 422);
            }
            return back()->with('error', 'Mohon maaf, tiket untuk acara ini sudah habis.');
        }

        // 3. Generate Kode TRX / Order ID Unik (Sesuai Teori 10.3.2)
        $orderId = 'TRX-' . time() . '-' . strtoupper(Str::random(5));
        $totalPrice = $event->price + 5000; // Harga tiket + Biaya Layanan Rp 5.000

        // 4. Merekam Log Transaksi Baru ke Database dengan Status Awal 'Pending'
        Transaction::create([
            'event_id'       => $event->id,
            'order_id'       => $orderId,
            'customer_name'  => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'total_price'    => $totalPrice,
            'status'         => 'Pending', 
        ]);

        // 5. Kurangi stok tiket event sebanyak 1 secara otomatis
        $event->decrement('stock');

        // 6. Jika dikirim lewat AJAX (Fetch), kembalikan data JSON berupa ketikan nama dan order_id asli
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'order_id' => $orderId,
                'customer_name' => $request->customer_name
            ]);
        }

        // Fallback jika diakses tanpa AJAX
        return redirect('/')->with('success', 'Pesanan Anda dengan Order ID ' . $orderId . ' berhasil direkam!'); 
    }
}