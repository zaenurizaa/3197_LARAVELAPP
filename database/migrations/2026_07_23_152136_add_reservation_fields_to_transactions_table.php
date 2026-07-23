<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Menandai apakah tiket ini sedang di-reserve
            $table->timestamp('reserved_until')->nullable()->after('status');
            $table->boolean('is_reserved')->default(false)->after('reserved_until');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['reserved_until', 'is_reserved']);
        });
    }
};