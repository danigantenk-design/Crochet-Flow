<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $table = 'user_addresses';

    // Definisikan fillable agar aman dan postal_code bisa masuk
    protected $fillable = [
        'user_id',
        'recipient_name',
        'phone_number',
        'full_address',
        'postal_code', 
        'label',       
        'city_id',
        'is_primary'
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}