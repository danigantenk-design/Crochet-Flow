@extends('layouts.seller')

@section('content')
<div class="container mx-auto px-4 py-8">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">🧶 Produk Saya</h2>
            <p class="text-sm text-gray-500">Kelola katalog produk tokomu di sini.</p>
        </div>
        <a href="{{ route('products.create') }}" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded-lg shadow-sm transition flex items-center gap-2 text-sm">
            <span>+</span> Tambah Produk
        </a>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        @if($products->isEmpty())
            <div class="p-12 text-center flex flex-col items-center justify-center">
                <div class="text-6xl mb-4">🧶</div>
                <h3 class="text-lg font-bold text-gray-900">Belum ada produk</h3>
                <p class="text-gray-500 mt-2 mb-6">Kamu belum memajang produk apapun di tokomu.</p>
                <a href="{{ route('products.create') }}" class="text-pink-600 font-bold hover:underline">
                    Mulai Tambah Produk Sekarang &rarr;
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="p-4 font-bold">Produk</th>
                            <th class="p-4 font-bold">Kategori</th>
                            <th class="p-4 font-bold">Harga</th>
                            <th class="p-4 font-bold">Stok</th>
                            <th class="p-4 font-bold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach($products as $product)
                        <tr class="hover:bg-gray-50 transition">
                            
                            {{-- Kolom Produk (Gambar + Nama) --}}
                            <td class="p-4 align-middle">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded bg-gray-100 overflow-hidden border border-gray-200 flex-shrink-0">
                                        {{-- Pakai Accessor image_url --}}
                                        <img src="{{ $product->image_url }}" fetchpriority="high" loading="lazy" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800">{{ $product->name }}</div>
                                        <div class="text-xs text-gray-400">Berat: {{ $product->weight }}gr</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Kategori --}}
                            <td class="p-4 align-middle">
                                <span class="bg-gray-100 text-gray-600 py-1 px-2 rounded text-xs font-bold">
                                    {{ $product->category->name ?? 'Tanpa Kategori' }}
                                </span>
                            </td>

                            {{-- Harga --}}
                            <td class="p-4 align-middle font-bold text-gray-700">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </td>

                            {{-- Stok --}}
                            <td class="p-4 align-middle">
                                @if($product->stock > 0)
                                    <span class="text-green-600 font-bold">{{ $product->stock }} pcs</span>
                                @else
                                    <span class="text-red-600 font-bold bg-red-100 px-2 py-1 rounded text-xs">Habis</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="p-4 align-middle text-center">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Tombol Lihat (Ke Halaman Depan) --}}
                                    <a href="{{ route('products.show', $product->slug) }}" target="_blank" class="text-gray-400 hover:text-gray-600 p-2" title="Lihat di Web">
                                        👁️
                                    </a>

                                    {{-- Tombol Edit --}}
                                    <a href="{{ route('products.edit', $product->id) }}" class="text-blue-500 hover:text-blue-700 p-2" title="Edit">
                                        ✏️
                                    </a>

                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Yakin hapus produk ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 p-2" title="Hapus">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection