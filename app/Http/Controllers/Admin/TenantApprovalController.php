<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TenantApprovalController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $query = Tenant::with(['users'])
            ->withCount(['events']);

        if (!empty($search)) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        }

        // Ambil semua tenant (verified, pending, rejected) untuk ditampilkan di grid view Manajemen Organizer
        $tenants = $query->latest()->get();

        return view('admin.tenants.index', compact('tenants'));
    }

    /**
     * ACC / Setujui Tenant
     */
    public function approve(Tenant $tenant): RedirectResponse
    {
        $tenant->update(['status' => 'verified']);

        return redirect()->route('admin.tenants.index')
            ->with('success', "Tenant '{$tenant->name}' berhasil disetujui!");
    }

    /**
     * Tolak Tenant
     */
    public function reject(Tenant $tenant): RedirectResponse
    {
        $tenant->update(['status' => 'rejected']);

        return redirect()->route('admin.tenants.index')
            ->with('info', "Pendaftaran tenant '{$tenant->name}' telah ditolak.");
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
        $tenantName = $tenant->name;

        // 🔥 Hapus secara kaskade semua user organizer yang berelasi dengan tenant ini agar bersih dari database
        $tenant->users()->delete();
        
        // Hapus data tenant
        $tenant->delete();

        return redirect()->route('admin.tenants.index')
            ->with('success', "Kemitraan dengan Tenant '{$tenantName}' telah diakhiri dan seluruh datanya dihapus dari database.");
    }
}