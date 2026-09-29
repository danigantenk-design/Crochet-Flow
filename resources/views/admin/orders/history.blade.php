@extends('layouts.admin')
@section('title', 'Riwayat Pesanan')

@section('content')
<div class="container mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">📦 Riwayat Semua Pesanan</h1>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs font-bold uppercase">
                <tr>
                    <th class="px-6 py-4">Invoice</th>
                    <th class="px-6 py-4">Pembeli</th>
                    <th class="px-6 py-4">Toko</th>
                    <th class="px-6 py-4">Total</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($orders as $order)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-bold text-pink-600">{{ $order->invoice_number }}</td>
                    <td class="px-6 py-4">{{ $order->user->full_name ?? 'User Tidak Ditemukan' }}</td>
                    <td class="px-6 py-4">{{ $order->shop->name }}</td>
                    <td class="px-6 py-4 font-bold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold 
                            {{ $order->status == 'completed' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ strtoupper($order->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('admin.orders.detail', $order->id) }}" class="text-blue-600 hover:text-blue-800 font-bold text-xs">
                            Detail
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $orders->links() }}</div>
    </div>
</div>
@endsection