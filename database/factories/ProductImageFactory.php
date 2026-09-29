<?php

namespace Database\Factories;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductImage>
 */
class ProductImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Gunakan nama disk yang sudah kita buat/konfigurasi
        $diskName = 'dummy_images'; 
        
        // Ambil semua file dari disk 'dummy_images'
        // Error 'Undefined method 'files'' akan hilang jika Storage diimpor
        $files = Storage::disk($diskName)->files();
        
        // Pilih satu file secara acak
        if (empty($files)) {
            $randomImageName = 'default-image.jpg'; 
        } else {
            $randomImageName = $this->faker->randomElement($files);
        }

        // --- INI ADALAH BAGIAN KRUSIAL YANG DIUBAH ---
        return [
            // GANTI 'path' menjadi 'image_url' sesuai nama kolom di database
            'image_url' => 'images/products/' . $randomImageName, 
            'is_primary' => 1,
            // Kolom-kolom lain jika ada
        ];
        
    }
    /**
     * Define the state for a specific image folder.
     */
    public function withImagesFrom(string $folderPath): static
    {
        // $folderPath akan berisi: 'images/amigurumi' atau 'images/aksesoris'
        
        return $this->state(function (array $attributes) use ($folderPath) {
            
            // Tentukan nama disk (dummy_images yang sudah kita buat)
            $diskName = 'dummy_images'; 
            
            // ASUMSI: Gambar fisik ada di public/images/products/amigurumi/
            $fullPath = public_path('images/products/' . $folderPath);
            
            // Gunakan glob untuk mencari file di folder spesifik
            $files = glob($fullPath . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);

            if (empty($files)) {
                $randomImagePath = 'images/default.jpg';
            } else {
                $randomFile = $this->faker->randomElement($files);
                // Kita simpan path relatif dari folder public:
                $imageName = basename($randomFile);
                $randomImagePath = 'images/products/' . $folderPath . '/' . $imageName;
            }

            return [
                'image_url' => $randomImagePath,
            ];
        });
    }
}
