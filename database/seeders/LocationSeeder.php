<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('locations')->insert([
            [
                'latitude' => '33.51127671134752',
                'longitude' => '36.30650510062487',
                'address' => 'Omayyad Mosque, Gold Souk, Hameidiyyeh Neighbourhood, Damascus, Ancient City of Damascus Municipality, Damascus Subdistrict, Damascus District, Damascus Governorate, Syria',
                'country' => 'Syria',
                'state' => 'Damascus Governorate',
                'city' => 'Damascus',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'latitude' => '33.51250255',
                'longitude' => '36.2900209679602',
                'address' => 'National Museum in Damascus, Barada Street, Hijaz Neighborhood, Al-Qanawat Municipality, Damascus Subdistrict, Damascus District, Damascus Governorate, Syria',
                'country' => 'Syria',
                'state' => 'Damascus Governorate',
                'city' => 'Al-Qanawat Municipality',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'latitude' => '33.51094027687154',
                'longitude' => '36.303253359161076',
                'address' => 'Al Bimaristan An-Nouri, 8, Mohammad Msalam Abdin Street, Hariqa Neighbourhood, Damascus, Ancient City of Damascus Municipality, Damascus Subdistrict, Damascus District, Damascus Governorate, Syria',
                'country' => 'Syria',
                'state' => 'Damascus Governorate',
                'city' => 'Damascus',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'latitude' => '36.1995189',
                'longitude' => '37.162919201638346',
                'address' => 'Citadel of Aleppo, Al Qalaa Street, Bayadah district, Sahet Bizzeh district, Aleppo, Mount Simeon Subdistrict, Mount Simeon District, Aleppo Governorate, Syria',
                'country' => 'Syria',
                'state' => 'Aleppo Governorate',
                'city' => 'Aleppo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'latitude' => '35.28682445',
                'longitude' => '36.394703528164186',
                'address' => 'The waterwheels of Al-Asharna, 50, Al `Asharinah, Salhab Subdistrict, Al-Suqaylabiyah District, Hama Governorate, Syria',
                'country' => 'Syria',
                'state' => 'Hama Governorate',
                'city' => 'Hama',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'latitude' => '25.2955302',
                'longitude' => '51.53942329967522',
                'address' => 'Museum of Islamic Art, Al Corniche Street, Mina District, Doha Port, Doha, Qatar',
                'country' => 'Qatar',
                'state' => 'Doha',
                'city' => 'Doha',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'latitude' => '34.11983985',
                'longitude' => '35.64647912494691',
                'address' => 'Byblos, Rue Pépé, Jbeil, Jbeil District, Keserwan-Jbeil Governorate, 4504, Lebanon',
                'country' => 'Lebanon',
                'state' => 'Keserwan-Jbeil Governorate',
                'city' => 'Jbeil',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'latitude' => '33.42',
                'longitude' => '35.85',
                'address' => 'Qasr Shbib, Rashayya District, Beqaa Governorate, Lebanon',
                'country' => 'Lebanon',
                'state' => 'Beqaa Governorate',
                'city' => 'Beqaa Governorate',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'latitude' => '29.97078125',
                'longitude' => '31.124233545443854',
                'address' => 'Giza Pyramids, Street 6, Area B, Giza, 12559, Egypt',
                'country' => 'Egypt',
                'state' => 'Giza',
                'city' => 'Giza',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'latitude' => '26.069167',
                'longitude' => '32.081944',
                'address' => 'Al Karnak, New Valley, Egypt',
                'country' => 'Egypt',
                'state' => 'New Valley',
                'city' => 'New Valley',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'latitude' => '30.05282363718765',
                'longitude' => '31.22880869658273',
                'address' => 'Imperial Boat, Al Moseqare Mohamed Abd Al Wahab Street, Mohamed Mazhar, Al-Sabtiyya, Cairo, 12344, Egypt',
                'country' => 'Egypt',
                'state' => 'Cairo',
                'city' => 'Cairo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'latitude' => '30.3497724',
                'longitude' => '35.37648463002928',
                'address' => 'Petra, Wadi Araba Sub-District, Aqaba Qasabah District, Aqaba, Jordan',
                'country' => 'Jordan',
                'state' => 'Aqaba',
                'city' => 'Wadi Araba Sub-District',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'latitude' => '29.31414680857457',
                'longitude' => '35.432158723803695',
                'address' => 'Aqaba Sub-District, Aqaba Qasabah District, Aqaba, 77110, Jordan',
                'country' => 'Jordan',
                'state' => 'Aqaba',
                'city' => 'Aqaba Sub-District',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'latitude' => '32.3323294',
                'longitude' => '35.7520522',
                'address' => 'Ajloun, Ajloun Sub-District, Ajloun Qasabah District, Ajlun, Jordan',
                'country' => 'Jordan',
                'state' => 'Ajlun',
                'city' => 'Ajloun',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'latitude' => '25.1971578619314',
                'longitude' => '55.274287441974266',
                'address' => 'Burj Khalifa, 1, Sheikh Mohammed bin Rashid Boulevard, Downtown Dubai, Dubai, United Arab Emirates',
                'country' => 'United Arab Emirates',
                'state' => 'Dubai',
                'city' => 'Dubai',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'latitude' => '25.173933325897462',
                'longitude' => '55.02612715786252',
                'address' => 'Dubai, United Arab Emirates',
                'country' => 'United Arab Emirates',
                'state' => 'Dubai',
                'city' => 'Dubai',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
