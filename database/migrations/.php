<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // Nama HIMA / UKM / Kepanitiaan
            $table->string('slug')->unique();                // URL Friendly (contoh: hima-if)
            $table->string('logo')->nullable();
            
            // Status Persetujuan Superadmin
            $table->enum('status', ['pending', 'verified', 'rejected', 'suspended'])->default('pending');
            
            // Rekening Pencairan Dana (Payout)
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_holder')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};