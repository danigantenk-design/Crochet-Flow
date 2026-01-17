<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // 1. TAMPILKAN FORM TAMBAH PRODUK
    public function create()
    {
        // Pastikan user punya toko
        if (!Auth::user()->shop) {
            return redirect()->route('shop.create');
        }

        $categories = Category::all();
        return view('shop.products.create', compact('categories'));
    }

    // 2. SIMPAN PRODUK BARU
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:100',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Wajib ada gambar
        ]);

        // Upload Gambar
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Simpan ke Database
        Product::create([
            'shop_id' => Auth::user()->shop->id, // Otomatis link ke toko user
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(5), // Slug unik
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $imagePath,
            'is_active' => 1, // Default langsung aktif
        ]);

        return redirect()->route('shop.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    // 3. TAMPILKAN FORM EDIT
    public function edit($id)
    {
        $shopId = Auth::user()->shop->id;
        // Pastikan produk milik toko user yang sedang login (Keamanan)
        $product = Product::where('shop_id', $shopId)->findOrFail($id);
        
        $categories = Category::all();
        return view('shop.products.edit', compact('product', 'categories'));
    }

    // 4. UPDATE PRODUK
    public function update(Request $request, $id)
    {
        $shopId = Auth::user()->shop->id;
        $product = Product::where('shop_id', $shopId)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:100',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Opsional saat edit
        ]);

        // Data yang akan diupdate
        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(5),
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ];

        // Cek jika ada upload gambar baru
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            // Simpan gambar baru
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('shop.index')->with('success', 'Produk berhasil diperbarui!');
    }

    // 5. HAPUS PRODUK
    public function destroy($id)
    {
        $shopId = Auth::user()->shop->id;
        $product = Product::where('shop_id', $shopId)->findOrFail($id);

        // Hapus file gambar
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }
}