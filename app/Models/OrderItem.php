<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi: Item ini induknya order yang mana?
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relasi: Item ini aslinya produk apa?
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}