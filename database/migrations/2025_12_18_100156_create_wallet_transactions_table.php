<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            
            // Jenis: 'credit' (uang masuk) atau 'debit' (uang keluar/tarik dana)
            $table->enum('type', ['credit', 'debit']); 
            
            $table->decimal('amount', 15, 2); // Jumlah uang
            $table->string('description'); // Keterangan (misal: "Penjualan Order #INV-123")
            
            // Opsional: Relasi ke Order (biar tau uang ini dari order mana)
            $table->unsignedBigInteger('reference_id')->nullable(); 
            $table->string('reference_type')->nullable(); // 'order', 'withdrawal'

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
