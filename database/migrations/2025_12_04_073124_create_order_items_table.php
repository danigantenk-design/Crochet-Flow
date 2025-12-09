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
    Schema::create('order_items', function (Blueprint $table) {
        $table->id();
        
        // Terhubung ke Order Header
        $table->foreignId('order_id')->constrained()->onDelete('cascade');
        
        // Produk apa
        $table->foreignId('product_id')->constrained()->onDelete('cascade');
        
        $table->integer('quantity');
        
        // Harga saat dibeli (PENTING!)
        // Jangan ambil harga dari tabel products saat menampilkan history, 
        // karena harga produk bisa berubah naik/turun di masa depan.
        $table->decimal('price_at_purchase', 12, 2);
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
