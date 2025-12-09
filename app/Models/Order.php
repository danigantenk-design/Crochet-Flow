<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id']; // Mengizinkan mass assignment untuk semua kolom kecuali ID

    // Relasi: Order milik siapa? (Pembeli)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Order ini beli di toko mana?
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    // Relasi: Order ini isinya barang apa saja?
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Relasi: Pengiriman (jika ada)
    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }
}