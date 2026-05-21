<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    /**
     * Menampilkan daftar partner lengkap dengan fitur pencarian (Soal 3)
     */
    public function index(Request $request)
    {
        // Fitur Pencarian Partner (Soal 3)
        $search = $request->input('search');

        $partners = Partner::when($search, function ($query, $search) {
            return $query->where('name', 'LIKE', '%' . $search . '%');
        })->get();

        return view('admin.partners.index', compact('partners'));
    }

    /**
     * Menyimpan data partner baru ke database (Soal 2)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'logo_url' => 'nullable|string'
        ]);

        Partner::create($request->only('name', 'logo_url'));
        return redirect()->back()->with('success', 'Partner baru sukses didaftarkan!');
    }

    /**
     * Menampilkan halaman edit form partner berdasarkan ID
     */
    public function edit(int $id)
    {
        $partner = Partner::findOrFail($id);
        return view('admin.partners.edit', compact('partner'));
    }

    /**
     * Memperbarui data partner di database berdasarkan ID
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'logo_url' => 'nullable|string'
        ]);

        $partner = Partner::findOrFail($id);
        $partner->update($request->only('name', 'logo_url'));

        // KODE YANG BENAR
        return redirect()->route('partners.index')->with('success', 'Data Partner berhasil diperbarui!');    }

    /**
     * Menghapus data partner dari database berdasarkan ID
     */
    public function destroy(int $id)
    {
        $partner = Partner::findOrFail($id);
        $partner->delete();

        return redirect()->back()->with('success', 'Partner berhasil dihapus dari sistem!');
    }
}