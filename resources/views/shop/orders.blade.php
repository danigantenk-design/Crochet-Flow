@extends('layouts.seller')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">📦 Pesanan Masuk</h2>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded mb-6 border-l-4 border-green-500">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- TAB HEADER (Filter Status Sederhana) --}}
        <div class="flex border-b border-gray-100 bg-gray-50 overflow-x-auto">
            <button class="px-6 py-4 text-sm font-bold text-pink-600 border-b-2 border-pink-600 bg-white">
                Semua Pesanan
            </button>
            {{-- Kamu bisa tambahkan logika filter status disini nanti --}}
        </div>

        @if($orders->isEmpty())
            <div class="p-12 text-center">
                <i class="fa-solid fa-clipboard-list text-6xl text-gray-200 mb-4"></i>
                <h3 class="text-gray-500 font-bold">Belum ada pesanan masuk</h3>
                <p class="text-gray-400 text-sm">Sabar ya, rejeki nggak kemana!</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold tracking-wider">
                        <tr>
                            <th class="p-4">No. Invoice</th>
                            <th class="p-4">Pembeli</th>
                            <th class="p-4">Total Bayar</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Tanggal</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach($orders as $order)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 font-mono font-bold text-pink-600">
                                #{{ $order->invoice_number }}
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-gray-800">{{ $order->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $order->items->count() }} Produk</div>
                            </td>
                            <td class="p-4 font-bold text-gray-800">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </td>
                            <td class="p-4">
                                @if($order->status == 'waiting_verification')
                                    <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs font-bold">Menunggu Konfirmasi</span>
                                @elseif($order->status == 'processing')
                                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-bold">Perlu Dikirim</span>
                                @elseif($order->status == 'shipped')
                                    <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded text-xs font-bold">Sedang Dikirim</span>
                                @elseif($order->status == 'completed')
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">Selesai</span>
                                @else
                                    <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-bold">{{ ucfirst($order->status) }}</span>
                                @endif
                            </td>
                            <td class="p-4 text-gray-500 text-xs">
                                {{ $order->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="p-4 text-center">
                                <a href="{{ route('shop.order.show', $order->id) }}" class="bg-pink-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-pink-700 transition shadow">
                                    Kelola
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="p-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection