@extends('layouts.seller')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">

        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">⚙️ Pengaturan Toko</h2>
                <p class="text-gray-500 text-sm">Update profil toko dan rekening pencairan dana.</p>
            </div>
            
            {{-- Badge Status --}}
            @if($shop->is_verified)
                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold border border-blue-200">
                    <i class="fa-solid fa-check-circle"></i> Terverifikasi
                </span>
            @else
                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold border border-yellow-200">
                    <i class="fa-solid fa-clock"></i> Menunggu Verifikasi
                </span>
            @endif
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
                {{ session('success') }}
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

        <form action="{{ route('shop.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- BAGIAN 1: PROFIL TOKO --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="p-6 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-bold text-gray-800">🏪 Identitas Toko</h3>
                </div>
                
                <div class="p-6 space-y-6">
                    {{-- Foto Profil --}}
                    <div class="flex items-center gap-6">
                        <div class="shrink-0 relative group">
                            @if($shop->image)
                                <img id="preview-img" src="{{ asset('storage/'.$shop->image) }}" class="h-24 w-24 object-cover rounded-full border-4 border-gray-100 shadow-sm">
                            @else
                                <div class="h-24 w-24 bg-pink-100 rounded-full flex items-center justify-center text-pink-500 text-2xl font-bold border-4 border-gray-100">
                                    {{ substr($shop->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Logo Toko</label>
                            <input type="file" name="image" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100 border rounded-lg">
                            <p class="text-xs text-gray-400 mt-1">Format JPG, PNG. Maks 2MB.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Toko</label>
                            <input type="text" name="name" value="{{ old('name', $shop->name) }}" required class="w-full border-gray-300 rounded-lg focus:ring-pink-500">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi Singkat</label>
                            <textarea name="description" rows="3" class="w-full border-gray-300 rounded-lg focus:ring-pink-500">{{ old('description', $shop->description) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">No. Telepon / WA</label>
                            <input type="text" name="phone" value="{{ old('phone', $shop->phone) }}" required class="w-full border-gray-300 rounded-lg focus:ring-pink-500">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">ID Kota (RajaOngkir)</label>
                            <input type="text" value="{{ $shop->city_id }}" readonly class="w-full border-gray-200 bg-gray-100 text-gray-500 rounded-lg cursor-not-allowed">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Alamat Lengkap (Titik Jemput)</label>
                            <textarea name="address" rows="2" required class="w-full border-gray-300 rounded-lg focus:ring-pink-500">{{ old('address', $shop->address) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BAGIAN 2: REKENING BANK (PENTING UNTUK WITHDRAW) --}}
            <div class="bg-blue-50 rounded-xl shadow-sm border border-blue-100 overflow-hidden mb-8">
                <div class="p-6 border-b border-blue-100 bg-blue-100/50">
                    <h3 class="font-bold text-blue-800 flex items-center gap-2">
                        <i class="fa-solid fa-building-columns"></i> Rekening Pencairan Dana
                    </h3>
                    <p class="text-xs text-blue-600 mt-1">Pastikan data ini benar agar proses penarikan saldo lancar.</p>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Bank / E-Wallet</label>
                        <select name="bank_name" class="w-full border-gray-300 rounded-lg focus:ring-pink-500">
                            <option value="">-- Pilih Bank --</option>
                            @foreach(['BCA', 'BRI', 'BNI', 'Mandiri', 'BSI', 'CIMB Niaga', 'Jago', 'SeaBank', 'DANA', 'OVO', 'GoPay'] as $bank)
                                <option value="{{ $bank }}" {{ old('bank_name', $shop->bank_name) == $bank ? 'selected' : '' }}>
                                    {{ $bank }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nomor Rekening</label>
                        <input type="number" name="account_number" value="{{ old('account_number', $shop->account_number) }}" 
                               class="w-full border-gray-300 rounded-lg focus:ring-pink-500" placeholder="Contoh: 1234567890">
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Atas Nama (Pemilik Rekening)</label>
                        <input type="text" name="account_holder" value="{{ old('account_holder', $shop->account_holder) }}" 
                               class="w-full border-gray-300 rounded-lg focus:ring-pink-500" placeholder="Nama sesuai buku tabungan">
                    </div>
                </div>
            </div>

            {{-- TOMBOL SIMPAN --}}
            <div class="flex justify-end gap-3 pb-8">
                <a href="{{ route('shop.index') }}" class="px-6 py-3 rounded-lg text-gray-600 font-bold hover:bg-gray-100 transition">
                    Batal
                </a>
                <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white px-8 py-3 rounded-lg font-bold shadow-md hover:shadow-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Semua Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection