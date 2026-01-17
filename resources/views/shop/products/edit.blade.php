@extends('layouts.seller')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-gray-100">
        
        <div class="mb-6 border-b border-gray-100 pb-4">
            <h2 class="text-2xl font-bold text-gray-800">✏️ Edit Produk</h2>
            <p class="text-gray-500 text-sm">Perbarui informasi produk: {{ $product->name }}</p>
        </div>

        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Nama Produk --}}
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Produk</label>
                    <input type="text" name="name" class="w-full border-gray-300 rounded-lg focus:ring-pink-500" required value="{{ old('name', $product->name) }}">
                </div>

                {{-- Kategori --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Kategori</label>
                    <select name="category_id" class="w-full border-gray-300 rounded-lg focus:ring-pink-500" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Aktif --}}
                <div class="flex items-end pb-2">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Tampilkan Produk</span>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Harga --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Harga (Rp)</label>
                    <input type="number" name="price" class="w-full border-gray-300 rounded-lg focus:ring-pink-500" required value="{{ old('price', $product->price) }}">
                </div>
                {{-- Stok --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Stok</label>
                    <input type="number" name="stock" class="w-full border-gray-300 rounded-lg focus:ring-pink-500" required value="{{ old('stock', $product->stock) }}">
                </div>
            </div>

            {{-- Deskripsi --}}
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi Produk</label>
                <textarea name="description" rows="5" class="w-full border-gray-300 rounded-lg focus:ring-pink-500">{{ old('description', $product->description) }}</textarea>
            </div>

            {{-- Ganti Foto --}}
            <div class="mb-8 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <label class="block text-sm font-bold text-gray-700 mb-2">Foto Produk</label>
                <div class="flex items-center gap-4">
                    {{-- Preview Gambar Lama --}}
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="w-20 h-20 object-cover rounded-lg border bg-white">
                    @endif
                    
                    <div class="flex-1">
                        <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-pink-100 file:text-pink-700 hover:file:bg-pink-200">
                        <p class="text-xs text-gray-400 mt-1">Biarkan kosong jika tidak ingin mengubah foto.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('shop.index') }}" class="px-6 py-2 rounded-lg text-gray-600 font-bold hover:bg-gray-100 transition">Batal</a>
                <button type="submit" class="bg-pink-600 text-white font-bold py-2 px-8 rounded-lg hover:bg-pink-700 shadow-md transition">
                    💾 Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection