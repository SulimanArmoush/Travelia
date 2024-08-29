<?php

namespace Database\Seeders;

use App\Models\Permissions\PermissionRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PermissionRole::insert([
            'permission_id' => 1,
            'role_id' => 1,
        ]);
    }
}
