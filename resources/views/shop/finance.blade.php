@extends('layouts.seller')

@section('content')
<div class="container mx-auto px-4 py-8">

    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            💰 Keuangan Toko
        </h2>
        <p class="text-gray-500 text-sm">Kelola pendapatan dan penarikan danamu.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        
        {{-- KARTU SALDO UTAMA --}}
        <div class="md:col-span-2 bg-gradient-to-r from-blue-600 to-blue-500 rounded-2xl p-8 text-white shadow-lg relative overflow-hidden">
            <div class="absolute right-0 top-0 opacity-10 transform translate-x-10 -translate-y-10">
                <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/><path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/></svg>
            </div>
            
            <p class="text-blue-100 font-medium mb-1">Saldo Aktif Saat Ini</p>
            <h3 class="text-4xl font-bold mb-6">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</h3>
            
            <div class="flex gap-3">
                <a href="{{ route('shop.withdraw.create') }}" class="bg-white text-blue-600 px-6 py-2 rounded-lg font-bold shadow hover:bg-gray-50 transition inline-block">
                    💸 Tarik Dana (Withdraw)
                </a>
                <a href="{{ route('shop.edit') }}" class="bg-blue-700 text-white px-4 py-2 rounded-lg font-bold border border-blue-400 hover:bg-blue-600 transition">
                    ⚙️ Pengaturan Rekening
                </a>
            </div>
        </div>

        {{-- KARTU RINGKASAN (Placeholder) --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-center items-center text-center">
            <div class="p-3 bg-green-100 text-green-600 rounded-full mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </div>
            <h4 class="text-gray-500 text-sm font-bold uppercase">Total Pemasukan</h4>
            <p class="text-2xl font-bold text-gray-800 mt-1">
                {{-- Hitung manual income yg masuk --}}
                Rp {{ number_format($transactions->where('type', 'credit')->sum('amount'), 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- TABEL RIWAYAT TRANSAKSI --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">📜 Riwayat Mutasi</h3>
        </div>
        
        @if($transactions->isEmpty())
            <div class="text-center py-12">
                <p class="text-gray-400">Belum ada transaksi.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-bold">
                        <tr>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Keterangan</th>
                            <th class="px-6 py-3">Tipe</th>
                            <th class="px-6 py-3 text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach($transactions as $trx)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-500">
                                {{ $trx->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-800">
                                {{ $trx->description }}
                            </td>
                            <td class="px-6 py-4">
                                @if($trx->type == 'credit')
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">Uang Masuk</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-bold">Penarikan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-bold {{ $trx->type == 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $trx->type == 'credit' ? '+' : '-' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection