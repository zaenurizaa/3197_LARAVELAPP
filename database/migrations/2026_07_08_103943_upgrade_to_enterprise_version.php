<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Tabel Organizers (Fitur Multi-Tenant)
        Schema::create('organizers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('phone');
            $table->timestamps();
        });

        // 2. Modifikasi Tabel Events (Multi-tenant & Free Bypass)
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('organizer_id')->nullable()->constrained('organizers')->onDelete('cascade');
            $table->boolean('is_free')->default(false);
        });

        // 3. Tabel Reservasi Tiket (Anti-Race Condition)
        Schema::create('ticket_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('session_id');
            $table->timestamp('expires_at');
            $table->timestamps();
        });

        // 4. Tabel Kupon (Dynamic Pricing)
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->decimal('discount_value', 10, 2);
            $table->enum('type', ['percent', 'fixed']);
            $table->integer('quota');
            $table->timestamps();
        });

        // 5. Modifikasi Tabel Transactions (Scanner & Diskon)
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('customer_whatsapp')->nullable(); // Untuk notif Fonnte
            $table->enum('attendance_status', ['pending', 'used'])->default('pending');
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->onDelete('set null');
        });

        // 6. Tabel Ulasan (Rating)
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { /* ... letakkan fungsi dropIfExists disini ... */ }
};