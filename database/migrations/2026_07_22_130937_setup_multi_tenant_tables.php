<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Hubungkan User ke Tenant & Sesuaikan Role
        Schema::table('users', function (Blueprint $table) {
            // Hapus foreign key & kolom organizer_id jika ada
            if (Schema::hasColumn('users', 'organizer_id')) {
                $table->dropForeign(['organizer_id']);
                $table->dropColumn('organizer_id');
            }

            // Tambah tenant_id jika belum ada
            if (!Schema::hasColumn('users', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
            }

            // Tambah role HANYA JIKA belum ada
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['superadmin', 'tenant_admin', 'customer'])->default('customer')->after('email');
            }
        });

        // 2. Pasang tenant_id di Events
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'organizer_id')) {
                $table->dropForeign(['organizer_id']);
                $table->dropColumn('organizer_id');
            }
            if (!Schema::hasColumn('events', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
            }
        });

        // 3. Pasang tenant_id di Transactions
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'organizer_id')) {
                $table->dropForeign(['organizer_id']);
                $table->dropColumn('organizer_id');
            }
            if (!Schema::hasColumn('transactions', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->onDelete('cascade');
            }
        });

        // 4. Hapus tabel organizers jika masih ada
        Schema::dropIfExists('organizers');
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'tenant_id')) {
                $table->dropForeign(['tenant_id']);
                $table->dropColumn('tenant_id');
            }
        });

        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'tenant_id')) {
                $table->dropForeign(['tenant_id']);
                $table->dropColumn('tenant_id');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'tenant_id')) {
                $table->dropForeign(['tenant_id']);
                $table->dropColumn('tenant_id');
            }
        });
    }
};