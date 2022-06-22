<?php

namespace Database\Seeders;

use App\Models\Trip;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TripsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate existing records
        Trip::truncate();

        $faker = \Faker\Factory::create();

        // Create dummy content
        for ($i = 0; $i < 10; $i++){
            Trip::create([
                'slug' => $faker->slug,
                'title' => $faker->sentence,
                'description' => $faker->sentence,
                'start_date' => $faker->date,
                'end_date' => $faker->date,
                'location' => $faker->sentence,
                'price' => $faker->randomNumber(2),
            ]);
        }

    }
}
