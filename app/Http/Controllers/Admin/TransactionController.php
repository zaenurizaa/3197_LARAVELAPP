<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::guard('admin')->user() ?? \Illuminate\Support\Facades\Auth::guard('organizer')->user() ?? auth()->user();
        $isSuperAdmin = $user ? $user->isSuperAdmin() : false;

        if ($isSuperAdmin) {
            // Superadmin melihat seluruh data transaksi
            $transactions = Transaction::with('event')->latest()->paginate(20);
        } else {
            // Organizer hanya melihat transaksi miliknya sendiri
            $transactions = Transaction::with('event')
                ->where('tenant_id', $user->tenant_id)
                ->latest()
                ->paginate(20);
        }

        return view('admin.transactions.index', compact('transactions'));
    }

    /**
     * Menampilkan Halaman Edit Transaksi
     */
    public function edit(Transaction $transaction)
    {
        $user = \Illuminate\Support\Facades\Auth::guard('admin')->user() ?? \Illuminate\Support\Facades\Auth::guard('organizer')->user() ?? auth()->user();
        if (!$user->isSuperAdmin() && $transaction->tenant_id !== $user->tenant_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengedit transaksi ini.');
        }

        // Memuat relasi event agar bisa ditampilkan detailnya di halaman edit jika butuh
        $transaction->load('event');
        return view('admin.transactions.edit', compact('transaction'));
    }

    /**
     * Memproses Update Status Transaksi dari Halaman Edit
     */
    public function update(Request $request, Transaction $transaction)
    {
        $user = \Illuminate\Support\Facades\Auth::guard('admin')->user() ?? \Illuminate\Support\Facades\Auth::guard('organizer')->user() ?? auth()->user();
        if (!$user->isSuperAdmin() && $transaction->tenant_id !== $user->tenant_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah transaksi ini.');
        }

        $request->validate([
            'status' => 'required|in:Pending,Success,Cancelled'
        ]);

        $transaction->update([
            'status' => $request->status
        ]);

        // Redirect ke menu index sesuai guard login
        $redirectRoute = $user->isSuperAdmin() ? 'admin.transactions.index' : 'organizer.transactions.index';
        return redirect()->route($redirectRoute)
            ->with('success', 'Status transaksi ' . $transaction->order_id . ' berhasil diperbarui!');
    }

    public function destroy(Transaction $transaction)
    {
        $user = \Illuminate\Support\Facades\Auth::guard('admin')->user() ?? \Illuminate\Support\Facades\Auth::guard('organizer')->user() ?? auth()->user();
        if (!$user->isSuperAdmin() && $transaction->tenant_id !== $user->tenant_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus transaksi ini.');
        }

        $orderId = $transaction->order_id;
        $transaction->delete();

        return back()->with('success', 'Transaksi dengan Order ID ' . $orderId . ' berhasil dihapus.');
    }
}