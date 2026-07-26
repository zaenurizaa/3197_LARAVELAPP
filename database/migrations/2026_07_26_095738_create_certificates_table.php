<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code');
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->string('attendee_name');
            $table->string('attendee_email');
            $table->string('event_title');
            $table->date('event_date');
            $table->string('file_path')->nullable();  // storage path for generated PDF
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};

