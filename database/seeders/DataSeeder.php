<?php

namespace Database\Seeders;

use App\Models\Dates\Day;
use App\Models\Dates\Hour;
use App\Models\Dates\Month;
use App\Models\Permissions\Role;
use App\Models\Permissions\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DataSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Admin'],
            ['name' => 'Trip_Organizer'],
            ['name' => 'Hotel_Manager'],
            ['name' => 'Restaurant_Manager'],
            ['name' => 'Transport_Manager'],
            ['name' => 'tourist'],
        ];

        $the_Admin = [
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
                'created_at'=>now(),
                'updated_at'=>now(),
            ]
        ];
/*
        $months = [
            ['name' => 'JANUARY'],
            ['name' => 'FEBRUARY'],
            ['name' => 'MARCH'],
            ['name' => 'APRIL'],
            ['name' => 'MAY'],
            ['name' => 'JUNE'],
            ['name' => 'JULY'],
            ['name' => 'AUGUST'],
            ['name' => 'SEPTEMBER'],
            ['name' => 'OCTOBER'],
            ['name' => 'NOVEMBER'],
            ['name' => 'DECEMBER'],
        ];

        $days = [
            ['name' => 'Saturday'],
            ['name' => 'Sunday'],
            ['name' => 'Monday'],
            ['name' => 'Tuesday'],
            ['name' => 'Wednesday'],
            ['name' => 'Thursday'],
            ['name' => 'Friday'],
        ];

        $hours = [
            ['name' => '01'],
            ['name' => '02'],
            ['name' => '03'],
            ['name' => '04'],
            ['name' => '05'],
            ['name' => '06'],
            ['name' => '07'],
            ['name' => '08'],
            ['name' => '09'],
            ['name' => '10'],
            ['name' => '11'],
            ['name' => '12'],
            ['name' => '13'],
            ['name' => '14'],
            ['name' => '15'],
            ['name' => '16'],
            ['name' => '17'],
            ['name' => '18'],
            ['name' => '19'],
            ['name' => '20'],
            ['name' => '21'],
            ['name' => '22'],
            ['name' => '23'],
            ['name' => '00'],
        ];*/

        Role::insert($roles);
        User::insert($the_Admin);
       /* Month::insert($months);
        Day::insert($days);
        Hour::insert($hours);*/

    }
}
