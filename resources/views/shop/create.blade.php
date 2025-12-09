@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow">
        <h2 class="text-2xl font-bold mb-6">Tambah Produk Baru</h2>

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Nama Produk</label>
                    <input type="text" name="name" class="w-full border rounded p-2" required value="{{ old('name') }}">
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Kategori</label>
                    <select name="category_id" class="w-full border rounded p-2" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Harga (Rp)</label>
                    <input type="number" name="price" class="w-full border rounded p-2" required value="{{ old('price') }}">
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Stok</label>
                    <input type="number" name="stock" class="w-full border rounded p-2" required value="{{ old('stock') }}">
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Berat (Gram)</label>
                    <input type="number" name="weight" class="w-full border rounded p-2" required value="{{ old('weight') }}">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Tipe Produk</label>
                <select name="product_type" id="product_type" class="w-full border rounded p-2">
                    <option value="physical">Fisik (Barang Jadi/Benang)</option>
                    <option value="digital">Digital (File PDF/Pola)</option>
                </select>
            </div>

            <div class="mb-4 hidden" id="digital_upload_div">
                <label class="block text-blue-700 font-bold mb-2">Upload File PDF (Max 10MB)</label>
                <input type="file" name="digital_file" accept=".pdf" class="w-full border border-blue-300 rounded p-2 bg-blue-50">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Deskripsi Produk</label>
                <textarea name="description" rows="5" class="w-full border rounded p-2" required>{{ old('description') }}</textarea>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">Foto Utama Produk</label>
                <input type="file" name="image" accept="image/*" class="w-full border rounded p-2" required>
            </div>

            <button type="submit" class="bg-pink-600 text-white font-bold py-2 px-6 rounded hover:bg-pink-700">Simpan Produk</button>
        </form>
    </div>
</div>

<script>
    // Script sederhana untuk show/hide input PDF
    const typeSelect = document.getElementById('product_type');
    const digitalDiv = document.getElementById('digital_upload_div');

    typeSelect.addEventListener('change', function() {
        if(this.value === 'digital') {
            digitalDiv.classList.remove('hidden');
        } else {
            digitalDiv.classList.add('hidden');
        }
    });
</script>
@endsection