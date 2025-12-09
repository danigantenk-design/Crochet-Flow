<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    // 1. Tampilkan Form Tambah Produk
    public function create()
    {
        // Ambil kategori untuk dropdown
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    // 2. Simpan Produk Baru ke Database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'weight' => 'required|numeric|min:0', // Gram
            'description' => 'required|string',
            'product_type' => 'required|in:physical,digital',
            
            // Validasi File
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Wajib ada 1 gambar utama
            'digital_file' => 'nullable|mimes:pdf|max:10240', // Max 10MB, wajib jika tipe digital
        ]);

        // Validasi tambahan: Jika digital, file PDF wajib ada
        if ($request->product_type == 'digital' && !$request->hasFile('digital_file')) {
            return back()->withErrors(['digital_file' => 'File PDF wajib diupload untuk produk digital.'])->withInput();
        }

        DB::beginTransaction();
        try {
            $shop = Auth::user()->shop;

            // 1. Simpan Data Produk
            $product = Product::create([
                'shop_id' => $shop->id,
                'category_id' => $request->category_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name) . '-' . Str::random(5),
                'price' => $request->price,
                'stock' => $request->stock,
                'weight' => $request->weight,
                'description' => $request->description,
                'product_type' => $request->product_type,
                'is_active' => true,
            ]);

            // 2. Upload & Simpan Gambar Utama
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');
                
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => '/storage/' . $path,
                    'is_primary' => true,
                ]);
            }

            // 3. Upload File Digital (Jika ada)
            if ($request->hasFile('digital_file')) {
                $filePath = $request->file('digital_file')->store('digital_products', 'public'); // Sebaiknya 'private' di production
                $product->update(['file_url' => '/storage/' . $filePath]);
            }

            DB::commit();
            return redirect()->route('shop.index')->with('success', 'Produk berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    // 3. Tampilkan Form Edit
    public function edit($id)
    {
        $product = Product::with('images')->findOrFail($id);

        // Keamanan: Pastikan yang edit adalah pemilik toko
        if ($product->shop_id !== Auth::user()->shop->id) {
            abort(403, 'Anda tidak berhak mengedit produk ini.');
        }

        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    // 4. Update Produk
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // Keamanan
        if ($product->shop_id !== Auth::user()->shop->id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'weight' => 'required|numeric|min:0',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // 1. Update Data Dasar
            $product->update([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name) . '-' . Str::random(5), // Update slug jika nama berubah
                'price' => $request->price,
                'stock' => $request->stock,
                'weight' => $request->weight,
                'description' => $request->description,
            ]);

            // 2. Update Gambar (Ganti gambar utama jika ada upload baru)
            if ($request->hasFile('image')) {
                // Hapus gambar lama dari storage (Opsional, agar hemat space)
                // ... logic hapus file ...

                // Upload gambar baru
                $path = $request->file('image')->store('products', 'public');
                
                // Update record gambar utama
                $primaryImage = ProductImage::where('product_id', $product->id)->where('is_primary', true)->first();
                if ($primaryImage) {
                    $primaryImage->update(['image_url' => '/storage/' . $path]);
                } else {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url' => '/storage/' . $path,
                        'is_primary' => true
                    ]);
                }
            }

            // 3. Update File PDF (Jika ada upload baru)
            if ($request->product_type == 'digital' && $request->hasFile('digital_file')) {
                $filePath = $request->file('digital_file')->store('digital_products', 'public');
                $product->update(['file_url' => '/storage/' . $filePath]);
            }

            DB::commit();
            return redirect()->route('shop.index')->with('success', 'Produk berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    // 5. Hapus Produk
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->shop_id !== Auth::user()->shop->id) {
            abort(403);
        }

        // Hapus file gambar & pdf dari storage (Disarankan)
        // ...

        // Hapus dari database (Cascade delete akan menghapus product_images juga)
        $product->delete();

        return redirect()->route('shop.index')->with('success', 'Produk berhasil dihapus.');
    }
}