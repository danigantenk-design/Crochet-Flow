@extends('layouts.seller') {{-- Menggunakan Layout Seller (Ada Sidebar) --}}

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">📦 Kelola Pesanan</h2>
            <p class="text-sm text-gray-500">Daftar pesanan yang masuk ke tokomu.</p>
        </div>
        <a href="{{ route('shop.index') }}" class="text-gray-500 hover:text-pink-600 font-bold text-sm flex items-center">
            &larr; Kembali ke Dashboard
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
        @if($orders->isEmpty())
            <div class="p-12 text-center">
                <div class="text-6xl mb-4">📭</div>
                <h3 class="text-lg font-medium text-gray-900">Belum ada pesanan</h3>
                <p class="text-gray-500 mt-1">Sabar ya, pesanan akan segera datang!</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="p-4 font-semibold">Invoice</th>
                            <th class="p-4 font-semibold">Pembeli</th>
                            <th class="p-4 font-semibold">Total</th>
                            <th class="p-4 font-semibold">Status</th>
                            <th class="p-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($orders as $order)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4">
                                <span class="font-mono text-sm font-bold text-pink-600">{{ $order->invoice_number }}</span>
                            </td>
                            <td class="p-4">
                                <p class="font-bold text-gray-800">{{ $order->user->full_name }}</p>
                                <p class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
                            </td>
                            <td class="p-4 font-bold text-gray-700">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </td>
                            <td class="p-4">
                                @if($order->status == 'processing')
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-bold border border-blue-200">
                                        📦 Perlu Dikirim
                                    </span>
                                @elseif($order->status == 'shipped')
                                    <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-xs font-bold border border-purple-200">
                                        🚚 Dikirim
                                    </span>
                                @elseif($order->status == 'completed')
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold border border-green-200">
                                        ✅ Selesai
                                    </span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-bold border border-yellow-200">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('shop.orders.show', $order->id) }}" class="inline-block bg-gray-900 text-white px-4 py-2 rounded-md text-sm font-bold hover:bg-gray-800 transition shadow-sm">
                                    Kelola Pesanan
                                </a>
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