@extends('layouts.seller')

@section('content')
<div class="container mx-auto px-4 py-8">

    {{-- Tombol Kembali --}}
    <a href="{{ route('shop.finance') }}" class="inline-flex items-center text-gray-500 hover:text-pink-600 mb-6 transition font-medium text-sm">
        <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Keuangan
    </a>

    <div class="max-w-2xl mx-auto">
        
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">💸 Tarik Dana</h2>
            <p class="text-gray-500 mt-2">Cairkan pendapatan tokomu ke rekening bank.</p>
        </div>

        {{-- INFO SALDO --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-8 text-center">
            <p class="text-blue-600 font-bold uppercase text-xs tracking-wider mb-1">Saldo Tersedia</p>
            <h3 class="text-4xl font-extrabold text-gray-800">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</h3>
        </div>

        {{-- ALERT ERROR --}}
        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 text-red-700 p-4 rounded mb-6 border border-red-200">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM PENARIKAN --}}
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-8">
                <form action="{{ route('shop.withdraw.store') }}" method="POST">
                    @csrf
                    
                    {{-- Input Nominal --}}
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Jumlah Penarikan (Rp)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-500 font-bold">Rp</span>
                            <input type="number" name="amount" min="10000" max="{{ $wallet->balance }}" required 
                                placeholder="0"
                                class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent text-lg font-bold text-gray-800 transition">
                        </div>
                        <div class="flex justify-between items-center mt-2">
                            <p class="text-xs text-gray-400">*Minimal penarikan Rp 10.000</p>
                            <button type="button" onclick="document.querySelector('input[name=amount]').value = {{ $wallet->balance }}" class="text-xs text-pink-600 font-bold hover:underline">
                                Tarik Semua Saldo
                            </button>
                        </div>
                    </div>

                    <hr class="border-gray-100 my-6">

                    <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        🏦 Rekening Tujuan
                    </h4>

                    {{-- Ambil Data Toko untuk Autofill --}}
                    @php
                        $shop = Auth::user()->shop;
                    @endphp

                    {{-- Nama Bank --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nama Bank</label>
                        <select name="bank_name" class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-pink-500 focus:border-pink-500">
                            <option value="">-- Pilih Bank --</option>
                            @foreach(['BCA', 'BRI', 'BNI', 'Mandiri', 'BSI', 'CIMB Niaga', 'Jago', 'SeaBank', 'DANA', 'OVO', 'GoPay'] as $bank)
                                <option value="{{ $bank }}" {{ (old('bank_name') ?? $shop->bank_name) == $bank ? 'selected' : '' }}>
                                    {{ $bank }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Nomor Rekening --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nomor Rekening / E-Wallet</label>
                        <input type="number" name="account_number" required 
                               value="{{ old('account_number') ?? $shop->account_number }}"
                               placeholder="Contoh: 1234567890" 
                               class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-pink-500 focus:border-pink-500">
                    </div>

                    {{-- Atas Nama --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Atas Nama (Pemilik Rekening)</label>
                        <input type="text" name="account_holder" required 
                               value="{{ old('account_holder') ?? $shop->account_holder }}"
                               placeholder="Nama sesuai buku tabungan" 
                               class="w-full border-gray-300 rounded-lg px-4 py-2 focus:ring-pink-500 focus:border-pink-500">
                    </div>

                    {{-- Tombol Submit --}}
                    <button type="submit" onclick="return confirm('Pastikan data rekening sudah benar. Lanjutkan?')" 
                            class="w-full bg-pink-600 hover:bg-pink-700 text-white font-bold py-4 rounded-lg shadow-lg hover:shadow-xl transform active:scale-95 transition flex justify-center items-center gap-2">
                        🚀 Ajukan Penarikan Dana
                    </button>

                </form>
            </div>
            <div class="bg-gray-50 px-8 py-4 text-center border-t border-gray-100">
                <p class="text-xs text-gray-500">
                    Proses pencairan dana membutuhkan waktu 1x24 jam kerja setelah disetujui Admin.
                </p>
            </div>
        </div>

    </div>
</div>
@endsection