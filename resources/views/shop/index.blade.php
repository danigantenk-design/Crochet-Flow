@extends('layouts.seller')

@section('content')
<div class="container mx-auto px-4 py-8">

    {{-- 1. HEADER & STATISTIK --}}
    <div class="grid grid-cols-1 md:grid-cols-1 gap-6 mb-8">
        
        {{-- Kartu Profil Toko --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex flex-col items-center text-center">
            <div class="mb-4 relative">
                @if($shop->image)
                    <img src="{{ asset('storage/'.$shop->image) }}" fetchpriority="high" loading="lazy" class="w-20 h-20 rounded-full object-cover border-4 border-pink-50">
                @else
                    <div class="w-20 h-20 rounded-full bg-pink-100 text-pink-600 flex items-center justify-center text-3xl font-bold border-4 border-pink-50">
                        {{ substr($shop->name, 0, 1) }}
                    </div>
                @endif
                <div class="absolute bottom-0 right-0 bg-green-500 w-5 h-5 rounded-full border-2 border-white" title="Online"></div>
            </div>
            <h2 class="text-xl font-bold text-gray-800">{{ $shop->name }}</h2>
            <p class="text-sm text-gray-500 mb-2">{{ $shop->city_id }} (ID Lokasi)</p>
            
            <div class="mt-2">
                @if($shop->is_verified)
                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1 mx-auto w-max">
                        <i class="fa-solid fa-circle-check"></i> Terverifikasi
                    </span>
                @else
                    <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1 mx-auto w-max">
                        <i class="fa-solid fa-clock"></i> Menunggu Verifikasi
                    </span>
                @endif
            </div>
        </div>

        {{-- Kartu Statistik Pendapatan --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            
            <div class="bg-gradient-to-br from-pink-500 to-rose-600 p-6 rounded-2xl text-white shadow-lg">
                <p class="text-xs font-bold uppercase opacity-80">Saldo Bisa Ditarik</p>
                <h3 class="text-3xl font-extrabold mt-2">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</h3>
                <a href="{{ route('shop.finance') }}" class="mt-4 inline-block text-xs bg-white text-pink-600 px-4 py-2 rounded-lg font-bold hover:bg-gray-100 transition">
                    Lihat Keuangan →
                </a>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <p class="text-gray-400 text-xs font-bold uppercase">Perlu Dikemas</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-2">{{ $ordersCount }} Pesanan</h3>
                <a href="{{ route('shop.orders') }}" class="text-blue-500 text-xs font-bold mt-2 inline-block">Proses Sekarang →</a>
            </div>

        </div>

    </div>
    

    {{-- 2. DAFTAR PRODUK --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        <div class="px-6 py-5 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="font-bold text-xl text-gray-800 flex items-center gap-2">
                📦 Daftar Produk Saya
            </h3>
            
            {{-- Tombol Tambah Produk --}}
            <a href="{{ route('products.create') }}" class="bg-pink-600 hover:bg-pink-700 text-white px-5 py-2.5 rounded-lg font-bold shadow-md hover:shadow-lg transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Produk Baru
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 m-6 mb-0 rounded shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($products->isEmpty())
            <div class="text-center py-16">
                <div class="mb-4">
                    <i class="fa-solid fa-box-open text-6xl text-gray-200"></i>
                </div>
                <h4 class="text-lg font-bold text-gray-600">Belum ada produk</h4>
                <p class="text-gray-400 mb-6">Mulai jualan dengan menambahkan produk pertamamu!</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold tracking-wider">
                        <tr>
                            <th class="p-4">Foto</th>
                            <th class="p-4">Nama Produk</th>
                            <th class="p-4">Harga & Stok</th>
                            <th class="p-4">Kategori</th>
                            <th class="p-4 text-center">Status</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach($products as $product)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4">
                                <img src="{{ $product->image_url }}" fetchpriority="high" loading="lazy" class="w-16 h-16 rounded-lg object-cover border bg-white">
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-gray-800">{{ $product->name }}</div>
                                <div class="text-xs text-gray-500 truncate max-w-xs">{{ Str::limit($product->description, 40) }}</div>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-pink-600">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                                <div class="text-xs text-gray-500">Stok: {{ $product->stock }}</div>
                            </td>
                            <td class="p-4">
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-medium">
                                    {{ $product->category->name ?? 'Uncategorized' }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                @if($product->is_active)
                                    <span class="text-green-600 bg-green-100 px-2 py-1 rounded-full text-xs font-bold">Aktif</span>
                                @else
                                    <span class="text-gray-500 bg-gray-100 px-2 py-1 rounded-full text-xs font-bold">Arsip</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('products.edit', $product->id) }}" class="text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-2 rounded transition" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Yakin hapus produk ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded transition" title="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection