<?php

namespace Database\Seeders;

use \App\Models\Permissions\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DataSeeder::class,
            LocationSeeder::class,
            AreaSeeder::class,
        ]);
    }
}
