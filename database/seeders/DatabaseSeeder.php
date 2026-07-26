<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Category;
use App\Models\Event;

use App\Models\Coupon;
use App\Models\EventTier;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Kupon Diskon (Pemasaran Fleksibel)
        Coupon::firstOrCreate(['code' => 'MAHASISWA50'], [
            'discount_value' => 50,
            'type'           => 'percent',
            'quota'          => 100,
        ]);

        Coupon::firstOrCreate(['code' => 'DISKON20K'], [
            'discount_value' => 20000,
            'type'           => 'fixed',
            'quota'          => 50,
        ]);

        Coupon::firstOrCreate(['code' => 'PROMO10'], [
            'discount_value' => 10,
            'type'           => 'percent',
            'quota'          => 200,
        ]);

        // 2. Buat Tenant Default
        $tenant = Tenant::firstOrCreate(['slug' => 'amikom-event-organizer'], [
            'name'                => 'Amikom Event Organizer',
            'status'              => 'verified',
            'bank_name'           => 'Bank Mandiri',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Amikom EO',
        ]);

        // 3. Buat Akun Superadmin
        User::firstOrCreate(['email' => 'admin@amikom.ac.id'], [
            'name'     => 'Admin Amikom',
            'password' => bcrypt('password'),
            'role'     => 'superadmin',
        ]);

        // 4. Buat Akun Organizer
        User::firstOrCreate(['email' => 'organizer@amikom.ac.id'], [
            'name'      => 'Organizer Amikom',
            'password'  => bcrypt('password'),
            'role'      => 'organizer',
            'tenant_id' => $tenant->id,
        ]);

        // 5. Buat Kategori Event
        $category = Category::firstOrCreate(['slug' => 'seminar-it'], [
            'name' => 'Seminar IT',
        ]);
        $category2 = Category::firstOrCreate([
            'name' => 'Entertaiment',
            'slug' => 'entertaiment',
        ]);
        $category3 = Category::firstOrCreate([
            'name' => 'Hackaton',
            'slug' => 'hackaton',
        ]);
        $category4 = Category::firstOrCreate([
            'name' => 'Workshop',
            'slug' => 'workshop',
        ]);
        $category5 = Category::firstOrCreate([
            'name' => 'UIUX',
            'slug' => 'uiux',
        ]);

        // 6. Buat Event dengan Dynamic Tiered Pricing
        $event1 = Event::firstOrCreate(['title' => 'Jazz Night 2025'], [
            'tenant_id'   => $tenant->id,
            'category_id' => $category2->id,
            'description' => 'Nikmati malam yang indah dengan alunan musik jazz yang merdu.',
            'date'        => '2026-05-10 19:00:00',
            'location'    => 'Amikom Baru',
            'price'       => 50000,
            'stock'       => 100,
            'poster_path' => 'posters/event-1.png',
        ]);

        // Tiering Penjualan Bertahap (Early Bird, Presale 1, Regular)
        EventTier::firstOrCreate(['event_id' => $event1->id, 'name' => 'Early Bird'], [
            'price'      => 25000,
            'start_date' => now()->subDays(5),
            'end_date'   => now()->addDays(5), // Aktif saat ini
            'stock'      => 30,
        ]);
        EventTier::firstOrCreate(['event_id' => $event1->id, 'name' => 'Presale 1'], [
            'price'      => 35000,
            'start_date' => now()->addDays(6),
            'end_date'   => now()->addDays(15),
            'stock'      => 40,
        ]);
        EventTier::firstOrCreate(['event_id' => $event1->id, 'name' => 'Regular'], [
            'price'      => 50000,
            'start_date' => now()->addDays(16),
            'end_date'   => now()->addDays(30),
            'stock'      => 50,
        ]);

        $event2 = Event::firstOrCreate(['title' => 'Hackaton - Unleash Your Inner Developer'], [
            'tenant_id'   => $tenant->id,
            'category_id' => $category->id,
            'description' => 'Ayo asah skill coding kamu dan ciptakan solusi inovatif untuk tantangan masa depan!',
            'date'        => '2026-05-05 10:00:00',
            'location'    => 'Inkubator Amikom',
            'price'       => 50000,
            'stock'       => 100,
            'poster_path' => 'posters/event-2.png',
        ]);

        EventTier::firstOrCreate(['event_id' => $event2->id, 'name' => 'Presale 1'], [
            'price'      => 30000,
            'start_date' => now()->subDays(2),
            'end_date'   => now()->addDays(3),
            'stock'      => 50,
        ]);

        Event::create([
            'tenant_id'   => $tenant->id,
            'category_id' => $category->id,
            'title'       => 'AI & FUTURE TECH SUMMIT 2026',
            'description' => 'Jelajahi tren terkini dalam kecerdasan buatan dan teknologi masa depan bersama para ahli di bidangnya.',
            'date'        => '2026-05-01 13:00:00',
            'location'    => 'Cinema Unit 6',
            'price'       => 50000,
            'stock'       => 100,
            'poster_path' => 'posters/event-3.png',
        ]);

        Event::create([
            'tenant_id'   => $tenant->id,
            'category_id' => $category3->id,
            'title'       => 'Hackaton Himasi - Unleash Your Inner Developer',
            'description' => 'Ayo asah skill coding kamu dan ciptakan solusi inovatif untuk tantangan masa depan!',
            'date'        => '2026-05-05 10:00:00',
            'location'    => 'Bpc Amikom',
            'price'       => 50000,
            'stock'       => 100,
            'poster_path' => 'posters/event-4.png',
        ]);

        Event::create([
            'tenant_id'   => $tenant->id,
            'category_id' => $category5->id,
            'title'       => 'Lomba UIUX',
            'description' => 'Ayo asah skill design kamu inovatif untuk tantangan masa depan!',
            'date'        => '2026-05-05 10:00:00',
            'location'    => 'Citra 2 amikom',
            'price'       => 50000,
            'stock'       => 100,
            'poster_path' => 'posters/event-5.png',
        ]);

        Event::create([
            'tenant_id'   => $tenant->id,
            'category_id' => $category5->id,
            'title'       => 'Lomba UIUX Trenggalek',
            'description' => 'Ayo asah skill design kamu inovatif untuk tantangan masa depan!',
            'date'        => '2026-05-05 10:00:00',
            'location'    => 'Trenggalek',
            'price'       => 50000,
            'stock'       => 100,
            'poster_path' => 'posters/event-6.png',
        ]);

        Event::create([
            'tenant_id'   => $tenant->id,
            'category_id' => $category5->id,
            'title'       => 'Workshop Eksternal',
            'description' => 'Ayo asah skill coding kamu inovatif untuk tantangan masa depan!',
            'date'        => '2026-05-05 10:00:00',
            'location'    => 'Gedung BSC lt 3',
            'price'       => 50000,
            'stock'       => 100,
            'poster_path' => 'posters/event-7.png',
        ]);

        // 7. Jalankan PartnerSeeder
        $this->call([
            PartnerSeeder::class,
        ]);
    }
}