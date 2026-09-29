@extends('layouts.admin')
@section('title', 'Riwayat Penarikan')

@section('content')
<div class="container mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">⌛ Riwayat Penarikan Dana</h1>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs font-bold uppercase">
                <tr>
                    <th class="px-6 py-4">Tanggal</th>
                    <th class="px-6 py-4">Toko / User</th>
                    <th class="px-6 py-4 text-right">Jumlah</th>
                    <th class="px-6 py-4 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($withdrawals as $wd)
                <tr>
                    <td class="px-6 py-4 text-gray-500">{{ $wd->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 font-bold text-gray-800">{{ $wd->user->shop->name ?? $wd->user->name }}</td>
                    <td class="px-6 py-4 text-right font-bold text-green-600">Rp {{ number_format($wd->amount, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold 
                            {{ $wd->status == 'approved' ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700' }}">
                            {{ strtoupper($wd->status) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $withdrawals->links() }}</div>
    </div>
</div>
@endsection