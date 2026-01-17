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

    public function shop() {
        return $this->belongsTo(Shop::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function images() {
        return $this->hasMany(ProductImage::class);
    }
    public function reviews(){
        return $this->hasMany(Review::class);
    }
    public function getImageUrlAttribute()
        {
      
        $image = $this->images()->where('is_primary', true)->first() ?? $this->images()->first();

        if ($image) {
            return asset('images/' . $image->image_url); 
        }

        return asset('images/default-product.jpg');
        }
    public function getAverageRatingAttribute()
        {
            return round($this->reviews()->avg('rating'), 1) ?? 0;
        }
    public function getReviewCountAttribute()
        {
            return $this->reviews()->count();
        }
    public function getCommissionRateAttribute()
        {
            // Mengembalikan persentase dalam bentuk desimal
            return $this->product_type === 'digital' ? 0.15 : 0.10;
        }

    public function calculateNetIncome($price)
        {
            $commission = $price * $this->commission_rate;
            return $price - $commission;
        }
    }
    