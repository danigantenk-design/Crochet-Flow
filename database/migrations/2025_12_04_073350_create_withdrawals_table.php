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
    Schema::create('withdrawals', function (Blueprint $table) {
        $table->id();
        
        // Toko mana yang mengajukan?
        $table->foreignId('shop_id')->constrained()->onDelete('cascade');
        
        // Jumlah penarikan
        $table->decimal('amount', 12, 2);
        
        // Status pengajuan
        $table->enum('status', ['requested', 'approved', 'rejected'])->default('requested');
        
        // Admin yang memproses (Nullable, karena saat request dibuat belum ada admin yang pegang)
        $table->foreignId('admin_id')->nullable()->constrained('users');
        
        // Catatan admin (misal: "Nomor rekening salah")
        $table->text('note')->nullable();
        
        // Kapan disetujui/ditolak
        $table->timestamp('processed_at')->nullable();
        
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
