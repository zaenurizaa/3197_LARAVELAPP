<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PartnerSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 5) as $index) {
            DB::table('partners')->insert([
                'name' => $faker->company,
                'logo_url' => 'https://placehold.co/200x200?text=' . $faker->word,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}