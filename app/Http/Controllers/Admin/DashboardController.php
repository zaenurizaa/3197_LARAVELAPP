<?php

namespace App\Http\Controllers\Admin; 

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Event;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung ringkasan data secara dinamis dari database
        $totalPendapatan = Transaction::whereIn('status', ['Success', 'success', 'settlement'])->sum('total_price');
        $tiketTerjual = Transaction::whereIn('status', ['Success', 'success', 'settlement'])->count();
        $eventAktif = Event::count();
        $pesananPending = Transaction::where('status', 'Pending')->count();

        // Ambil 5 transaksi terbaru untuk tabel ringkasan di bawah kartu dashboard
        $latestTransactions = Transaction::with('event')->latest()->take(5)->get();
        
        return view('admin.dashboard', compact('totalPendapatan', 'tiketTerjual', 'eventAktif', 'pesananPending', 'latestTransactions'));
    }
}