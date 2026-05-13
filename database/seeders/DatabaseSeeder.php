<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
public function run(): void
{
// 1. Akun Admin Utama
\App\Models\User::create([
'name' => 'Admin Amikom',
'email' => 'admin@amikom.ac.id',
'password' => bcrypt('password'),
'role' => 'admin',
]);
// 2. Insert Kategori Event
$category = \App\Models\Category::create([
    'name' => 'Seminar IT',
    'slug' => 'seminar-it',
    ]);
    $category2 = \App\Models\Category::firstOrCreate([
        'name' => 'Entertaiment',
        'slug' => 'entertaiment',
        ]);
        $category3 = \App\Models\Category::firstOrCreate([
            'name' => 'Hackaton',
            'slug' => 'hackaton',
            ]);
            $category4 = \App\Models\Category::firstOrCreate([
                'name' => 'Workshop',
                'slug' => 'workshop',
                ]);
                $category5 = \App\Models\Category::firstOrCreate([
                    'name' => 'UIUX',
                    'slug' => 'uiux',
                    ]);

        
        // 3. Insert Sampel Events
        \App\Models\Event::create([
        'category_id' => $category2->id,
        'title' => 'Jazz Night 2025',
        'description' => 'Nikmati malam yang indah dengan alunan musik jazz
        
        yang merdu.',
        
        'date' => '2026-05-10 19:00:00',
        'location' => 'Amikom Baru',
        'price' => 50000,
        'stock' => 100,
        'poster_path' => 'posters/event-1.png',
        ]);

        \App\Models\Event::create([
            'category_id' => $category->id,
            'title' => 'Hackaton - Unleash Your Inner Developer',
            'description' => 'Ayo asah skill coding kamu dan ciptakan solusi
            
            inovatif untuk tantangan masa depan!',
            'date' => '2026-05-05 10:00:00',
            'location' => 'Inkubator Amikom',
'price' => 50000,
'stock' => 100,
'poster_path' => 'posters/event-2.png',
]);

\App\Models\Event::create([
'category_id' => $category->id,
'title' => 'AI & FUTURE TECH SUMMIT 2026',
'description' => 'Jelajahi tren terkini dalam kecerdasan buatan dan

teknologi masa depan bersama para ahli di bidangnya.',

'date' => '2026-05-01 13:00:00',
'location' => 'Cinema Unit 6',
'price' => 50000,
'stock' => 100,
'poster_path' => 'posters/event-3.png',
]);
\App\Models\Event::create([
    'category_id' => $category3->id,
    'title' => 'Hackaton Himasi - Unleash Your Inner Developer',
    'description' => 'Ayo asah skill coding kamu dan ciptakan solusi
    
    inovatif untuk tantangan masa depan!',
    'date' => '2026-05-05 10:00:00',
    'location' => 'Bpc Amikom',
'price' => 50000,
'stock' => 100,
'poster_path' => 'posters/event-4.png',
]);
\App\Models\Event::create([
    'category_id' => $category5->id,
    'title' => 'Lomba UIUX',
    'description' => 'Ayo asah skill design kamu
    
    inovatif untuk tantangan masa depan!',
    'date' => '2026-05-05 10:00:00',
    'location' => 'Citra 2 amikom',
'price' => 50000,
'stock' => 100,
'poster_path' => 'posters/event-5.png',
]);
\App\Models\Event::create([
    'category_id' => $category5->id,
    'title' => 'Lomba UIUX Trenggalek',
    'description' => 'Ayo asah skill design kamu
    
    inovatif untuk tantangan masa depan!',
    'date' => '2026-05-05 10:00:00',
    'location' => 'Trenggalek',
'price' => 50000,
'stock' => 100,
'poster_path' => 'posters/event-6.png',
]);
\App\Models\Event::create([
    'category_id' => $category5->id,
    'title' => 'Workshop Eksternal',
    'description' => 'Ayo asah skill coding kamu
    
    inovatif untuk tantangan masa depan!',
    'date' => '2026-05-05 10:00:00',
    'location' => 'Gedung BSC lt 3',
'price' => 50000,
'stock' => 100,
'poster_path' => 'posters/event-7.png',
]);
}
}