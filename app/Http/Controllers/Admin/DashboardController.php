<?php
namespace App\Http\Controllers\Admin; // Harus ada \Admin-nya!

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Memanggil file: resources/views/admin/dashboard.blade.php
        return view('admin.dashboard');
    }

    public function transactions()
    {
        // Memanggil file: resources/views/admin/transactions.blade.php
        return view('admin.transactions');
    }
}