@extends('layouts.admin')
@section('title', 'Detail Pesanan #' . $order->invoice_number)

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('admin.orders.history') }}" class="text-gray-500 hover:text-pink-600 font-bold text-sm">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Riwayat
        </a>
        <span class="bg-gray-100 px-4 py-2 rounded-lg text-xs font-bold uppercase">{{ $order->status }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Daftar Barang --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Item Pesanan</h3>
                @foreach($order->items as $item)
                <div class="flex gap-4 mb-4 pb-4 border-b last:border-0">
                    <img src="{{ $item->product->image_url }}" fetchpriority="high" loading="lazy" class="w-16 h-16 rounded object-cover bg-gray-50">
                    <div class="flex-1">
                        <p class="font-bold text-sm text-gray-800">{{ $item->product->name }}</p>
                        <p class="text-xs text-gray-500">{{ $item->quantity }} x Rp {{ number_format($item->price_at_purchase) }}</p>
                    </div>
                    <div class="text-sm font-bold text-gray-700">
                        Rp {{ number_format($item->price_at_purchase * $item->quantity) }}
                    </div>
                </div>
                @endforeach
            </div>


        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Rincian Biaya</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal Produk</span>
                        <span>Rp {{ number_format($order->total_price - $order->shipping_cost) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Ongkos Kirim</span>
                        <span>Rp {{ number_format($order->shipping_cost) }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-gray-800 pt-2 border-t">
                        <span>Total Bayar</span>
                        <span>Rp {{ number_format($order->total_price) }}</span>
                    </div>
                    
                    {{-- Rincian Komisi Platform (Hanya untuk Admin) --}}
                    <div class="mt-4 p-3 bg-pink-50 rounded-lg border border-pink-100">
                        <div class="flex justify-between text-xs font-bold text-pink-700 uppercase">
                            <span>Komisi Platform</span>
                            <span>Rp {{ number_format($order->getTotalCommission()) }}</span>
                        </div>
                        <p class="text-[10px] text-pink-500 mt-1 italic">*10% Fisik, 15% Digital</p>
                    </div>
                </div>
            </div>
        </div>

    <div class="space-y-6">
        {{-- Informasi Pembeli & Toko --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Rincian Transaksi</h3>
            
            <div class="mb-4">
                <p class="text-xs font-bold text-gray-400 uppercase">Pembeli:</p>
                <p class="text-sm font-bold text-gray-800">{{ $order->user->full_name }}</p>
                <p class="text-xs text-gray-500">{{ $order->user->email }}</p>
            </div>

            <div class="mb-4">
                <p class="text-xs font-bold text-gray-400 uppercase">Penjual / Toko:</p>
                <p class="text-sm font-bold text-pink-600">{{ $order->shop->name }}</p>
            </div>

            <div class="mb-4">
                <p class="text-xs font-bold text-gray-400 uppercase">Ekspedisi:</p>
                {{-- Menampilkan nama ekspedisi jika ada, jika tidak (digital) beri keterangan --}}
                <p class="text-sm font-medium text-gray-700">
                    {{ $order->shipping_cost > 0 ? ($order->courier_name ?? 'Reguler') : 'Produk Digital' }}
                </p>
            </div>
            
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase">Alamat Pengiriman:</p>
                <p class="text-xs text-gray-600 leading-relaxed italic">{{ $order->shipping_address_snapshot }}</p>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection