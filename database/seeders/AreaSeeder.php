<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tourist_areas')->insert([
            [
                'name' => 'Umayyad Mosque',
                'description' => 'The Umayyad Mosque in Damascus is one of the oldest and most important historical and religious monuments in Syria and in the Islamic world in general. The Umayyad Mosque is considered one of the greatest Islamic temples, a center of worship',
                'img' => 'areaPhoto/5KcGcXvInLoA2LzJe0tJIWnmPMOMHOxXSsa861SR.jpg',
                'type' => 'Mosques',
                'location_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'National Museum',
                'description' => 'The National Museum in Damascus is one of the most important museums in Syria and is a prominent cultural and historical center. The museum is located in the heart of Damascus, and includes a large and diverse collection of antiquities',
                'img' => 'areaPhoto/x2C8FgGjXU6YSueq3uqZF8OBP4DT32f2p3hsQUsP.jpg',
                'type' => 'Museums',
                'location_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Al Hamidiah Souq',
                'description' => 'It is one of the oldest and most famous traditional markets in the world. Hamidiyeh is considered a vital center for commercial and cultural activity in the old city',
                'img' => 'areaPhoto/CqE2DPni2U3LqkWuend1YbtWFWa9tKXdAoHGZKk9.jpg',
                'type' => 'Markets',
                'location_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Citadel of Aleppo',
                'description' => 'Aleppo Citadel is considered one of the most important historical monuments in the city, and its construction dates back to the Middle Ages. The Citadel of Aleppo offers wonderful views of the city',
                'img' => 'areaPhoto/bBikJ7Dhl1UfeiRIgcylwnctDVNlsvohKfid2r0L.jpg',
                'type' => 'Citadels',
                'location_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Waterwheels of Hama',
                'description' => 'The Hama Waterways is a group of ancient buildings in the city of Hama, Syria. Hama\'s waterwheels are considered one of the city\'s most prominent historical landmarks. It is a group of inclined wooden towers',
                'img' => 'areaPhoto/PTtTEoixI2CsTQdNgxtQxbfGAmZ1uh1OFRKc42Ou.jpg',
                'type' => 'Ruins',
                'location_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Islamic Art Museum',
                'description' => 'It is a museum that includes a rich art collection dating back to various Islamic periods. The museum is located in Beirut\'s Cultural District, and is part of the Beiteddine building dating back to the 19th century.',
                'img' => 'areaPhoto/cnd25D9EYZKZGxXDTMzva5OOzfAhk9kCgQrLkv8g.jpg',
                'type' => 'Museums',
                'location_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jbeil Citadel',
                'description' => 'It is a prominent historical facility located in the coastal city of Jbeil, Lebanon. Also known as the “Jbeil Crusader Castle,” it is one of the most important archaeological and historical sites in the country. Byblos Castle was built during the Crusader times in the 12th century AD, and was part of the defense system built by the Crusader powers to protect the region from invaders. The castle was designed in a way that allows a view and protection of the city and the port.',
                'img' => 'areaPhoto/mACw1auXsCATB80NPYrKI3u89ji51YnzYl70Fkbm.jpg',
                'type' => 'Citadels',
                'location_id' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lebanon Mountains',
                'description' => 'It is a mountain range located in the eastern part of the Mediterranean Sea, and extends along the eastern border of Lebanon. This mountain range forms an important part of Lebanon\'s terrain, extending from north to south over hundreds of kilometers. Lebanon\'s mountains are characterized by their stunning scenery and diverse terrain, including towering peaks, deep valleys, magnificent waterfalls, and lush green forests. This area is home to many wild plants and animals, making it an important natural haven in Lebanon.',
                'img' => 'areaPhoto/dcwUgDzzq7z3ErPfDXYLGJ3OiFxrkihnm1Sm0SJg.jpg',
                'type' => 'Mountains',
                'location_id' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Giza Pyramids',
                'description' => 'They are huge pyramidal structures built during ancient times, and they are among the most important archaeological and tourist attractions in the world. The pyramids were built during the Old Kingdom period in Egypt, and three of them are the largest and most famous.',
                'img' => 'areaPhoto/KYwL48PvXNsI8ljsbypocJBIgWKL03M5mFpeuWMu.jpg',
                'type' => 'Ruins',
                'location_id' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Karnak Temple',
                'description' => 'It is one of the largest Pharaonic temples in Egypt, and it is an important archaeological site located in the city of Luxor on the eastern bank of the Nile River. The temple was built in different Pharaonic eras, starting from the modern era of the Old Kingdom until the era of the Roman Empire.',
                'img' => 'areaPhoto/JBfiOcOcIt7vEn0yFxGTxxrQ6km0LCR6juDPEd9H.jpg',
                'type' => 'Ruins',
                'location_id' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Egyptian Museum',
                'description' => 'It is one of the most important museums in the world and is considered home to a huge collection of Pharaonic antiquities and archaeological collectibles dating back to several periods of ancient Egyptian history. The museum is located in the heart of Cairo, the capital of Egypt.',
                'img' => 'areaPhoto/zXOgzwzziel7MTkOxmj0U2r1KkiOFs8fbSTHtHym.jpg',
                'type' => 'Museums',
                'location_id' => 11,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Al Petra',
                'description' => 'It is an important archaeological site and a historic city located in southern Jordan, and it is one of the Seven Modern Wonders of the World. Petra is considered one of the most prominent tourist destinations in the world thanks to its natural beauty and rich history.',
                'img' => 'areaPhoto/xWyA786TYRhXUvoT1aEYkLRLC5c3LBKbQ0pFiMvU.jpg',
                'type' => 'Citadels',
                'location_id' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ram Valley',
                'description' => 'It is a beautiful desert area located in southern Jordan, and is considered one of the country\'s prominent tourist attractions. Wadi Rum is famous for its unique rocky terrain and stunning landscapes that attract visitors from all over the world.',
                'img' => 'areaPhoto/TVPb7jl4DHOTtVRtrWnd2djep8rzKtIuqWUJhq1A.jpg',
                'type' => 'Nature',
                'location_id' => 13,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ajlon Citadel',
                'description' => 'It is a historical castle located in Ajloun Governorate in northern Jordan, and it is considered one of the important cultural and historical landmarks in the region. The castle was built during the Islamic Middle Ages, specifically in the period between the twelfth and fourteenth centuries AD.',
                'img' => 'areaPhoto/GR8v850ubhYNz0qQWQpivlUrX14obCsMkCFFVacT.jpg',
                'type' => 'Citadels',
                'location_id' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Khalifa Tower',
                'description' => 'Burj Khalifa is a high-rise tower located in Dubai, United Arab Emirates, and is currently the tallest tower in the world. Officially opened on January 4, 2010, it is considered a symbol of urban sophistication and luxury in Dubai and the UAE in general. Burj Khalifa is approximately 828 meters (2,717 feet) tall and has 163 floors. The tower includes a variety of facilities and activities',
                'img' => 'areaPhoto/mj0yDBVpQP6DjfU9mQ59MhCAVCoHJH5UgmLCSFuL.jpg',
                'type' => 'Towers',
                'location_id' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Al Arab Tower',
                'description' => 'Burj Al Arab is a luxury hotel located in Dubai, United Arab Emirates, and is considered one of the most prominent tourist attractions in the world. Burj Al Arab is distinguished by its unique design that combines the famous sail shape with modern luxury.',
                'img' => 'areaPhoto/dLv9TxClQEKOHwzOiUjHPGIhr1UdoRn0mJuVQMyZ.jpg',
                'type' => 'Towers',
                'location_id' => 16,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);

    }
}
