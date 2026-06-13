<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('event')->latest()->paginate(20);
        return view('admin.transactions.index', compact('transactions'));
    }

    /**
     * Menampilkan Halaman Edit Transaksi
     */
    public function edit(Transaction $transaction)
    {
        // Memuat relasi event agar bisa ditampilkan detailnya di halaman edit jika butuh
        $transaction->load('event');
        return view('admin.transactions.edit', compact('transaction'));
    }

    /**
     * Memproses Update Status Transaksi dari Halaman Edit
     */
    public function update(Request $request, Transaction $transaction)
    {
        $request->validate([
            'status' => 'required|in:Pending,Success,Cancelled'
        ]);

        $transaction->update([
            'status' => $request->status
        ]);

        return redirect()->route('admin.transactions.index')
            ->with('success', 'Status transaksi ' . $transaction->order_id . ' berhasil diperbarui!');
    }

    public function destroy(Transaction $transaction)
    {
        $orderId = $transaction->order_id;
        $transaction->delete();

        return back()->with('success', 'Transaksi dengan Order ID ' . $orderId . ' berhasil dihapus.');
    }
}