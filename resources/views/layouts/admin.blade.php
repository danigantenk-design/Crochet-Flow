<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - CrochetFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Font Awesome untuk Ikon --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans text-gray-800">

    <div class="min-h-screen flex">
        
        {{-- SIDEBAR (Tetap disini agar muncul di semua halaman admin) --}}
        <aside class="w-64 bg-gray-900 text-white flex flex-col fixed h-full shadow-lg z-10">
            <div class="h-16 flex items-center justify-center border-b border-gray-800 bg-gray-900">
                <span class="text-xl font-bold text-pink-500 flex items-center gap-2">
                    <i class="fa-solid fa-yarn"></i> CrochetFlow
                </span>
            </div>
            
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                {{-- MENU UTAMA --}}
                <p class="px-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Main</p>
                <a href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-pink-600 text-white font-bold shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <i class="fa-solid fa-chart-line w-5"></i> Dashboard
                </a>

                {{-- DATA RIWAYAT --}}
                <p class="px-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-6 mb-2">Data Riwayat</p>
                
                <a href="{{ route('admin.users') }}" 
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition-all {{ request()->routeIs('admin.users') ? 'bg-pink-600 text-white font-bold shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <i class="fa-solid fa-users w-5"></i> Semua User
                </a>

                <a href="{{ route('admin.shops') }}" 
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition-all {{ request()->routeIs('admin.shops') ? 'bg-pink-600 text-white font-bold shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <i class="fa-solid fa-store w-5"></i> Daftar Toko
                </a>

                <a href="{{ route('admin.orders.history') }}" 
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition-all {{ request()->routeIs('admin.orders.history') ? 'bg-pink-600 text-white font-bold shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <i class="fa-solid fa-box w-5"></i> Riwayat Pesanan
                </a>

                <a href="{{ route('admin.withdrawals.history') }}" 
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition-all {{ request()->routeIs('admin.withdrawals.history') ? 'bg-pink-600 text-white font-bold shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <i class="fa-solid fa-clock-rotate-left w-5"></i> Riwayat Penarikan
                </a>

                {{-- LAPORAN --}}
                <p class="px-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-6 mb-2">Laporan</p>
                <a href="{{ route('admin.sales.report') }}" 
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm transition-all {{ request()->routeIs('admin.sales.report') ? 'bg-pink-600 text-white font-bold shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <i class="fa-solid fa-file-invoice-dollar w-5"></i> Laporan Penjualan
                </a>
            </nav>

            <div class="p-4 border-t border-gray-800 bg-gray-900">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="w-full flex items-center gap-2 text-red-400 hover:text-red-300 hover:bg-gray-800 p-2 rounded transition">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        {{-- KONTEN UTAMA (Berubah-ubah sesuai halaman) --}}
        <main class="flex-1 p-8 ml-64">
            @yield('content')
        </main>
    </div>

</body>
</html>