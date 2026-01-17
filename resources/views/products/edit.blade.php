@extends('layouts.seller')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">✏️ Edit Produk</h2>
        <a href="{{ route('shop.index') }}" class="text-gray-500 hover:text-pink-600 font-bold text-sm">
            &larr; Batal
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8">
        
        {{-- Perhatikan action route-nya ke UPDATE --}}
        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT') {{-- PENTING: Method PUT untuk update --}}

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                {{-- KIRI: Upload Gambar --}}
                <div x-data="{ imagePreview: '{{ $product->image_url }}' }">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Foto Produk</label>
                    
                    <div class="w-full aspect-square bg-gray-50 rounded-xl border-2 border-dashed border-gray-300 flex flex-col items-center justify-center overflow-hidden relative hover:bg-gray-100 transition cursor-pointer">
                        
                        <img :src="imagePreview" class="w-full h-full object-cover">

                        <input type="file" name="image" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*"
                               @change="imagePreview = URL.createObjectURL($event.target.files[0])">
                    </div>
                    <p class="text-xs text-gray-400 mt-2 text-center">Klik gambar untuk mengganti (Opsional)</p>
                    @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- KANAN: Detail Produk --}}
                <div class="space-y-5">
                    
                    {{-- Nama --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Produk</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-pink-200" required>
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Kategori</label>
                        <select name="category_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-pink-200">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Harga & Stok --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Harga (Rp)</label>
                            <input type="number" name="price" value="{{ old('price', $product->price) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-pink-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Stok</label>
                            <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-pink-200" required>
                        </div>
                    </div>

                    {{-- Berat --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Berat (Gram)</label>
                        <input type="number" name="weight" value="{{ old('weight', $product->weight) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-pink-200" required>
                    </div>

                </div>
            </div>

            {{-- Deskripsi --}}
            <div class="mt-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi Lengkap</label>
                <textarea name="description" rows="5" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-pink-200" required>{{ old('description', $product->description) }}</textarea>
            </div>

            {{-- Tombol Submit --}}
            <div class="pt-6 border-t border-gray-100 flex justify-end gap-3">
                <a href="{{ route('shop.index') }}" class="px-6 py-3 rounded-lg text-gray-600 hover:bg-gray-100 font-bold transition">Batal</a>
                <button type="submit" class="bg-blue-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-blue-700 transition shadow-lg transform active:scale-95 flex items-center gap-2">
                    <span>💾</span> Update Produk
                </button>
            </div>

        </form>
    </div>
</div>
@endsection