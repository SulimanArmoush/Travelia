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
            LocationSeeder::class,
            AreaSeeder::class,
            PermissionSeeder::class,
            PermissionRoleSeeder::class
        ]);

        User::insert([
            [
                'firstName' => 'Kinan',
                'lastName' => 'Hawri',
                'email' => 'kinan@gmail.com',
                'phone' => '0957965126',
                'password' => bcrypt('12345678'),
                'age' => '23',
                'address' => 'Damas\Midan',
                'wallet' => '0',
                'photo' => 'images/My_photo.jpg',
                'passport' => 'images/passport_image.jpg',
                'role_id' => '1',
                'deviceToken' => 'I am the Admin ولااااك',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
