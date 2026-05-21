<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;
use App\Models\Partner;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Menampilkan Halaman Utama Publik (Homepage)
     * Mengelola filter kategori dinamis dan data partner resmi (Soal 4 UTS)
     */
    public function index(Request $request)
    {
        // 1. Ambil semua jenis kategori untuk tampilan filter tab button 
        $categories = Category::all();

        // 2. Ambil semua data partner untuk pemenuhan Soal 4 UTS
        $partners = Partner::all();

        // 3. Buat kueri dasar untuk mengambil data event aktif (belum kedaluwarsa)
        $query = Event::with('category')
                      ->where('date', '>=', now())
                      ->orderBy('date', 'asc');

        // 4. Saring data jika pengunjung melakukan filter spesifik berdasarkan kategori (?category=slug)
        if ($request->has('category') && $request->category != '') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // 5. Eksekusi query untuk mendapatkan list data event
        $events = $query->get();

        // 6. Kirim seluruh data ke view utama publik
        return view('welcome', compact('events', 'categories', 'partners'));
    }
}