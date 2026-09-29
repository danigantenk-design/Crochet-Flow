@extends('layouts.admin')
@section('content')
<div class="container mx-auto px-6 py-8">
    <h3 class="text-gray-700 text-3xl font-medium mb-6">🚚 Manajemen Ekspedisi</h3>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-8">
        <h4 class="font-bold text-gray-700 mb-4 text-sm uppercase">Tambah Ekspedisi Baru</h4>
        <form action="{{ route('admin.couriers.store') }}" method="POST" class="flex flex-wrap gap-4">
            @csrf
            <input type="text" name="code" placeholder="Kode (CONTOH: JNE)" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-pink-500" required>
            <input type="text" name="name" placeholder="Nama Lengkap Ekspedisi" class="border border-gray-300 rounded-lg px-4 py-2 text-sm flex-1" required>
            <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white px-6 py-2 rounded-lg font-bold transition shadow-sm">Simpan</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-gray-500 text-xs font-bold uppercase">
                <tr>
                    <th class="px-6 py-4">Kode</th>
                    <th class="px-6 py-4">Nama Ekspedisi</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($couriers as $c)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-mono font-bold text-pink-600">{{ $c->code }}</td>
                    <td class="px-6 py-4 text-gray-800">{{ $c->name }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold {{ $c->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $c->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right flex justify-end gap-2">
                        <form action="{{ route('admin.couriers.toggle', $c->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <button class="text-[10px] font-bold px-3 py-1 rounded border {{ $c->is_active ? 'text-red-500 border-red-200 hover:bg-red-50' : 'text-green-500 border-green-200 hover:bg-green-50' }}">
                                {{ $c->is_active ? 'Matikan' : 'Aktifkan' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection