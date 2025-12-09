<x-app-layout>

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl mx-auto">
        <a href="{{ route('profile.show') }}" class="text-gray-500 hover:text-pink-600 mb-4 inline-flex items-center text-sm font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>

        <div class="bg-white p-6 rounded-lg shadow border-t-4 border-pink-500">
            <h2 class="text-xl font-bold mb-2 text-gray-800">Atur Alamat Pengiriman</h2>
            <p class="text-sm text-gray-500 mb-6">Alamat ini akan digunakan sebagai default saat kamu berbelanja.</p>

            <form method="post" action="{{ route('profile.address.update') }}">
                @csrf
                @method('put')

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Penerima</label>
                    <input type="text" name="recipient_name" 
                           value="{{ old('recipient_name', $user_addresses->recipient_name ?? $user->full_name) }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 transition" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">No. WhatsApp / Telepon</label>
                    {{-- Perhatikan: name="phone" sesuai validasi controller, tapi value dari phone_number --}}
                    <input type="text" name="phone_number" 
                           value="{{ old('phone_number', $user_addresses->phone_number ?? $user->phone_number) }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 transition" 
                           required placeholder="08...">
                    @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Alamat Lengkap</label>
                    <textarea name="full_address" rows="4" 
                    value="{{ old('full_address', $user_addresses->full_address ?? '') }}"
                              class="w-full border-gray-300 rounded-md shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 transition" 
                              required placeholder="Nama Jalan, No Rumah, RT/RW, Kelurahan, Kecamatan, Kode Pos">{{ old('full_address', $user_addresses->full_address ?? '') }}</textarea>
                </div>

                <button type="submit" class="w-full bg-pink-600 text-white font-bold py-2.5 rounded hover:bg-pink-700 transition shadow-lg">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</div>
</x-app-layout>