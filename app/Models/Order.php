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

    // Logika Hitung Total Pendapatan Bersih Penjual (Setelah Potongan Komisi)
    public function calculateNetIncome()
    {
        $totalNet = 0;
        foreach ($this->items as $item) {
            $price = $item->price_at_purchase * $item->quantity;
            // 15% Digital, 10% Fisik
            $rate = ($item->product->product_type === 'digital') ? 0.15 : 0.10;
            $totalNet += ($price - ($price * $rate));
        }
        // Ongkir tidak dipotong komisi
        return $totalNet + $this->shipping_cost;
    }

    // Cek apakah pesanan ini 100% Digital
    public function isFullDigital()
    {
        return $this->items->every(fn($item) => $item->product->product_type === 'digital');
    }
}