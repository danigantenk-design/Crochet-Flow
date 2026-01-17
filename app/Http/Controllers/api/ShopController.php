<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    // List semua toko yang sudah diverifikasi
    public function apiIndex()
    {
        $shops = Shop::where('is_verified', true)
                     ->where('is_active', true)
                     ->get();

        return response()->json([
            'success' => true,
            'data' => $shops
        ]);
    }

    // Detail toko beserta produk-produknya
    public function apiShow($id)
    {
        $shop = Shop::with(['products' => function($q) {
            $q->where('is_active', true);
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $shop
        ]);
    }
}