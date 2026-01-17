<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Header Card --}}
            <div class="bg-pink-600 rounded-t-xl p-8 text-center text-white shadow-lg">
                <h2 class="text-3xl font-extrabold mb-2">✨ Mulai Perjalanan Bisnismu!</h2>
                <p class="text-pink-100">Lengkapi data di bawah ini untuk membuka Toko Rajut resmimu.</p>
            </div>

            <div class="bg-white overflow-hidden shadow-xl rounded-b-xl p-8 border border-t-0 border-gray-200">
                
                {{-- Form Start --}}
                <form action="{{ route('shop.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    {{-- Error Alerts --}}
                    @if($errors->any())
                        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
                            <p class="font-bold">Oops! Ada kesalahan:</p>
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        {{-- KOLOM KIRI: Identitas Toko --}}
                        <div class="space-y-6">
                            <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">🏪 Identitas Toko</h3>

                            {{-- Nama Toko --}}
                            <div>
                                <label for="name" class="block text-sm font-bold text-gray-700 mb-1">Nama Toko <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" required value="{{ old('name') }}" placeholder="Contoh: Rajutan Nenek"
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 transition">
                            </div>

                            {{-- Logo Toko --}}
                            <div>
                                <label for="image" class="block text-sm font-bold text-gray-700 mb-1">Logo / Foto Profil Toko</label>
                                <input type="file" name="image" id="image" accept="image/*"
                                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100 border border-gray-300 rounded-lg cursor-pointer">
                                <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG. Maks 2MB.</p>
                            </div>

                            {{-- Deskripsi --}}
                            <div>
                                <label for="description" class="block text-sm font-bold text-gray-700 mb-1">Deskripsi Singkat</label>
                                <textarea name="description" id="description" rows="4" placeholder="Ceritakan sedikit tentang keunikan produk rajutanmu..."
                                          class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 transition">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        {{-- KOLOM KANAN: Kontak & Alamat --}}
                        <div class="space-y-6">
                            <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">📍 Kontak & Lokasi</h3>

                            {{-- No HP --}}
                            <div>
                                <label for="phone" class="block text-sm font-bold text-gray-700 mb-1">No. WhatsApp / Telepon <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-sm font-bold">+62</span>
                                    <input type="number" name="phone" id="phone" required value="{{ old('phone') }}" placeholder="8123456789"
                                           class="w-full pl-12 border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 transition">
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Penting untuk verifikasi Admin.</p>
                            </div>

                            {{-- City ID --}}
                            <div>
                                <label for="city_id" class="block text-sm font-bold text-gray-700 mb-1">ID Kota/Kecamatan (RajaOngkir) <span class="text-red-500">*</span></label>
                                <input type="number" name="city_id" id="city_id" required value="{{ old('city_id') }}" placeholder="Masukkan ID Kota"
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 transition bg-gray-50">
                                <p class="text-xs text-blue-500 mt-1">
                                    <a href="#" class="hover:underline">Klik di sini untuk cari ID Kotamu</a>
                                </p>
                            </div>

                            {{-- Alamat Lengkap --}}
                            <div>
                                <label for="address" class="block text-sm font-bold text-gray-700 mb-1">Alamat Lengkap (Pickup) <span class="text-red-500">*</span></label>
                                <textarea name="address" id="address" rows="3" required placeholder="Nama Jalan, No. Rumah, RT/RW, Kelurahan..."
                                          class="w-full border-gray-300 rounded-lg shadow-sm focus:border-pink-500 focus:ring focus:ring-pink-200 transition">{{ old('address') }}</textarea>
                                <p class="text-xs text-gray-400 mt-1">Digunakan kurir untuk menjemput paket.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Submit --}}
                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <button type="submit" class="w-full bg-pink-600 hover:bg-pink-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200 flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Buka Toko Sekarang
                        </button>
                        <p class="text-center text-xs text-gray-400 mt-4">
                            Dengan mendaftar, Anda menyetujui Syarat & Ketentuan CrochetFlow.
                        </p>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>