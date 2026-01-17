<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // Mengambil produk beserta relasi images dan shop
        $products = Product::with(['images', 'shop'])
                    ->where('is_active', true)
                    ->latest()
                    ->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function show($id)
    {
        $product = Product::with(['images', 'shop', 'category'])->find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $product]);
    }
}