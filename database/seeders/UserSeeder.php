<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\News;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin Adit',
            'email' => 'leasehat@gmail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now()
        ]);

        User::factory()->create([
            'name' => 'Sekolah Saras',
            'email' => 'saras@example.com',
            'password' => Hash::make('password'),
        ]);

        User::factory()->create([
            'name' => 'Sekolah Yulia',
            'email' => 'yulia@example.com',
            'password' => Hash::make('password'),
        ]);

        // User::factory(6)->create();


        Category::create([
            'name' => 'Berita',
            'slug' => 'berita'
        ]);

        Category::create([
            'name' => 'Prestasi',
            'slug' => 'prestasi'
        ]);

        Category::create([
            'name' => 'Event',
            'slug' => 'event'
        ]);

        Tag::create([
            'name' => 'kesehatan'
        ]);
        Tag::create([
            'name' => 'olahraga'
        ]);
        Tag::create([
            'name' => 'seni'
        ]);
        Tag::create([
            'name' => 'musik'
        ]);

        $dataYulia = [
            [
                'content_url' => 'QFLAuddS6qM',
                'title' => 'Full Guide Belajar CODING untuk Pemula',
            ],
            [
                'content_url' => 'A2rj60uKFyc',
                'title' => '10 TIPS UNTUK MULAI BELAJAR PROGRAMMING',
            ],
            [
                'content_url' => 'Qzcc-FWv0cM',
                'title' => 'PENYESALAN Ketika Kuliah IT',
            ],
            [
                'content_url' => 'AHLt6eXyVQk',
                'title' => 'Apa Itu Coding? Tips Coding untuk Pemula! Kamu Wajib Banget Tahu!',
            ],
            [
                'content_url' => 'u1YAgJmZtmE',
                'title' => 'Penting! Ini Tips Buat Programmer Pemula | Code Insight',
            ],
        ];

        foreach ($dataYulia as $item) {
            News::create([
                'content_url' => $item['content_url'],
                'title' => $item['title'],
                'category_id' => rand(1, 3),
                'user_id' => 4,
            ]);
        }

        $dataAdit = [
            [
                "content_url" => "y8tKvxHE2mI",
                "title" => "RATA-RATA SUDAH MAU SELESAI!!! PROSES OGOH OGOH 2025 PART 8",
            ],

            [
                "content_url" => "ucPK5PSHmMw",
                "title" => "MUSIM DIMULAI 🔥 PROGRES OGOH OGOH 2025 #5",
            ],

            [
                "content_url" => "LCevkk0DEV8",
                "title" => "Ogoh ogoh denpasar 2025 keren dan super detail 🔥🔥🔥",
            ],

            [
                "content_url" => "450p7goxZqg",
                "title" => "John Legend - All of Me (Official Video)",
            ],

            [
                "content_url" => "PRnXtFEEYgw",
                "title" => "FULL SELURUH PESERTA PENILAIAN LOMBA OGOH-OGOH DENPASAR SELATAN HARI PERTAMA 2024"
            ],
        ];

        foreach ($dataAdit as $item) {
            News::create([
                'content_url' => $item['content_url'],
                'title' => $item['title'],
                'category_id' => rand(1, 3),
                'user_id' => 2,
            ]);
        }


        $dataSaras = [
            [
                "content_url" => "qU9mHegkTc4",
                "title" => "505",
            ],

            [
                "content_url" => "IpFX2vq8HKw",
                "title" => "yung kai - blue (Official Music Video)",
            ],

            [
                "content_url" => "dxfUGDgfjPs",
                "title" => "Blok situs dan Ekstensi di Mikrotik",
            ],

            [
                "content_url" => "OEV8gMkCHXQ",
                "title" => "CSS in 100 Second",
            ],

            [
                "content_url" => "qCk37vXQnUY",
                "title" => "Kehidupan di Zaman Prasejarah",
            ],
        ];

        foreach ($dataSaras as $item) {
            News::create([
                'content_url' => $item['content_url'],
                'title' => $item['title'],
                'category_id' => rand(1, 3),
                'user_id' => 3,
            ]);
        }
    }
}
