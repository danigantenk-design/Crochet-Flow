@extends('layouts.admin')

@section('title', 'Manajemen Toko')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h3 class="text-gray-700 text-3xl font-medium mb-6">🏪 Daftar Toko & Mitra</h3>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Info Toko</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemilik</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi Admin</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($shops as $shop)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($shop->image)
                                <img src="{{ asset('storage/'.$shop->image) }}" class="w-10 h-10 rounded object-cover border">
                            @else
                                <div class="w-10 h-10 bg-pink-100 text-pink-600 rounded flex items-center justify-center font-bold">
                                    {{ substr($shop->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <div class="text-sm font-bold text-gray-900">{{ $shop->name }}</div>
                                <div class="text-xs text-gray-500">City ID: {{ $shop->city_id }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $shop->user->name }}</div>
                        <div class="text-xs text-gray-500">{{ $shop->user->email }}</div>
                    </td>
                    <td class="px-6 py-4">
                        {{-- LOGIKA STATUS: Fokus pada is_active --}}
                        @if($shop->is_active)
                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800 flex items-center w-fit gap-1">
                                <i class="fa-solid fa-check-circle"></i> Aktif / Live
                            </span>
                        @else
                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-800 flex items-center w-fit gap-1">
                                <i class="fa-solid fa-clock"></i> Menunggu Persetujuan
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center items-center gap-3">
                            
                            {{-- TOMBOL SETUJUI: Hanya muncul jika is_active bernilai 0 atau false --}}
                            @if(!$shop->is_active)
                                <form action="{{ route('admin.shop.approve', $shop->id) }}" method="POST">
                                    @csrf 
                                    @method('PATCH')
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold px-4 py-1.5 rounded shadow-sm transition">
                                        Setujui
                                    </button>
                                </form>
                            @else
                                {{-- Jika sudah aktif, tampilkan teks centang hijau saja --}}
                                <span class="text-green-600 font-bold text-xs">
                                    <i class="fa-solid fa-check-double"></i> Berhasil Disetujui
                                </span>
                            @endif

                            {{-- TOMBOL LIHAT --}}
                            <a href="{{ route('shop.show', $shop->id) }}" target="_blank" class="text-blue-500 hover:text-blue-700 transition" title="Lihat Toko">
                                <i class="fa-solid fa-eye text-lg"></i>
                            </a>

                            {{-- TOMBOL HAPUS --}}
                            <form action="{{ route('admin.shops.delete', $shop->id) }}" method="POST" onsubmit="return confirm('Hapus toko ini secara permanen?');">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 transition" title="Hapus Toko">
                                    <i class="fa-solid fa-trash text-lg"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">
            {{ $shops->links() }}
        </div>
    </div>
</div>
@endsection