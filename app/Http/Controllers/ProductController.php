<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // MENAMPILKAN SEMUA PRODUK (Biasanya untuk Pembeli)
    public function index()
    {
        // Tambahkan with('reviews') agar rating bisa dipanggil dengan cepat
        $products = Product::with('reviews')->where('is_active', 1)->latest()->get();
        return view('products.index', compact('products'));
    }

    // TAMPILKAN DETAIL PRODUK
    public function show($slug)
    {
        // Ambil produk beserta ulasannya
        $product = Product::with(['reviews.user', 'shop'])->where('slug', $slug)->firstOrFail();
        return view('products.show', compact('product'));
    }

    // 1. TAMPILKAN FORM TAMBAH PRODUK (SELLER)
    public function create()
    {
        if (!Auth::user()->shop) {
            return redirect()->route('shop.create');
        }

        $categories = Category::all();
        return view('shop.products.create', compact('categories'));
    }

    // 2. SIMPAN PRODUK BARU
    public function store(Request $request)
    {
        if (!Auth::user()->shop) {
            return redirect()->route('shop.create');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'weight' => 'required_if:product_type,physical|nullable|numeric',
            'product_type' => 'required|in:physical,digital',
            'digital_file' => 'nullable|file|mimes:pdf,zip,rar|max:20480',
            'description' => 'required|string',
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $digitalPath = null;
            if ($request->hasFile('digital_file') && $request->product_type === 'digital') {
                $digitalPath = $request->file('digital_file')->store('digital-products', 'public');
            }

            // 1. Simpan Data Produk Utama
            $product = Product::create([
                'shop_id' => Auth::user()->shop->id,
                'category_id' => $request->category_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name) . '-' . time(),
                'price' => $request->price,
                'stock' => $request->stock,
                'weight' => $request->product_type === 'digital' ? 0 : ($request->weight ?? 0),
                'product_type' => $request->product_type,
                'file_path' => $digitalPath,
                'file_url' => $digitalPath,
                'description' => $request->description,
                'is_active' => true,
            ]);

            // 2. Simpan Banyak Gambar ke public/images/product/{kategori}
            if ($request->hasFile('images')) {
                $category = Category::find($request->category_id);
                $folderName = Str::slug($category->name ?? 'umum');

                foreach ($request->file('images') as $key => $image) {
                    $fileName = time() . '_' . Str::random(5) . '.' . $image->getClientOriginalExtension();
                    
                    // Pindahkan file fisik ke folder public/images/product/{nama-kategori}
                    $image->move(public_path("images/product/{$folderName}"), $fileName);
                    
                    // Simpan path ke tabel product_images kolom image_url
                    $product->images()->create([
                        'image_url' => "product/{$folderName}/{$fileName}",
                        'is_primary' => ($key === 0) ? true : false,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('shop.index')->with('success', 'Produk berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    // 3. TAMPILKAN FORM EDIT
    public function edit($id)
    {
        if (!Auth::user()->shop) {
            return redirect()->route('shop.create');
        }

        $shopId = Auth::user()->shop->id;
        $product = Product::where('shop_id', $shopId)->findOrFail($id);
        
        $categories = Category::all();
        return view('shop.products.edit', compact('product', 'categories'));
    }

    // 4. UPDATE PRODUK
    public function update(Request $request, $id)
    {
        if (!Auth::user()->shop) {
            return redirect()->route('shop.create');
        }

        $shopId = Auth::user()->shop->id;
        $product = Product::where('shop_id', $shopId)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:100',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'digital_file' => 'nullable|file|mimes:pdf,zip,rar|max:20480',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(5),
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ];

        if ($request->hasFile('digital_file')) {
            if ($product->file_path && Storage::disk('public')->exists($product->file_path)) {
                Storage::disk('public')->delete($product->file_path);
            }
            $digitalPath = $request->file('digital_file')->store('digital-products', 'public');
            $data['file_path'] = $digitalPath;
            $data['file_url'] = $digitalPath;
        }

        if ($request->hasFile('image')) {
            $category = Category::find($request->category_id);
            $folderName = Str::slug($category->name ?? 'umum');
            $imageFile = $request->file('image');
            $fileName = time() . '_' . Str::random(5) . '.' . $imageFile->getClientOriginalExtension();
            $imageFile->move(public_path("images/product/{$folderName}"), $fileName);

            $primaryImage = $product->images()->where('is_primary', true)->first() ?: $product->images()->first();
            if ($primaryImage) {
                if (file_exists(public_path("images/{$primaryImage->image_url}"))) {
                    @unlink(public_path("images/{$primaryImage->image_url}"));
                }
                $primaryImage->update([
                    'image_url' => "product/{$folderName}/{$fileName}",
                    'is_primary' => true
                ]);
            } else {
                $product->images()->create([
                    'image_url' => "product/{$folderName}/{$fileName}",
                    'is_primary' => true
                ]);
            }
        }

        $product->update($data);
        return redirect()->route('shop.index')->with('success', 'Produk berhasil diperbarui!');
    }

    // 5. HAPUS PRODUK
    public function destroy($id)
    {
        if (!Auth::user()->shop) {
            return redirect()->route('shop.create');
        }

        $shopId = Auth::user()->shop->id;
        $product = Product::where('shop_id', $shopId)->findOrFail($id);

        if ($product->file_path && Storage::disk('public')->exists($product->file_path)) {
            Storage::disk('public')->delete($product->file_path);
        }

        foreach ($product->images as $img) {
            if (file_exists(public_path("images/{$img->image_url}"))) {
                @unlink(public_path("images/{$img->image_url}"));
            }
            $img->delete();
        }

        $product->delete();
        return back()->with('success', 'Produk berhasil dihapus.');
    }
}