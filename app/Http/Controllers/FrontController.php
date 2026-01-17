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

    // 1. Logika Filter Kategori (UTAMAKAN INI DULU)
    // Gunakan 'where' agar hasil terkunci pada kategori ini saja
    if ($request->has('category') && $request->category != '') {
        $query->whereHas('category', function($q) use ($request) {
            $q->where('slug', $request->category);
        });
    }

    // 2. Logika Search (Nama Produk)
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;

        $query->where(function($q) use ($search) {
            // Cari di Nama Produk
            $q->where('name', 'like', "%{$search}%")
              // Jika ingin mencari di deskripsi juga, aktifkan ini:
              // ->orWhere('description', 'like', "%{$search}%")
              
              // SARAN: Hapus pencarian kategori di sini agar tidak tumpang tindih
              // dengan Filter Kategori di atas.
              ->orWhereHas('category', function($subQuery) use ($search) {
                  $subQuery->where('name', 'like', "%{$search}%");
              });
        });
    }

    $products = $query->latest()->paginate(12);
    $categories = Category::all();

    return view('welcome', compact('products', 'categories'));
}

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
                    ->with(['shop', 'category', 'images'])
                    ->firstOrFail();

        return view('products.show', compact('product'));
    }
    public function search(Request $request)
    {
        // 1. Ambil keyword pencarian
        $query = $request->input('q');
        $categorySlug = $request->input('category');
        $sort = $request->input('sort');

        // 2. Query Dasar (Hanya produk aktif)
        $products = Product::where('is_active', true);

        // 3. Filter berdasarkan Keyword (Nama Produk)
        if ($query) {
            $products->where('name', 'like', '%' . $query . '%');
        }

        // 4. Filter berdasarkan Kategori
        if ($categorySlug) {
            $products->whereHas('category', function($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // 5. Logic Sorting (Urutan)
        if ($sort == 'price_low') {
            $products->orderBy('price', 'asc');
        } elseif ($sort == 'price_high') {
            $products->orderBy('price', 'desc');
        } else {
            $products->latest(); // Default: Terbaru
        }

        // 6. Eksekusi Query (Paginate agar tidak berat)
        $products = $products->paginate(12)->withQueryString();
        $categories = Category::all();

        return view('search', compact('products', 'categories', 'query', 'categorySlug', 'sort'));
    }
}