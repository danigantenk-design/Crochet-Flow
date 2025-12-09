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
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        
        // Relasi ke Toko & Kategori
        $table->foreignId('shop_id')->constrained()->onDelete('cascade');
        $table->foreignId('category_id')->constrained()->onDelete('cascade');
        
        $table->string('name');
        
        // Saya tambahkan slug agar URL produk cantik (contoh: /product/pola-baju-anak)
        $table->string('slug')->unique(); 
        
        $table->text('description');
        
        // Decimal(12, 2) mendukung angka hingga ratusan miliar dengan 2 desimal (aman untuk Rupiah)
        $table->decimal('price', 12, 2); 
        
        $table->integer('stock');
        
        // Berat dalam gram (penting untuk API RajaOngkir)
        $table->integer('weight');
        
        // Pembeda barang fisik atau download file
        $table->enum('product_type', ['physical', 'digital'])->default('physical');
        
        // Default 0 saat produk baru dibuat
        $table->integer('sold_count')->default(0);
        
        // Link file jika digital (Nullable)
        $table->string('file_url')->nullable();
        
        // Status produk tampil/tidak
        $table->boolean('is_active')->default(true);
        
        $table->timestamps();
        $table->softDeletes(); // Fitur "Sampah" agar data tidak hilang permanen
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
