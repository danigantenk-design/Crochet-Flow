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
        Schema::create('users', function (Blueprint $table) {
            // id (PK, Integer)
            $table->id();
            
            // full_name (Varchar)
            $table->string('full_name');
            
            // email (Varchar, Unique)
            $table->string('email')->unique();
            
            // password (Varchar, Hashed)
            $table->string('password');
            
            // role (Enum: 'admin', 'buyer', 'seller')
            // Kita beri default 'buyer' agar user baru otomatis jadi pembeli
            $table->enum('role', ['admin', 'buyer', 'seller'])->default('buyer');
            
            // phone_number (Varchar) - Dibuat nullable dulu jaga-jaga user daftar via email saja
            $table->string('phone_number')->nullable();
            
            // avatar_url (Varchar) - Nullable karena user baru belum punya foto profil
            $table->string('avatar_url')->nullable();
            
            // email_verified_at
            $table->timestamp('email_verified_at')->nullable();
            
            // remember_token
            $table->rememberToken();
            
            // created_at (Timestamp) & updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};