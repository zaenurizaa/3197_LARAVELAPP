<?php

namespace App\Http\Controllers\Admin; 

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Stat Utama Ringkasan
        $totalPendapatan = Transaction::whereIn('status', ['Success', 'success', 'settlement'])->sum('total_price');
        $tiketTerjual    = Transaction::whereIn('status', ['Success', 'success', 'settlement'])->count();
        $eventAktif      = Event::count();
        $pesananPending  = Transaction::where('status', 'Pending')->count();

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
            'latestTransactions',
            'months',
            'userData',
            'eventData'
        ));
    }
}