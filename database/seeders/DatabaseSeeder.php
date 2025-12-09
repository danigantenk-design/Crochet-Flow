<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Shop;           
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\UserAddress;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin
        User::create([
            'full_name' => 'Admin Crochet',
            'email' => 'admin@crochetflow.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'phone_number' => '081234567890',
        ]);

        // 2. Seller
        $seller = User::create([
            'full_name' => 'Siti Pengrajin',
            'email' => 'seller@crochetflow.com',
            'password' => bcrypt('password'),
            'role' => 'seller',
            'phone_number' => '08987654321',
        ]);

        // 3. Toko milik Seller
        $shop = Shop::factory()->create([
            'user_id' => $seller->id,
            'name' => 'Siti Crochet Store',
            'slug' => 'siti-crochet-store',
        ]);

        // 4. Buyer (+ Langsung buatin Alamat)
        $buyer = User::create([
            'full_name' => 'Budi Pembeli',
            'email' => 'buyer@crochetflow.com',
            'password' => bcrypt('password'),
            'role' => 'buyer',
        ]);

        // Buatkan alamat untuk si Buyer (menggunakan factory)
        UserAddress::factory()->create([
            'user_id' => $buyer->id,
            'recipient_name' => $buyer->full_name,
        ]);

        // 5. Kategori
        $categories = Category::factory(5)->create();

        // 6. Produk (+ Langsung buatin Gambar)
        // Buat 20 produk, masing-masing punya 1 gambar
        foreach(range(1, 20) as $item) {
            Product::factory()
                ->has(ProductImage::factory()->count(1), 'images') // Relasi ke images
                ->create([
                    'shop_id' => $shop->id,
                    'category_id' => $categories->random()->id,
                ]);
        }
    }
}