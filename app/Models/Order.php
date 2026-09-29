<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $guarded = ['id']; 

    public function user() { return $this->belongsTo(User::class); }
    public function shop() { return $this->belongsTo(Shop::class); }
    public function items() { return $this->hasMany(OrderItem::class); }
    public function shipment() { return $this->hasOne(Shipment::class); }


    public function getTotalCommission()
    {
        return $this->items->sum('commission_fee');
    }

    public function calculateNetIncome()
    {
        return ($this->total_price - $this->getTotalCommission());
    }

    public function getAdminProfitAttribute()
    {
        return $this->getTotalCommission();
    }

    // Cek apakah pesanan ini 100% Digital
    public function isFullDigital()
    {
        return $this->items->every(fn($item) => $item->product->product_type === 'digital');
    }
    

    
}