<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Organizer;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Menampilkan daftar event milik masing-masing panitia (Multi-Tenant)
     */
    public function index(): View|RedirectResponse
    {
        $userId = Auth::id(); // Menggunakan Facade Auth agar bebas dari error Intelephense

        $organizer = Organizer::where('user_id', $userId)->first();

        if (!$organizer) {
            return redirect()->route('home')->with('error', 'Akun Anda belum terdaftar sebagai kepanitiaan resmi.');
        }

        $events = Event::where('organizer_id', $organizer->id)->get();

        return view('admin.events.index', compact('events'));
    }

    /**
     * Form tambah event baru
     */
    public function create(): View
    {
        return view('admin.events.create');
    }

    /**
     * Menyimpan data event baru ke dalam database
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:1',
        ]);

        $userId = Auth::id();

        /** @var \App\Models\Organizer $organizer */
        $organizer = Organizer::where('user_id', $userId)->firstOrFail();

        Event::create([
            'organizer_id' => $organizer->id,
            'title'        => $request->title,
            'price'        => $request->price,
            'stock'        => $request->stock,
            'is_free'      => $request->price == 0,
        ]);

        return redirect()->route('events.index')->with('success', 'Event baru berhasil diterbitkan!');
    }

    /**
     * Menampilkan halaman detail informasi event ke publik
     */
    public function show(string $id): View
    {
        $event = Event::findOrFail($id);
        $categories = Category::all();

        return view('layout.event-detail', compact('event', 'categories'));
    }

    /**
     * Menampilkan Tiket Saya OR Kartu E-Ticket Fisik
     */
    public function ticket(Request $request): View|RedirectResponse
    {
        $categories = Category::all();
        $orderId = $request->query('order_id');
        $transaction = null;
        $transactions = collect();

        // 1. Jika User Meminta Order ID Tertentu (Cari / Klik Cetak Tiket)
        if ($orderId) {
            $transaction = Transaction::with('event')
                ->where('order_id', $orderId)
                ->first();

            if (!$transaction) {
                return redirect()->route('ticket')->with('error', 'Nomor pesanan (Order ID) tidak ditemukan.');
            }
        } 
        // 2. Jika Tidak Ada order_id, Ambil Semua Transaksi User yang Sedang Login
        elseif (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $transactions = Transaction::with('event')
                ->where('customer_email', $user->email)
                ->latest()
                ->get();
        }

        return view('layout.ticket', compact('transaction', 'transactions', 'categories'));
    }
}