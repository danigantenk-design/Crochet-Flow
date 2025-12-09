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
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        
        // Pembeli
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
        
        // Toko (Penting untuk split order per toko)
        $table->foreignId('shop_id')->constrained()->onDelete('cascade');
        
        // Invoice Number (Contoh: INV/20251204/001)
        $table->string('invoice_number')->unique();
        
        // Status Transaksi
        $table->enum('status', ['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled'])->default('pending');
        
        // Keuangan (Decimal untuk akurasi)
        $table->decimal('total_price', 12, 2);
        $table->decimal('shipping_cost', 12, 2)->default(0);
        
        // Pembayaran
        $table->string('payment_method')->nullable();
        $table->string('midtrans_order_id')->nullable(); // ID dari Payment Gateway
        $table->string('payment_status')->default('pending'); // capture, settlement, expire, dll
        
        // Snapshot Alamat (PENTING!)
        // Simpan sebagai TEXT atau JSON. 
        // Isinya copy data alamat saat checkout. Jadi kalau user pindah rumah bulan depan, data order lama gak berubah.
        $table->text('shipping_address_snapshot');
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
