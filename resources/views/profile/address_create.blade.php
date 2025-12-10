<x-app-layout>
{{-- @extends('layouts.app') --}}

{{-- @section('content') --}}
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl mx-auto">
        <a href="{{ route('profile.show') }}" class="text-gray-500 hover:text-pink-600 mb-4 inline-flex items-center text-sm font-medium">
            &larr; Batal
        </a>

        <div class="bg-white p-6 rounded-lg shadow border-t-4 border-pink-500">
            <h2 class="text-xl font-bold mb-4 text-gray-800">Tambah Alamat Baru</h2>

            <form method="post" action="{{ route('profile.address.store') }}">
                @csrf
                {{-- Method POST adalah default, jadi tidak perlu @method('PUT') --}}

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Penerima</label>
                    <input type="text" name="recipient_name" class="w-full border rounded p-2" required placeholder="Nama penerima paket">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">No. WhatsApp / Telepon</label>
                    <input type="text" name="phone_number" class="w-full border rounded p-2" required placeholder="08...">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Alamat Lengkap</label>
                    <textarea name="full_address" rows="3" class="w-full border rounded p-2" required placeholder="Jalan, RT/RW, Kelurahan, Kecamatan..."></textarea>
                </div>
                {{-- <div class="mb-4">
                    <label for="city_id">ID Kota (Contoh: 153)</label>
                    <input type="number" name="city_id" id="city_id" class="w-full border rounded p-2" required>
                </div> --}}
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Kode Pos</label>
                    <input type="text" name="postal_code" class="w-full border rounded p-2" required placeholder="Contoh: 12345" maxlength="10">
                </div>
                <button type="submit" class="w-full bg-pink-600 text-white font-bold py-2 rounded hover:bg-pink-700">
                    Simpan Alamat
                </button>
            </form>
        </div>
    </div>
</div>
{{-- @endsection --}}
</x-app-layout>