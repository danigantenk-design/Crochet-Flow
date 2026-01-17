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
    Schema::create('shops', function (Blueprint $table) {
        $table->id();
        
        // Pemilik Toko
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        
        $table->string('name');
        
        // Logo bisa null saat awal buat toko
        $table->string('logo_url')->nullable();
        
        // Slug unique untuk URL toko (crochetflow.com/shop/nama-toko)
        $table->string('slug')->unique();
        
        $table->text('description')->nullable();
        
        // Lokasi asal pengiriman (Origin RajaOngkir)
        $table->integer('city_id');
        
        // Status verifikasi toko
        $table->boolean('is_verified')->default(false);

        $table->boolean('is_active')->default(false);
        
        // Info Bank (Nullable dulu, diisi saat mau cairkan dana)
        $table->string('bank_name')->nullable();
        $table->string('bank_account_number')->nullable();
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
