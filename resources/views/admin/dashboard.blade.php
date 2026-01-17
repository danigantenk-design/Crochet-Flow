@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    {{-- Header --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Halo, Admin! 👋</h1>
            <p class="text-gray-500 text-sm">Berikut ringkasan aktivitas marketplace hari ini.</p>
        </div>
        <div class="text-sm text-gray-500 bg-white px-4 py-2 rounded shadow-sm">
            {{ now()->format('l, d F Y') }}
        </div>
    </div>

    {{-- Kartu Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="bg-blue-100 p-3 rounded-full text-blue-600">
                <i class="fa-solid fa-users text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-xs font-bold uppercase">Total Pengguna</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $totalUsers }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="bg-purple-100 p-3 rounded-full text-purple-600">
                <i class="fa-solid fa-store text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-xs font-bold uppercase">Total Toko</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $totalShops }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="bg-yellow-100 p-3 rounded-full text-yellow-600">
                <i class="fa-solid fa-shopping-bag text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-xs font-bold uppercase">Total Pesanan</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $totalOrders }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="bg-green-100 p-3 rounded-full text-green-600">
                <i class="fa-solid fa-money-bill-wave text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-xs font-bold uppercase">Omzet Platform</p>
                <h3 class="text-xl font-bold text-green-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>

    {{-- Notifikasi Sukses --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r shadow-sm flex justify-between items-center">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
            <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">&times;</button>
        </div>
    @endif

    {{-- Tabel Pembayaran Masuk --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-100 bg-blue-50 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-lg text-blue-800 flex items-center gap-2">
                    💰 Verifikasi Pembayaran Masuk
                </h3>
                <p class="text-xs text-blue-600">Pastikan uang sudah masuk ke rekening Admin sebelum konfirmasi.</p>
            </div>
            @if($pendingPayments->count() > 0)
                <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full animate-pulse">
                    {{ $pendingPayments->count() }} Perlu Dicek
                </span>
            @endif
        </div>

        @if($pendingPayments->isEmpty())
            <div class="p-10 text-center text-gray-400">
                <i class="fa-regular fa-folder-open text-4xl mb-3"></i>
                <p>Tidak ada pembayaran baru yang perlu dicek.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-semibold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Invoice / Tanggal</th>
                            <th class="px-6 py-4">Info Pembeli & Toko</th>
                            <th class="px-6 py-4">Nominal Transfer</th>
                            <th class="px-6 py-4">Bukti</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($pendingPayments as $order)
                        <tr class="hover:bg-blue-50/30 transition">
                            <td class="px-6 py-4 align-top">
                                <div class="font-mono font-bold text-pink-600 text-sm">{{ $order->invoice_number }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    <i class="fa-regular fa-clock"></i> {{ $order->created_at->format('d M Y H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <div class="font-bold text-gray-800 text-sm">{{ $order->user->full_name }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    Beli dari: <span class="font-semibold text-gray-700">{{ $order->shop->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <div class="font-bold text-green-600 text-sm bg-green-50 px-2 py-1 rounded inline-block border border-green-100">
                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top">
                                @if($order->payment_proof)
                                    <a href="{{ Storage::url($order->payment_proof) }}" target="_blank" class="group flex items-center gap-2 text-blue-600 hover:text-blue-800 text-xs font-bold border border-blue-200 px-3 py-1 rounded-lg hover:bg-blue-50 transition w-max">
                                        <i class="fa-regular fa-image"></i> Lihat Struk
                                    </a>
                                @else
                                    <span class="text-red-500 text-xs italic">Tidak ada gambar</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 align-top text-right">
                                <form action="{{ route('admin.payment.confirm', $order->id) }}" method="POST" onsubmit="return confirm('⚠️ Pastikan uang Rp {{ number_format($order->total_price) }} SUDAH MASUK ke rekening Admin.\n\nLanjutkan konfirmasi?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-sm text-xs font-bold transition flex items-center gap-2 ml-auto">
                                        <i class="fa-solid fa-check-double"></i> Terima Uang
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Tabel Pengajuan Toko --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                🏪 Pengajuan Toko Baru
            </h3>
            @if($pendingShops->count() > 0)
                <span class="bg-yellow-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                    {{ $pendingShops->count() }} Pending
                </span>
            @endif
        </div>
        
        @if($pendingShops->isEmpty())
            <div class="p-10 text-center text-gray-400">
                <i class="fa-solid fa-shop text-4xl mb-3"></i>
                <p>Tidak ada pengajuan toko baru saat ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-semibold tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Nama Toko</th>
                            <th class="px-6 py-3">Pemilik</th>
                            <th class="px-6 py-3">Lokasi</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($pendingShops as $shop)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800">{{ $shop->name }}</div>
                                <div class="text-xs text-gray-500 truncate max-w-xs">{{ Str::limit($shop->description, 30) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium">{{ $shop->user->full_name }}</div>
                                <div class="text-xs text-gray-400">{{ $shop->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $shop->city_id }}</td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <form action="{{ route('admin.shop.reject', $shop->id) }}" method="POST" class="inline" onsubmit="return confirm('Tolak pengajuan toko ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-bold border border-red-200 hover:bg-red-50 px-3 py-1 rounded transition">
                                        Tolak
                                    </button>
                                </form>
                                <form action="{{ route('admin.shop.approve', $shop->id) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-xs font-bold hover:bg-blue-700 shadow transition">
                                        Setujui
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection