<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index(Request $request)
    {
        // Mulai Query Produk (Hanya produk aktif)
        $query = Product::with(['category', 'images', 'shop'])
                    ->where('is_active', true);

        // 1. Logika Search (Nama Produk ATAU Nama Kategori)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;

            // Kita gunakan where(function...) untuk mengelompokkan logika OR
            // Agar tidak merusak filter 'is_active' di atas
            $query->where(function($q) use ($search) {
                // Cari di Nama Produk
                $q->where('name', 'like', "%{$search}%")
                  // ATAU Cari di Relasi Kategori -> Kolom Name
                  ->orWhereHas('category', function($subQuery) use ($search) {
                      $subQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // 2. Logika Filter Kategori (Filter Tombol Kategori)
        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Ambil data (Paginate biar rapi kalau produk banyak)
        $products = $query->latest()->paginate(12);

        // Ambil Kategori untuk menu filter
        $categories = Category::all();

        return view('welcome', compact('products', 'categories'));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
                    ->with(['shop', 'category', 'images'])
                    ->firstOrFail();

        return view('details', compact('product'));
    }
}