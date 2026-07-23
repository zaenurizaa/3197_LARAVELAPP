<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            // 💡 Foreign Key ke tabel Tenants (karena Tenants sudah dibuat duluan di langkah 2)
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();

            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('date');
            $table->string('location');
            $table->integer('price');
            $table->integer('stock');
            $table->string('poster_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};