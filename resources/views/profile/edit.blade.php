<x-app-layout>

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl mx-auto">
        <a href="{{ route('profile.show') }}" class="text-gray-500 hover:text-pink-600 mb-4 inline-flex items-center text-sm font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Profil
        </a>

        <div class="bg-white p-6 rounded-lg shadow border border-gray-100">
            <h2 class="text-xl font-bold mb-6 text-gray-800 border-b pb-2">Edit Data Diri</h2>

            <form method="post" action="{{ route('profile.update') }}">
                @csrf
                @method('patch')

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 transition" required>
                    @error('full_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 transition" required>
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-1">No. HP (Akun)</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 transition" placeholder="08...">
                    @error('phone_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="w-full bg-gray-800 text-white font-bold py-2.5 rounded hover:bg-gray-700 transition shadow-lg">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</div>
</x-app-layout>