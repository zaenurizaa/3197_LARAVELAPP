<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // 💡 Foreign Key ke tabel Tenants untuk memisahkan pendapatan
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();

            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('order_id')->unique(); 
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->integer('total_price');
            $table->string('status')->default('Pending');
            $table->string('snap_token')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};