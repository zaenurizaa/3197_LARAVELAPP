<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkins', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code');
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->string('attendee_name');
            $table->string('attendee_email');
            $table->string('scanner_ip')->nullable();
            $table->string('scanner_user')->nullable();
            $table->timestamp('checked_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkins');
    }
};

