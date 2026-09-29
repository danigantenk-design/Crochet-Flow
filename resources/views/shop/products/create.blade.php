@extends('layouts.seller')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">✨ Tambah Produk Baru</h2>
        <a href="{{ route('shop.index') }}" class="text-gray-500 hover:text-pink-600 font-bold text-sm">&larr; Batal</a>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8">
        {{-- Inisialisasi Alpine.js dengan x-data --}}
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="productUpload()">
            @csrf

            {{-- 1. PILIHAN TIPE PRODUK (Trigger utama untuk memunculkan field Berat) --}}
            <div class="bg-pink-50 p-4 rounded-lg border border-pink-100 mb-6">
                <label class="block text-sm font-bold text-pink-700 mb-2">Tipe Produk <span class="text-red-500">*</span></label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer bg-white px-4 py-2 rounded-md border hover:border-pink-300 transition">
                        <input type="radio" name="product_type" value="physical" x-model="productType" required> 
                        <span class="text-sm font-medium text-gray-700">📦 Produk Fisik (Komisi 10%)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer bg-white px-4 py-2 rounded-md border hover:border-pink-300 transition">
                        <input type="radio" name="product_type" value="digital" x-model="productType"> 
                        <span class="text-sm font-medium text-gray-700">💻 Produk Digital/Pola (Komisi 15%)</span>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                {{-- KIRI: MULTI UPLOAD GAMBAR --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Foto Produk (Maks 5) <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 gap-3">
                        {{-- Preview Gambar --}}
                        <template x-for="(image, index) in previews" :key="index">
                            <div class="relative aspect-square rounded-xl overflow-hidden border shadow-sm group">
                                <img :src="image" class="w-full h-full object-cover">
                                <button type="button" @click="removeImage(index)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center shadow hover:bg-red-600 transition">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                                <div x-if="index === 0" class="absolute bottom-0 inset-x-0 bg-black/50 text-[8px] text-white text-center py-1">UTAMA</div>
                            </div>
                        </template>
                        
                        {{-- Tombol Input (Hanya muncul jika < 5) --}}
                        <label x-show="previews.length < 5" class="aspect-square bg-gray-50 rounded-xl border-2 border-dashed border-gray-300 flex flex-col items-center justify-center relative hover:bg-gray-100 transition cursor-pointer group">
                            <span class="text-3xl text-gray-400 group-hover:scale-110 transition">+</span>
                            <span class="text-[10px] text-gray-400 mt-1">Upload</span>
                            {{-- PENTING: name="images[]" dan attribute multiple --}}
                            <input type="file" name="images[]" class="hidden" accept="image/*" @change="handleFiles($event)" multiple>
                        </label>
                    </div>
                    @error('images') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                </div>

                {{-- KANAN: DETAIL PRODUK --}}
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Produk</label>
                        <input type="text" name="name" class="w-full border-gray-300 rounded-lg text-sm focus:ring-pink-500 focus:border-pink-500" placeholder="Contoh: Sweater Rajut Pola Bunga" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Kategori</label>
                            <select name="category_id" class="w-full border-gray-300 rounded-lg text-sm" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- INPUT BERAT (Hanya muncul jika FISIK) --}}
                        <div x-show="productType === 'physical'" x-transition>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Berat (Gram)</label>
                            <input type="number" name="weight" value="200" class="w-full border-gray-300 rounded-lg text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Harga (Rp)</label>
                            <input type="number" name="price" class="w-full border-gray-300 rounded-lg text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Stok</label>
                            <input type="number" name="stock" class="w-full border-gray-300 rounded-lg text-sm" required>
                        </div>
                    </div>

                    {{-- FILE DIGITAL (Hanya muncul jika DIGITAL) --}}
                    <div x-show="productType === 'digital'" x-transition class="bg-blue-50 p-3 rounded-lg border border-blue-100">
                        <label class="block text-sm font-bold text-blue-700 mb-1">File Pola (PDF/ZIP)</label>
                        <input type="file" name="digital_file" class="w-full text-xs text-gray-500 file:bg-blue-600 file:text-white file:rounded-md file:border-0 file:px-3 file:py-1">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi Lengkap</label>
                <textarea name="description" rows="4" class="w-full border-gray-300 rounded-lg text-sm" required></textarea>
            </div>

            <div class="pt-6 border-t flex justify-end">
                <button type="submit" class="bg-pink-600 text-white font-bold py-2 px-10 rounded-lg hover:bg-pink-700 transition shadow-lg">💾 Simpan Produk</button>
            </div>
        </form>
    </div>
</div>

{{-- SCRIPT ALPINE JS --}}
<script>
    function productUpload() {
        return {
            productType: 'physical', // Default pilihan
            previews: [],
            handleFiles(event) {
                const files = event.target.files;
                for (let i = 0; i < files.length; i++) {
                    if (this.previews.length < 5) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.previews.push(e.target.result);
                        };
                        reader.readAsDataURL(files[i]);
                    }
                }
            },
            removeImage(index) {
                this.previews.splice(index, 1);
            }
        }
    }
</script>
@endsection