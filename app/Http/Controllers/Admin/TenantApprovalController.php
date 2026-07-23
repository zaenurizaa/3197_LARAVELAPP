<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TenantApprovalController extends Controller
{
    /**
     * Tampilkan daftar tenant pending & tenant aktif
     */
    public function index(): View
    {
        $pendingTenants = Tenant::with('users')
            ->where('status', 'pending')
            ->latest()
            ->get();

        $approvedTenants = Tenant::with('users')
            ->where('status', 'verified')
            ->latest()
            ->paginate(10);

        return view('admin.tenants.index', compact('pendingTenants', 'approvedTenants'));
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