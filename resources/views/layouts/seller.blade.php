<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Center - CrochetFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 font-sans text-gray-800">

    <div class="min-h-screen flex flex-col md:flex-row">
        
        {{-- SIDEBAR SELLER --}}
        <aside class="w-full md:w-64 bg-white border-r border-gray-200 flex flex-col md:h-screen md:fixed z-20">
            {{-- Logo Area --}}
            <div class="h-16 flex items-center justify-center border-b border-gray-100 bg-pink-600 text-white">
                <span class="text-xl font-bold flex items-center gap-2">
                    <i class="fa-solid fa-store"></i> Seller Center
                </span>
            </div>

            {{-- Info Toko Singkat --}}
            <div class="p-4 border-b border-gray-100 flex items-center gap-3 bg-pink-50">
                @if(Auth::user()->shop->image)
                    <img src="{{ asset('storage/'.Auth::user()->shop->image) }}" class="w-10 h-10 rounded-full object-cover border border-pink-200">
                @else
                    <div class="w-10 h-10 rounded-full bg-pink-200 flex items-center justify-center text-pink-700 font-bold">
                        {{ substr(Auth::user()->shop->name, 0, 1) }}
                    </div>
                @endif
                <div class="overflow-hidden">
                    <h4 class="font-bold text-sm truncate text-gray-800">{{ Auth::user()->shop->name }}</h4>
                    <p class="text-xs text-green-600 flex items-center gap-1">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span> Online
                    </p>
                </div>
            </div>
            
            {{-- Menu Navigasi --}}
            <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
                
                <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 mt-2">Bisnis Saya</p>
                
                <a href="{{ route('shop.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('shop.index') ? 'bg-pink-100 text-pink-700 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-pink-600' }}">
                    <i class="fa-solid fa-chart-pie w-5 text-center"></i> Dashboard & Produk
                </a>
                
                <a href="{{ route('shop.orders') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('shop.orders*') ? 'bg-pink-100 text-pink-700 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-pink-600' }}">
                    <i class="fa-solid fa-box-open w-5 text-center"></i> Pesanan Masuk
                </a>

                {{-- MENU KEUANGAN (YANG KEMARIN HILANG) --}}
                <a href="{{ route('shop.finance') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('shop.finance*') || request()->routeIs('shop.withdraw*') ? 'bg-pink-100 text-pink-700 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-pink-600' }}">
                    <i class="fa-solid fa-wallet w-5 text-center"></i> Keuangan & Saldo
                </a>

                <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 mt-6">Pengaturan</p>

                <a href="{{ route('shop.edit') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors {{ request()->routeIs('shop.edit') ? 'bg-pink-100 text-pink-700 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-pink-600' }}">
                    <i class="fa-solid fa-gear w-5 text-center"></i> Profil Toko
                </a>

                <a href="{{ route('front.index') }}" target="_blank" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-pink-600 transition-colors">
                    <i class="fa-solid fa-earth-americas w-5 text-center"></i> Lihat Website
                </a>
            </nav>

            {{-- Tombol Keluar --}}
            <div class="p-4 border-t border-gray-200">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 justify-center w-full px-4 py-2 text-sm font-bold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Akun User
                </a>
            </div>
        </aside>

        {{-- KONTEN UTAMA --}}
        <main class="flex-1 p-6 md:ml-64 bg-gray-50 min-h-screen">
            @yield('content')
        </main>
    </div>

</body>
</html>