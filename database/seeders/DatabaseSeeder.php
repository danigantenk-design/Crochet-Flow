<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Shop;           
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\UserAddress;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin
        User::create([
            'full_name' => 'Admin Crochet',
            'email' => 'admin@crochetflow.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone_number' => '081234567890',
        ]);

        // 2. Seller
        $seller = User::create([
            'full_name' => 'Siti Pengrajin',
            'email' => 'seller@crochetflow.com',
            'password' => Hash::make('password'),
            'role' => 'seller',
            'phone_number' => '08987654321',
        ]);

        // 3. Toko milik Seller
        $shop = Shop::create([
            'user_id' => $seller->id,
            'name' => 'Siti Crochet Store',
            'slug' => 'siti-crochet-store',
            'description' => 'Toko kerajinan rajut tangan (crochet) berkualitas tinggi.',
            'city_id' => 151, // Contoh ID Kota
            'phone' => '08987654321',
            'address' => 'Jl. Rajut No. 123',
            'is_verified' => true,
            'is_active' => true,
        ]);

        // 4. Buyer (+ Alamat)
        $buyer = User::create([
            'full_name' => 'Budi Pembeli',
            'email' => 'buyer@crochetflow.com',
            'password' => Hash::make('password'),
            'role' => 'buyer',
        ]);

        UserAddress::create([
            'user_id' => $buyer->id,
            'label' => 'Rumah',
            'recipient_name' => 'Budi Pembeli',
            'phone_number' => '081122334455',
            'full_address' => 'Jl. Pembeli Sukses No. 45, Jakarta',
            'city_id' => 151,
            'postal_code' => '12345',
            'is_primary' => true,
        ]);

        // 5. Kategori
        $categoryData = [
            'amigurumi' => 'Amigurumi',
            'aksesoris' => 'Aksesoris Rajut',
            'dekorasi'  => 'Dekorasi Rumah',
        ];

        $categories = collect($categoryData)->map(function ($name, $slug) {
             return Category::create([
                 'name' => $name,
                 'slug' => $slug,
             ]);
        });

        $amigurumiCat = $categories->where('slug', 'amigurumi')->first();
        $aksesorisCat = $categories->where('slug', 'aksesoris')->first();
        $dekorasiCat  = $categories->where('slug', 'dekorasi')->first();

        // 6. Produk (Fisik & Digital)
        $myProducts = [
            // Produk Fisik
            ['name' => 'Tas Rajut Daily Ceria', 'cat' => $aksesorisCat->id, 'price' => 55000, 'img' => 'products/aksesoris/1.jpg', 'type' => 'physical', 'file' => null],
            ['name' => 'Pot Bunga Mini', 'cat' => $dekorasiCat->id, 'price' => 15000, 'img' => 'products/dekorasi/7.jpg', 'type' => 'physical', 'file' => null],
            ['name' => 'Gantungan Kunci Ubur-ubur', 'cat' => $aksesorisCat->id, 'price' => 20000, 'img' => 'products/aksesoris/3.jpg', 'type' => 'physical', 'file' => null],
            ['name' => 'Boneka Amigurumi Sapi Lucu', 'cat' => $amigurumiCat->id, 'price' => 60000, 'img' => 'products/amigurumi/6.jpg', 'type' => 'physical', 'file' => null],
            
            // Produk Digital (Pola PDF)
            [
                'name' => 'Pola Rajut Boneka Kelinci', 
                'cat' => $amigurumiCat->id, 
                'price' => 25000, 
                'img' => 'products/amigurumi/kelinci.jpg', 
                'type' => 'digital', 
                'file' => 'patterns/pola-kelinci.pdf'
            ],
            [
                'name' => 'Pola Rajut Tas Rajut Estetik', 
                'cat' => $aksesorisCat->id, 
                'price' => 35000, 
                'img' => 'products/aksesoris/1.jpg', 
                'type' => 'digital', 
                'file' => 'patterns/pola-tas.pdf'
            ],
            [
                'name' => 'Pola Rajut Tatakan Gelas Bunga', 
                'cat' => $dekorasiCat->id, 
                'price' => 15000, 
                'img' => 'products/dekorasi/7.jpg', 
                'type' => 'digital', 
                'file' => 'patterns/pola-tatakan.pdf'
            ],
            [
                'name' => 'Pola Rajut Amigurumi Ayam', 
                'cat' => $amigurumiCat->id, 
                'price' => 20000, 
                'img' => 'products/amigurumi/8.jpg', 
                'type' => 'digital', 
                'file' => 'patterns/pola-ayam.pdf'
            ],
        ];

        foreach ($myProducts as $p) {
            $product = Product::create([
                'shop_id' => $shop->id,
                'category_id' => $p['cat'],
                'name' => $p['name'],
                'slug' => Str::slug($p['name']) . '-' . Str::random(5),
                'description' => 'Produk rajutan tangan (handmade) berkualitas tinggi dengan benang lembut.',
                'price' => $p['price'],
                'stock' => ($p['type'] === 'digital') ? 999 : 15, // Produk digital stok melimpah
                'weight' => ($p['type'] === 'digital') ? 0 : 250,   // Produk digital berat 0
                'product_type' => $p['type'],
                'file_path' => $p['file'],
                'is_active' => true,
            ]);

            ProductImage::create([
                'product_id' => $product->id,
                'image_url' => $p['img'], 
                'is_primary' => true
            ]);
        }   
    }
}