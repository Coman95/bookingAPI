<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // run: php artisan db:seed
        // or php artisan migrate:fresh --seed ( rebuilding database )
        $this->call(UsersTableSeeder::class);
        $this->call(TripsTableSeeder::class);
    }
}
