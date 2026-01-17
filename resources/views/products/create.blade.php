@extends('layouts.seller')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">✨ Tambah Produk Baru</h2>
        <a href="{{ route('shop.index') }}" class="text-gray-500 hover:text-pink-600 font-bold text-sm">
            &larr; Batal
        </a>
    </div>

    {{-- Tampilkan Error Global jika ada --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <strong class="font-bold">Ups! Ada kesalahan:</strong>
            <ul class="list-disc list-inside mt-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8">
        
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                {{-- KIRI: Upload Gambar --}}
                <div x-data="{ imagePreview: null }">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Foto Produk <span class="text-red-500">*</span></label>
                    
                    <div class="w-full aspect-square bg-gray-50 rounded-xl border-2 border-dashed border-gray-300 flex flex-col items-center justify-center overflow-hidden relative hover:bg-gray-100 transition cursor-pointer group">
                        
                        <template x-if="imagePreview">
                            <img :src="imagePreview" class="w-full h-full object-cover">
                        </template>
                        
                        <template x-if="!imagePreview">
                            <div class="text-center p-4">
                                <span class="text-4xl mb-2 block">📷</span>
                                <span class="text-sm text-gray-400 font-medium">Klik untuk upload gambar</span>
                                <p class="text-xs text-gray-400 mt-1">(Max: 2MB)</p>
                            </div>
                        </template>

                        <input type="file" name="image" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*"
                               @change="imagePreview = URL.createObjectURL($event.target.files[0])">
                    </div>
                    @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- KANAN: Detail Produk --}}
                <div class="space-y-5">
                    
                    {{-- 1. Nama Produk --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-pink-200" placeholder="Contoh: Sweater Rajut Pola Bunga" required>
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- 2. Kategori (Dropdown dari Database) --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                        <select name="category_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-pink-200">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        
                        @if($categories->isEmpty())
                            <p class="text-xs text-yellow-600 mt-1">⚠️ Belum ada kategori. Jalankan seeder dulu!</p>
                        @endif
                    </div>

                    {{-- 3. Harga & Stok --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Harga (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="price" value="{{ old('price') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-pink-200" placeholder="0" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Stok <span class="text-red-500">*</span></label>
                            <input type="number" name="stock" value="{{ old('stock') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-pink-200" placeholder="0" required>
                        </div>
                    </div>
                    @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    @error('stock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                    {{-- 4. Berat (Gram) --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Berat (Gram) <span class="text-red-500">*</span></label>
                        <input type="number" name="weight" value="{{ old('weight', 200) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-pink-200" placeholder="Contoh: 200" required>
                        <p class="text-xs text-gray-400 mt-1">Digunakan untuk hitung ongkir.</p>
                        @error('weight') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                </div>
            </div>

            {{-- 5. Deskripsi (Full Width) --}}
            <div class="mt-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi Lengkap <span class="text-red-500">*</span></label>
                <textarea name="description" rows="5" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring-pink-200" placeholder="Jelaskan detail produkmu..." required>{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tombol Submit --}}
            <div class="pt-6 border-t border-gray-100 flex justify-end">
                <button type="submit" class="bg-pink-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-pink-700 transition shadow-lg transform active:scale-95 flex items-center gap-2">
                    <span>💾</span> Simpan Produk
                </button>
            </div>

        </form>
    </div>
</div>
@endsection