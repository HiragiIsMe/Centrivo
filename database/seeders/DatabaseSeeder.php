<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\SellerProfile;
use App\Models\Location;
use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceImage;
use App\Models\Review;
use App\Models\ServiceRequest;
use App\Models\Transaction;
use App\Models\Report;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin
        $admin = User::create([
            'email' => 'admin@centrivo.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        UserProfile::create([
            'user_id' => $admin->id,
            'name' => 'Administrator',
            'phone' => '081234567890',
            'address' => 'Kantor Pusat Centrivo',
        ]);

        // 2. Create Users
        $user1 = User::create([
            'email' => 'user1@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'is_active' => true,
        ]);
        UserProfile::create([
            'user_id' => $user1->id,
            'name' => 'Budi Santoso',
            'phone' => '08111111111',
            'address' => 'Jl. Merdeka No.1, Jakarta',
            'latitude' => -6.200000,
            'longitude' => 106.816666,
        ]);

        $user2 = User::create([
            'email' => 'user2@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'is_active' => true,
        ]);
        UserProfile::create([
            'user_id' => $user2->id,
            'name' => 'Siti Aminah',
            'phone' => '08222222222',
            'address' => 'Jl. Sudirman No.2, Bandung',
            'latitude' => -6.914744,
            'longitude' => 107.609810,
        ]);

        // 3. Create Sellers
        $seller1 = User::create([
            'email' => 'seller1@example.com',
            'password' => Hash::make('password'),
            'role' => 'seller',
            'is_active' => true,
        ]);
        SellerProfile::create([
            'user_id' => $seller1->id,
            'brand_name' => 'Klinik AC Jember',
            'description' => 'Ahlinya perbaikan dan service AC segala merk.',
            'phone' => '08333333333',
        ]);
        $location1 = Location::create([
            'user_id' => $seller1->id,
            'province' => 'Jawa Timur',
            'city' => 'Jember',
            'district' => 'Sumbersari',
            'address' => 'Jl. Mastrip No.10',
            'latitude' => -8.1724,
            'longitude' => 113.7005,
        ]);

        $seller2 = User::create([
            'email' => 'seller2@example.com',
            'password' => Hash::make('password'),
            'role' => 'seller',
            'is_active' => true,
        ]);
        SellerProfile::create([
            'user_id' => $seller2->id,
            'brand_name' => 'Digital Kreatif Studio',
            'description' => 'Layanan desain grafis dan website profesional.',
            'phone' => '08444444444',
        ]);
        $location2 = Location::create([
            'user_id' => $seller2->id,
            'province' => 'Jawa Timur',
            'city' => 'Surabaya',
            'district' => 'Gubeng',
            'address' => 'Jl. Dharmawangsa No. 20',
            'latitude' => -7.2756,
            'longitude' => 112.7538,
        ]);

        // 4. Create Categories
        $cat1 = Category::create(['name' => 'Rumah Tangga']);
        $cat2 = Category::create(['name' => 'IT & Digital']);
        $cat3 = Category::create(['name' => 'Otomotif']);

        // 5. Create Services
        $service1 = Service::create([
            'seller_id' => $seller1->id,
            'category_id' => $cat1->id,
            'location_id' => $location1->id,
            'service_name' => 'Service AC Cuci & Tambah Freon',
            'description' => 'Melayani cuci AC, perbaikan bocor, dan isi ulang Freon R32/R410. Garansi 30 hari!',
            'start_price' => 75000,
            'whatsapp' => '08333333333',
            'status' => 'active',
        ]);

        $service2 = Service::create([
            'seller_id' => $seller2->id,
            'category_id' => $cat2->id,
            'location_id' => $location2->id,
            'service_name' => 'Pembuatan Website Profile Perusahaan',
            'description' => 'Website modern, responsif, dan SEO friendly. Sudah termasuk domain dan hosting 1 tahun.',
            'start_price' => 1500000,
            'whatsapp' => '08444444444',
            'status' => 'active',
        ]);

        
        echo "Database seeded successfully!\n";
    }
}
