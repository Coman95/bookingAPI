<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate existing records
        User::truncate();

        $faker = \Faker\Factory::create();

        // hash password before the loop, or else our seeder will be too slow.
        $password = Hash::make('cosmin');

        // Create dummy content
        User::create([
            'first_name' => 'Administrator',
            'last_name' => 'Administrator',
            'email' => 'admin@test.com',
            'password' => $password,
        ]);

        for ($i = 0; $i < 10; $i++) {
            User::create([
                'first_name' => $faker->name,
                'last_name' => $faker->name,
                'email' => $faker->email,
                'password' => $password,
            ]);
        }
    }
}
