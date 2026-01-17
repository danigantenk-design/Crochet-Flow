@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h3 class="text-gray-700 text-3xl font-medium mb-6">💰 Permintaan Penarikan Dana</h3>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded my-6 overflow-x-auto">
        <table class="min-w-full w-full table-auto">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Tanggal</th>
                    <th class="py-3 px-6 text-left">Toko / User</th>
                    <th class="py-3 px-6 text-left">Bank Tujuan</th>
                    <th class="py-3 px-6 text-right">Jumlah (Rp)</th>
                    <th class="py-3 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                @forelse($withdrawals as $wd)
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left whitespace-nowrap">
                        {{ $wd->created_at->format('d M Y H:i') }}
                    </td>
                    <td class="py-3 px-6 text-left">
                        <div class="flex items-center">
                            <span class="font-medium">{{ $wd->user->shop->name ?? 'User: ' . $wd->user->name }}</span>
                        </div>
                    </td>
                    <td class="py-3 px-6 text-left">
                        <p class="font-bold text-gray-800">{{ $wd->bank_name }}</p>
                        <p>{{ $wd->account_number }}</p>
                        <p class="text-xs text-gray-500">a.n {{ $wd->account_holder }}</p>
                    </td>
                    <td class="py-3 px-6 text-right font-bold text-green-600">
                        Rp {{ number_format($wd->amount, 0, ',', '.') }}
                    </td>
                    <td class="py-3 px-6 text-center">
                        <div class="flex item-center justify-center gap-2">
                            {{-- Tombol Terima --}}
                            <form action="{{ route('admin.withdrawals.approve', $wd->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" onclick="return confirm('Sudah transfer uangnya? Klik OK untuk konfirmasi.')" 
                                        class="bg-green-500 text-white px-3 py-1 rounded text-xs font-bold hover:bg-green-600 transition">
                                    ✅ Transfer
                                </button>
                            </form>

                            {{-- Tombol Tolak --}}
                            <form action="{{ route('admin.withdrawals.reject', $wd->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Tolak dan kembalikan saldo ke user?')" 
                                        class="bg-red-500 text-white px-3 py-1 rounded text-xs font-bold hover:bg-red-600 transition">
                                    ❌ Tolak
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-gray-400">
                        Tidak ada permintaan penarikan dana baru.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection