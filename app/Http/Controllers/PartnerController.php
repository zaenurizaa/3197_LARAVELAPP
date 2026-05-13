<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::all();
        return view('admin.partners.index', compact('partners'));
    }
    public function store(Request $request)
{
    // Validasi sederhana
    $request->validate([
        'name' => 'required',
        'logo_url' => 'required'
    ]);

    // Simpan ke Database
    Partner::create([
        'name' => $request->name,
        'logo_url' => $request->logo_url
    ]);

    // Redirect kembali ke halaman utama dengan pesan sukses
    return redirect()->route('partners.index')->with('success', 'Partner berhasil ditambahkan!');
}
// Menampilkan halaman edit
public function edit($id) {
    $partner = Partner::findOrFail($id);
    return view('admin.partners.edit', compact('partner'));
}

// Memproses perubahan data
public function update(Request $request, $id) {
    $request->validate([
        'name' => 'required',
        'logo_url' => 'required'
    ]);

    $partner = Partner::findOrFail($id);
    $partner->update($request->all());

    return redirect()->route('partners.index')->with('success', 'Partner berhasil diupdate!');
}

// Menghapus data
public function destroy($id) {
    $partner = Partner::findOrFail($id);
    $partner->delete();

    return redirect()->route('partners.index')->with('success', 'Partner berhasil dihapus!');
}
}