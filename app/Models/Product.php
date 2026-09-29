<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    // ==========================================
    // RELASI DATABASE
    // ==========================================

    public function shop() {
        return $this->belongsTo(Shop::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function images() {
        return $this->hasMany(ProductImage::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // ==========================================
    // ACCESSORS (Dinamis Attribute)
    // ==========================================

    // app/Models/Product.php

    public function getImageUrlAttribute()
    {
        // Ambil data gambar pertama dari relasi images
        $image = $this->images()->first();

        if ($image) {
            return asset('images/' . $image->image_url);
        }

        return asset('images/default-product.jpg');
    }

    /**
     * Menghitung rata-rata rating dari review
     */
    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('rating'), 1) ?? 0;
    }

    /**
     * Menghitung total jumlah review
     */
    public function getReviewCountAttribute()
    {
        return $this->reviews()->count();
    }

    /**
     * Menentukan rate komisi berdasarkan tipe produk (Digital 15%, Fisik 10%)
     */
    public function getCommissionRateAttribute()
    {
        return $this->product_type === 'digital' ? 0.15 : 0.10;
    }

    // ==========================================
    // LOGIKA BISNIS & PERHITUNGAN
    // ==========================================

    /**
     * Menghitung pendapatan bersih setelah dipotong komisi
     * Digunakan untuk rincian di Dashboard Penjual dan Admin
     */
    public function calculateNetIncome($price = null)
    {
        // Jika price tidak dipassing, gunakan harga produk saat ini
        $basePrice = $price ?? $this->price;
        $commission = $basePrice * $this->commission_rate;
        
        return $basePrice - $commission;
    }

    /**
     * Fungsi helper manual jika tidak ingin menggunakan Accessor
     */
    public function averageRating()
    {
        return $this->reviews()->avg('rating') ?: 0;
    }

    public function totalReviews()
    {
        return $this->reviews()->count();
    }
}