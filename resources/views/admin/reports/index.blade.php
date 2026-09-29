@extends('layouts.admin')
@section('title', 'Laporan Penjualan')

@section('content')
<div class="container mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">📊 Laporan Penjualan Bulanan</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($reports as $report)
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-start mb-4">
                <span class="text-xs font-bold text-pink-600 bg-pink-50 px-2 py-1 rounded">{{ $report->month }}</span>
                <i class="fa-solid fa-chart-line text-gray-300 text-xl"></i>
            </div>
            <p class="text-gray-500 text-xs uppercase font-bold tracking-widest">Total Pendapatan</p>
            <h3 class="text-2xl font-extrabold text-gray-800">Rp {{ number_format($report->revenue, 0, ',', '.') }}</h3>
            <div class="mt-4 pt-4 border-t border-gray-50 flex justify-between items-center text-xs">
                <span class="text-gray-400">Total Transaksi Selesai:</span>
                <span class="font-bold text-gray-700">{{ $report->total_sales }} Pesanan</span>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection