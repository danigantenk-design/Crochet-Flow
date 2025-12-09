<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    // Pastikan nama tabel sesuai database Anda
    protected $table = 'user_addresses';

    protected $guarded = ['id'];

    // Relasi balik ke User (Opsional, tapi bagus untuk dimiliki)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
{
    return $this->hasOne(UserAddress::class);
}

}