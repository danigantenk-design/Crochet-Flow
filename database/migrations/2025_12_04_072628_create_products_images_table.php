<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('product_images', function (Blueprint $table) {
        $table->id();
        
        // Jika produk dihapus, gambarnya ikut terhapus dari DB
        $table->foreignId('product_id')->constrained()->onDelete('cascade');
        
        $table->string('image_url');
        
        // Menandakan gambar utama (thumbnail)
        $table->boolean('is_primary')->default(false);
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_images');
    }
};
