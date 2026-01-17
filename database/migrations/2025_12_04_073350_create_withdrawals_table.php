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
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Info Penarikan
            $table->decimal('amount', 15, 2); // Jumlah yang ditarik
            $table->string('status')->default('pending'); // pending, approved, rejected
            
            // Info Rekening Tujuan
            $table->string('bank_name');      // BCA, BRI, dll
            $table->string('account_number'); // 1234567890
            $table->string('account_holder'); // Atas Nama Siapa
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
