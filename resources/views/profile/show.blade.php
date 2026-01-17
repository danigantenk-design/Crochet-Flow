<x-app-layout>

@section('content')

<div class="container mx-auto px-4 py-8">
    {{-- REVISI: Tombol Kembali diarahkan ke Home atau Dashboard --}}
    <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-pink-600 mb-4 inline-flex items-center text-sm font-medium">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Kembali ke Dashboard
    </a>

    <div class="max-w-2xl mx-auto bg-white shadow rounded-lg overflow-hidden">
        
        <div class="bg-pink-50 p-6 flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Profil Saya</h2>
            <a href="{{ route('profile.edit') }}" class="px-4 py-2 bg-gray-800 text-white rounded text-sm hover:bg-gray-700 font-bold">
                Edit Data Diri
            </a>
        </div>

        <div class="p-6 space-y-6">
            @if (session('status') === 'profile-updated')
                <div class="bg-green-100 text-green-700 p-3 rounded border border-green-200">
                    ✅ Data diri berhasil diperbarui!
                </div>
            @elseif (session('status') === 'address-updated')
                <div class="bg-green-100 text-green-700 p-3 rounded border border-green-200">
                    ✅ Alamat pengiriman berhasil disimpan!
                </div>
            @endif

            <div>
                <h3 class="font-bold text-gray-500 text-sm uppercase mb-3 border-b pb-1">Informasi Akun</h3>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <span class="text-gray-500 text-xs">Nama Lengkap</span>
                        <p class="font-semibold text-lg text-gray-900">{{ $user->full_name ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-xs">Email</span>
                        <p class="font-semibold text-lg text-gray-900">{{ $user->email ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-xs">No. HP (Akun)</span>
                        <p class="font-semibold text-lg text-gray-900">{{ $user->phone_number ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <div class="flex justify-between items-center mb-4 border-b pb-2">
                    <h3 class="font-bold text-gray-800 text-lg">Daftar Alamat</h3>
                    <a href="{{ route('profile.address.create') }}" class="bg-gray-800 text-white px-4 py-2 rounded text-sm hover:bg-gray-700 font-bold transition">
                        + Tambah Alamat
                    </a>
                </div>
                
                @if($user->addresses->isEmpty())
                    <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                        <p class="text-gray-500 italic mb-3">Belum ada alamat tersimpan.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($user->addresses as $address)
                        <div class="bg-white p-4 rounded-lg border {{ $address->is_primary ? 'border-pink-500 ring-1 ring-pink-500' : 'border-gray-200' }} shadow-sm relative group">
                            
                            @if($address->is_primary)
                                <span class="absolute top-2 right-2 bg-pink-100 text-pink-700 text-xs px-2 py-1 rounded font-bold">Utama</span>
                            @endif

                            <p class="font-bold text-gray-800">{{ $address->recipient_name }}</p>
                            <p class="text-gray-600 text-sm mb-1">{{ $address->phone_number }}</p>
                            <p class="text-gray-700 text-sm">{{ $address->full_address }}</p>

                            <div class="mt-3 pt-3 border-t flex justify-end">
                                <form action="{{ route('profile.address.destroy', $address->id) }}" method="POST" onsubmit="return confirm('Yakin hapus alamat ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 text-xs hover:text-red-700 font-bold flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Hapus Alamat
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="mt-8 border-t pt-6">
                <div class="mt-8 pt-6 border-t border-gray-100">
                <h3 class="font-bold text-gray-800 text-lg mb-4">Area Penjual</h3>
                
                <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg p-6 text-white shadow-lg relative overflow-hidden group">
                    <div class="absolute -right-6 -top-6 bg-white opacity-10 rounded-full w-32 h-32 group-hover:scale-150 transition duration-500"></div>
                    
                    <div class="relative z-10 flex justify-between items-center">
                        <div>
                            @if($user->shop)
                                <h4 class="text-xl font-bold mb-1">{{ $user->shop->name }}</h4>
                                <p class="text-white text-opacity-90 text-sm">Kelola produk dan pesananmu di sini.</p>
                            @else
                                <h4 class="text-xl font-bold mb-1">Mulai Berjualan?</h4>
                                <p class="text-white text-opacity-90 text-sm">Buka toko gratis dan jangkau pembeli sekarang.</p>
                            @endif
                        </div>

                        <div>
                            @if($user->shop)
                                <a href="{{ route('shop.index') }}" class="inline-block bg-white text-pink-600 font-bold px-6 py-2 rounded shadow hover:bg-gray-100 transition">
                                    Masuk ke Toko &rarr;
                                </a>
                            @else
                                <a href="{{ route('shop.create') }}" class="inline-block bg-white text-purple-600 font-bold px-6 py-2 rounded shadow hover:bg-gray-100 transition">
                                    Buka Toko Gratis
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 border-t pt-6">
                 </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 text-red-600 hover:text-red-800 font-bold transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
</x-app-layout>