<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Status Pengajuan Toko') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-10 text-center">
                
                <!-- Ilustrasi Jam / Menunggu -->
                <div class="mb-6 flex justify-center">
                    <div class="bg-yellow-100 p-4 rounded-full">
                        <svg class="w-16 h-16 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>

                <h3 class="text-2xl font-bold text-gray-800 mb-2">Pengajuan Sedang Ditinjau</h3>
                <p class="text-gray-600 mb-6">
                    Terima kasih telah mendaftar sebagai partner CrochetFlow. <br>
                    Data toko kamu sedang diverifikasi oleh tim Admin kami. Proses ini biasanya memakan waktu 1x24 jam.
                </p>

                <div class="bg-gray-50 p-4 rounded-lg text-sm text-gray-500 mb-8 inline-block text-left">
                    <p><strong>Status:</strong> <span class="text-yellow-600 font-bold uppercase">Pending Verification</span></p>
                    <p><strong>Tanggal Pengajuan:</strong> {{ Auth::user()->shop->created_at->format('d M Y, H:i') }}</p>
                </div>

                <div>
                    <a href="/" class="text-pink-600 font-bold hover:underline">
                        &larr; Kembali Belanja
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>