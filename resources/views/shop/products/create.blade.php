@extends('layouts.seller')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-gray-100">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">✨ Tambah Produk Baru</h2>
        <p class="text-gray-500 text-sm">Upload produk rajutan terbaikmu.</p>
    </div>

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Bagian Kiri: Info Dasar --}}
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Produk</label>
                    <input type="text" name="name" required class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500" placeholder="Contoh: Boneka Amigurumi Kucing">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Harga (Rp)</label>
                        <input type="number" name="price" required class="w-full border-gray-300 rounded-lg focus:ring-pink-500" placeholder="50000">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Stok</label>
                        <input type="number" name="stock" required class="w-full border-gray-300 rounded-lg focus:ring-pink-500" placeholder="10">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Kategori</label>
                    <select name="category_id" required class="w-full border-gray-300 rounded-lg focus:ring-pink-500">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Bagian Kanan: Gambar & Deskripsi --}}
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Foto Produk</label>
                    <input type="file" name="image" required accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100 border border-gray-300 rounded-lg cursor-pointer">
                    <p class="text-xs text-gray-400 mt-1">Format JPG/PNG, Maks 2MB.</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi Produk</label>
                    <textarea name="description" rows="5" class="w-full border-gray-300 rounded-lg focus:ring-pink-500" placeholder="Jelaskan detail ukuran, bahan, dan keunikan produk..."></textarea>
                </div>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end gap-4">
            <a href="{{ route('shop.index') }}" class="px-6 py-2 rounded-lg text-gray-600 font-bold hover:bg-gray-100 transition">Batal</a>
            <button type="submit" class="bg-pink-600 text-white px-8 py-2 rounded-lg font-bold hover:bg-pink-700 shadow-md transition">
                🚀 Simpan Produk
            </button>
        </div>
    </form>
</div>
@endsection