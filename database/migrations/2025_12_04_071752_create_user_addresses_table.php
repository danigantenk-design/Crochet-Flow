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
    Schema::create('user_addresses', function (Blueprint $table) {
        $table->id();
        
        // Relasi ke tabel users
        // constrained() otomatis mencari tabel 'users' & 'id'
        // onDelete('cascade') menghapus alamat jika user dihapus
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        
        $table->string('recipient_name'); // Nama Penerima
        $table->string('phone_number');   // HP Penerima
        $table->string('label');          // Misal: Rumah, Kantor
        
        // ID Kota dari RajaOngkir (Integer)
        $table->integer('city_id');       
        
        $table->text('full_address');
        $table->string('postal_code');
        
        // Alamat default? Default false.
        $table->boolean('is_primary')->default(false);
        
        // Timestamps (created_at, updated_at)
        $table->timestamps();
        
        // Soft Deletes (deleted_at)
        $table->softDeletes();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
