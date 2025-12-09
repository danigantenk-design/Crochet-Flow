<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Seller Center - CrochetFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased" x-data="{ sidebarOpen: false }">

    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-64 bg-gray-900 text-white flex-shrink-0 hidden md:flex flex-col">
            <div class="p-4 border-b border-gray-800 flex items-center gap-2">
                <span class="text-2xl">🧶</span>
                <span class="font-bold text-lg tracking-wide">Seller Center</span>
            </div>
            
            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-1 px-2">
                    <li>
                        <a href="{{ route('shop.index') }}" class="flex items-center px-4 py-3 rounded-md {{ request()->routeIs('shop.index') ? 'bg-pink-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                            <span class="mr-3">📊</span> Dashboard
                        </a>
                    </li>

                    <li>
                        <a href="#" class="flex items-center px-4 py-3 rounded-md text-gray-400 hover:bg-gray-800 hover:text-white group">
                            <span class="mr-3">🧶</span> Produk
                        </a>
                        <div class="pl-12 space-y-1 mt-1">
                            <a href="{{ route('products.create') }}" class="block py-2 text-sm text-gray-500 hover:text-pink-400">Tambah Produk</a>
                        </div>
                    </li>

                    <li>
                        <a href="{{ route('shop.orders') }}" class="flex items-center px-4 py-3 rounded-md {{ request()->routeIs('shop.orders*') ? 'bg-pink-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                            <span class="mr-3">📦</span> Pesanan Masuk
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('shop.edit') }}" class="flex items-center px-4 py-3 rounded-md {{ request()->routeIs('shop.edit') ? 'bg-pink-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                            <span class="mr-3">⚙️</span> Pengaturan Toko
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="p-4 border-t border-gray-800">
                <a href="{{ route('front.index') }}" class="flex items-center justify-center w-full bg-gray-800 hover:bg-gray-700 text-white py-2 rounded text-sm transition">
                    &larr; Kembali ke Website
                </a>
            </div>
        </aside>

        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            
            <header class="bg-white shadow-sm md:hidden flex justify-between items-center p-4">
                <div class="font-bold text-gray-800">Seller Center</div>
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </header>

            <div x-show="sidebarOpen" class="fixed inset-0 z-50 bg-gray-900 bg-opacity-50 md:hidden" @click="sidebarOpen = false"></div>
            <div x-show="sidebarOpen" class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white transition transform md:hidden" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full">
                 <div class="p-4 font-bold text-xl border-b border-gray-800">Menu Toko</div>
                 <nav class="p-4 space-y-2">
                    <a href="{{ route('shop.index') }}" class="block py-2 text-gray-300">Dashboard</a>
                    <a href="{{ route('shop.orders') }}" class="block py-2 text-gray-300">Pesanan</a>
                    <a href="{{ route('products.create') }}" class="block py-2 text-gray-300">Tambah Produk</a>
                    <a href="{{ route('front.index') }}" class="block py-2 text-pink-400 font-bold mt-4">&larr; Kembali ke Web</a>
                 </nav>
            </div>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded relative">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded relative">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>