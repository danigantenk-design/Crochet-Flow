<x-app-layout>

{{-- @extends('layouts.app')  --}}

{{-- @section('content') --}}
<div class="py-12">
    <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
            <h2 class="text-2xl font-bold mb-6 text-center">Buka Toko Rajut Anda Sekarang!</h2>
            
            {{-- Form untuk mengirim data ke ShopController@store --}}
            <form action="{{ route('shop.store') }}" method="POST">
                @csrf
                
                {{-- Form Validation Errors --}}
                @if($errors->any())
                    <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Nama Toko --}}
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-bold mb-2">Nama Toko</label>
                    <input type="text" name="name" id="name" class="w-full border rounded p-2" required value="{{ old('name') }}">
                </div>

                {{-- Deskripsi --}}
                <div class="mb-4">
                    <label for="description" class="block text-gray-700 font-bold mb-2">Deskripsi Toko (Opsional)</label>
                    <textarea name="description" id="description" rows="3" class="w-full border rounded p-2">{{ old('description') }}</textarea>
                </div>

                {{-- City ID (Harus ada untuk lokasi pengiriman) --}}
                <div class="mb-6">
                    <label for="city_id" class="block text-gray-700 font-bold mb-2">Kota (Lokasi Pengiriman)</label>
                    <input type="number" name="city_id" id="city_id" class="w-full border rounded p-2" placeholder="Masukkan ID Kota/Kecamatan" required value="{{ old('city_id') }}">
                </div>

                <button type="submit" class="w-full bg-pink-600 text-white font-bold py-2 px-4 rounded hover:bg-pink-700 transition">
                    Daftarkan Toko
                </button>
            </form>
        </div>
    </div>
</div>
{{-- @endsection --}}
</x-app-layout>