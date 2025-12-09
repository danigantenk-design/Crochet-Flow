@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow">
        <h2 class="text-2xl font-bold mb-6">Edit Produk: {{ $product->name }}</h2>

        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Nama Produk</label>
                    <input type="text" name="name" class="w-full border rounded p-2" required value="{{ old('name', $product->name) }}">
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Kategori</label>
                    <select name="category_id" class="w-full border rounded p-2" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Harga (Rp)</label>
                    <input type="number" name="price" class="w-full border rounded p-2" required value="{{ old('price', $product->price) }}">
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Stok</label>
                    <input type="number" name="stock" class="w-full border rounded p-2" required value="{{ old('stock', $product->stock) }}">
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Berat (Gram)</label>
                    <input type="number" name="weight" class="w-full border rounded p-2" required value="{{ old('weight', $product->weight) }}">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Deskripsi Produk</label>
                <textarea name="description" rows="5" class="w-full border rounded p-2" required>{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Ganti Foto Utama (Opsional)</label>
                <div class="flex items-center gap-4 mb-2">
                    <img src="{{ $product->images->first()->image_url ?? '' }}" class="w-16 h-16 object-cover border rounded">
                    <span class="text-sm text-gray-500">Foto saat ini</span>
                </div>
                <input type="file" name="image" accept="image/*" class="w-full border rounded p-2">
            </div>
            
            @if($product->product_type == 'digital')
                <div class="mb-6 p-4 bg-blue-50 rounded border border-blue-200">
                    <label class="block text-blue-800 font-bold mb-2">Update File PDF (Opsional)</label>
                    <p class="text-xs text-gray-600 mb-2">File saat ini: {{ basename($product->file_url ?? 'Belum ada file') }}</p>
                    <input type="file" name="digital_file" accept=".pdf" class="w-full border border-blue-300 rounded p-2">
                </div>
            @endif

            <button type="submit" class="bg-gray-800 text-white font-bold py-2 px-6 rounded hover:bg-gray-700">Update Produk</button>
        </form>
    </div>
</div>
@endsection