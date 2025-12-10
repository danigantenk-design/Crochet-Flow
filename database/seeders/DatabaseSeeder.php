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

        // 5. Kategori (Gunakan kode yang sudah diperbaiki untuk keunikan)
        $categoryData = [
            'amigurumi' => 'Amigurumi',
            'aksesoris' => 'Aksesoris Rajut',
            'dekorasi'  => 'Dekorasi Rumah',
        ];

        $categories = collect($categoryData)->map(function ($name, $slug) {
             return Category::create([
                 'name' => $name,
                 'slug' => $slug, // Menggunakan key array sebagai slug
             ]);
        });
        $categories = new \Illuminate\Database\Eloquent\Collection($categories);

        // 6. Produk (+ Langsung buatin Gambar BERDASARKAN KATEGORI)
        
        // Dapatkan objek Category spesifik
        $amigurumiCat = $categories->where('slug', 'amigurumi')->first();
        $aksesorisCat = $categories->where('slug', 'aksesoris')->first();
        $dekorasiCat = $categories->where('slug', 'dekorasi')->first();
        
        // A. Buat 8 Produk Amigurumi (Mengambil Gambar dari folder /amigurumi)
        Product::factory(4)
            ->has(ProductImage::factory()->withImagesFrom('amigurumi')->count(1), 'images')
            ->create([
                'shop_id' => $shop->id,
                'category_id' => $amigurumiCat->id,
            ]);

        // B. Buat 8 Produk Aksesoris (Mengambil Gambar dari folder /aksesoris)
        Product::factory(10)
            ->has(ProductImage::factory()->withImagesFrom('aksesoris')->count(1), 'images')
            ->create([
                'shop_id' => $shop->id,
                'category_id' => $aksesorisCat->id,
            ]);
            
        // C. Sisanya 4 Produk Pakaian
        Product::factory(4)
            ->has(ProductImage::factory()->withImagesFrom('dekorasi')->count(1), 'images')
            ->create([
                'shop_id' => $shop->id,
                'category_id' => $dekorasiCat->id,
            ]);
    
    }
}