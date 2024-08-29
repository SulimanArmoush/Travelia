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
                'address' => 'Syria',
                'wallet' => '0',
                'photo' => 'images/fX60hIcKiXN3pLZkUvBdcNsV5rbiewz3fx6zjYnk',
                'passport' => 'images/P0zbNwXNRMrVbXbI70sG23RwPFh1wc7RKK3nM5V2',
                'role_id' => '1',
                'deviceToken' => 'fAWhGitITie_MNPmL50b3k:APA91bGC1RdHn8n1z9v-6pzuK1SO18Jl7qJVMGh5pX7_UgYkZsYWqifWl4d04v6_4NefWaG8BAGPnrWWZkpNbi0JBbYSBrRwsosCKHURcoigxwtXiN6wwCrEG7VYiCOtbUanjXCnDF91',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
