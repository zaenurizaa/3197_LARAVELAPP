<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    /**
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $partners = Partner::when($search, function ($query, $search) {
            return $query->where('name', 'LIKE', '%' . $search . '%');
        })->get();

        return view('admin.partners.index', compact('partners'));
    }

    /**
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
     */
    public function edit(int $id)
    {
        $partner = Partner::findOrFail($id);
        return view('admin.partners.edit', compact('partner'));
    }

    /**
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'logo_url' => 'nullable|string'
        ]);

        $partner = Partner::findOrFail($id);
        $partner->update($request->only('name', 'logo_url'));

return redirect()->route('admin.partners.index')->with('success', 'Data Partner berhasil diperbarui!');
    }
    /**
     */
    public function destroy(int $id)
    {
        $partner = Partner::findOrFail($id);
        $partner->delete();

        return redirect()->back()->with('success', 'Partner berhasil dihapus dari sistem!');
    }
}