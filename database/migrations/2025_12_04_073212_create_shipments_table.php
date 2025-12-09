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
    Schema::create('shipments', function (Blueprint $table) {
        $table->id();
        
        // Relasi ke Order
        $table->foreignId('order_id')->constrained()->onDelete('cascade');
        
        $table->string('status'); // picking, shipping, delivered
        $table->string('courier_code'); // jne, pos, tiki
        $table->string('service_type'); // OKE, REG, YES
        $table->string('tracking_number')->nullable(); // No Resi (awal null dulu sebelum dikirim)
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
