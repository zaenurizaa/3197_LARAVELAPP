<?php

namespace App\Http\Controllers\Admin; 

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('admin')->user() ?? Auth::guard('organizer')->user() ?? auth()->user();
        $isSuperAdmin = $user ? $user->isSuperAdmin() : false;

        if ($isSuperAdmin) {
            // 1. Stat Utama Ringkasan Superadmin
            $totalPendapatan = Transaction::whereIn('status', ['Success', 'success', 'settlement', 'used'])->sum('total_price');
            $tiketTerjual    = Transaction::whereIn('status', ['Success', 'success', 'settlement', 'used'])->count();
            $eventAktif      = Event::count();
            $pesananPending  = Transaction::where('status', 'Pending')->count();
            
            // 4 Tambahan Info Superadmin DINAMIS:
            // Sesuai DB: total tenant = 1 (Himaski), total partner = 0, total kategori = 5, total pengguna = 3 (Admin Amikom, Zaky, zaenurizaaa)
            $totalOrganizer  = \App\Models\Tenant::count(); 
            $partnerTerdaftar = \App\Models\Partner::count();
            $kategoriEvent   = \App\Models\Category::count();
            $totalPengguna   = User::count();

            // 2. Ambil 5 Transaksi Terbaru
            $latestTransactions = Transaction::with('event')->latest()->take(5)->get();

            // 3. Data Pertumbuhan Pengguna Per Bulan (Tahun Ini)
            $userGrowth = User::select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('COUNT(*) as total')
                )
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();

            // 4. Data Pertumbuhan Penyelenggaraan Event Per Bulan (Tahun Ini)
            $eventGrowth = Event::select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('COUNT(*) as total')
                )
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();
        } else {
            // Organizer - filter data berdasarkan tenant_id milik organizer saja
            $tenantId = $user->tenant_id;

            $totalPendapatan = Transaction::where('tenant_id', $tenantId)
                ->whereIn('status', ['Success', 'success', 'settlement'])
                ->sum('total_price');
            $tiketTerjual    = Transaction::where('tenant_id', $tenantId)
                ->whereIn('status', ['Success', 'success', 'settlement'])
                ->count();
            $eventAktif      = Event::where('tenant_id', $tenantId)->count();
            $pesananPending  = Transaction::where('tenant_id', $tenantId)->where('status', 'Pending')->count();

            // Sisa variabel kosong untuk layout organizer agar tidak error
            $totalOrganizer  = 0;
            $partnerTerdaftar = 0;
            $kategoriEvent   = 0;
            $totalPengguna   = 0;

            $latestTransactions = Transaction::with('event')
                ->where('tenant_id', $tenantId)
                ->latest()
                ->take(5)
                ->get();

            $userGrowth = [];
            $eventGrowth = Event::select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('COUNT(*) as total')
                )
                ->where('tenant_id', $tenantId)
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();
        }

        // Mapping 12 Bulan (Jan - Des)
        $months    = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $userData  = [];
        $eventData = [];

        for ($i = 1; $i <= 12; $i++) {
            $userData[]  = $userGrowth[$i] ?? 0;
            $eventData[] = $eventGrowth[$i] ?? 0;
        }

        return view('admin.dashboard', compact(
            'totalPendapatan', 
            'tiketTerjual', 
            'eventAktif', 
            'pesananPending',
            'totalOrganizer',
            'partnerTerdaftar',
            'kategoriEvent',
            'totalPengguna',
            'latestTransactions',
            'months',
            'userData',
            'eventData'
        ));
    }
}