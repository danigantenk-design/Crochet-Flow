<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            
            // Relasi
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            
            // PENTING: Saya buat nullable dulu biar ga error kalau kategori belum dipilih/dibuat
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            
            // Decimal aman untuk Rupiah
            $table->decimal('price', 12, 2); 
            $table->integer('stock');

            // Berat (gram). Saya kasih default 100gr supaya tidak error kalau form kosong
            $table->integer('weight')->default(100);
            
            $table->enum('product_type', ['physical', 'digital'])->default('physical');
            $table->integer('sold_count')->default(0);
            $table->string('file_url')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};