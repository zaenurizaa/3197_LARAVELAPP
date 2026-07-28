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

    /**
     * Akhiri Kerja Sama / Hapus Tenant
     */
    public function destroy(Tenant $tenant): RedirectResponse
    {
        $tenantName = $tenant->name;
        $tenant->delete();

        return redirect()->route('admin.tenants.index')
            ->with('success', "Kerja sama dengan Tenant '{$tenantName}' telah diakhiri.");
    }
}