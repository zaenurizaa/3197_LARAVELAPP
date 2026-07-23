<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        // 1. Otomatis Filter Data saat Mengambil/Membaca dari DB
        static::addGlobalScope('tenant', function (Builder $builder) {
            // Jika user login & rolenya adalah admin UKM/Tenant
            if (auth()->check() && auth()->user()->role === 'tenant_admin') {
                $builder->where('tenant_id', auth()->user()->tenant_id);
            }
        });

        // 2. Otomatis Mengisi tenant_id saat Menyimpan Data Baru
        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }
}