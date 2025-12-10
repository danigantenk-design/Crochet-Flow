<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
    // 1. Definisikan daftar kategori yang Anda inginkan
    $categories = [
        'Aksesoris', 
        'Amigurumi', 
        'Dekorasi Rumah', 
    ];
    
    // 2. Pilih salah satu nama kategori secara acak
    $categoryName = $this->faker->randomElement($categories);

    return [
        'name' => $categoryName,
        
        // 3. Buat slug dari nama yang sudah dipilih
        'slug' => Str::slug($categoryName), 
    ];
    }
}