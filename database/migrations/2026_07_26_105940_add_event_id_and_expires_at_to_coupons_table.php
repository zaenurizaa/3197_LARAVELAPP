<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            if (!Schema::hasColumn('coupons', 'event_id')) {
                $table->foreignId('event_id')->nullable()->after('id')->constrained('events')->onDelete('cascade');
            }
            if (!Schema::hasColumn('coupons', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('quota');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropColumn(['event_id', 'expires_at']);
        });
    }
};
