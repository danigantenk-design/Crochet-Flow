@extends('layouts.seller') {{-- Gunakan Layout Seller --}}

@section('content')
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Dashboard Toko</h1>
            <p class="text-gray-500 mt-1">Halo, <span class="text-pink-600 font-bold">{{ $shop->name }}</span>! Semangat jualan hari ini.</p>
        </div>
        <a href="{{ route('products.create') }}" class="bg-pink-600 hover:bg-pink-700 text-white px-5 py-2.5 rounded shadow-lg font-bold transition">
            + Tambah Produk
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <a href="{{ route('shop.orders') }}" class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-yellow-400 hover:shadow-md transition group cursor-pointer">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Pesanan Baru</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $ordersCount ?? 0 }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full group-hover:bg-yellow-200 transition">
                    📦
                </div>
            </div>
            <div class="mt-4 text-xs font-bold text-yellow-600 flex items-center">
                Lihat Pesanan &rarr;
            </div>
        </a>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-green-500">
            <p class="text-gray-500 text-sm font-medium">Total Pendapatan</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">Rp {{ number_format($income ?? 0, 0, ',', '.') }}</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-pink-500">
            <p class="text-gray-500 text-sm font-medium">Total Produk</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $products->count() }}</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-blue-500">
            <p class="text-gray-500 text-sm font-medium">Item Terjual</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $products->sum('sold_count') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Produk Terbaru</h3>
            <a href="#" class="text-sm text-pink-600 font-bold hover:underline">Lihat Semua</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="p-4">Produk</th>
                        <th class="p-4">Harga</th>
                        <th class="p-4 text-center">Stok</th>
                        <th class="p-4 text-center">Terjual</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($products->take(5) as $product)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 flex items-center gap-3">
                            <img src="{{ $product->images->first()->image_url ?? '' }}" class="w-10 h-10 rounded object-cover border">
                            <span class="font-medium text-gray-800">{{ $product->name }}</span>
                        </td>
                        <td class="p-4 text-gray-600">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td class="p-4 text-center">
                            <span class="px-2 py-1 rounded text-xs font-bold {{ $product->stock > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td class="p-4 text-center text-gray-500">{{ $product->sold_count }}</td>
                        <td class="p-4 text-right">
                            <a href="{{ route('products.edit', $product->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-bold mr-2">Edit</a>
                            
                            <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:text-red-800 text-sm font-bold">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-400">Belum ada produk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection