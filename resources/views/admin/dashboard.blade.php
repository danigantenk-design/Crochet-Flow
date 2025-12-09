<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - CrochetFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <div class="min-h-screen flex">
        
        <!-- SIDEBAR -->
        <aside class="w-64 bg-gray-900 text-white flex flex-col">
            <div class="h-16 flex items-center justify-center border-b border-gray-800">
                <span class="text-xl font-bold text-pink-500">🧶 Admin Panel</span>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 bg-gray-800 rounded text-white font-bold">
                    📊 Dashboard
                </a>
                <a href="#" class="block px-4 py-2 text-gray-400 hover:bg-gray-800 hover:text-white rounded">
                    👥 Users Management
                </a>
                <a href="#" class="block px-4 py-2 text-gray-400 hover:bg-gray-800 hover:text-white rounded">
                    💰 Withdrawals (Pencairan)
                </a>
                <a href="/" target="_blank" class="block px-4 py-2 text-gray-400 hover:bg-gray-800 hover:text-white rounded border-t border-gray-800 mt-4">
                    🌍 Lihat Website
                </a>
            </nav>
            <div class="p-4 border-t border-gray-800">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="w-full text-left text-red-400 hover:text-red-300 text-sm">🚪 Logout</button>
                </form>
            </div>
        </aside>

        <!-- CONTENT -->
        <main class="flex-1 p-8 overflow-y-auto">
            
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Ringkasan Sistem</h1>

            <!-- STATISTIK -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- User -->
                <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
                    <p class="text-gray-500 text-sm">Total Pengguna</p>
                    <h3 class="text-3xl font-bold">{{ $totalUsers }}</h3>
                </div>
                <!-- Toko -->
                <div class="bg-white p-6 rounded-lg shadow border-l-4 border-purple-500">
                    <p class="text-gray-500 text-sm">Total Toko</p>
                    <h3 class="text-3xl font-bold">{{ $totalShops }}</h3>
                </div>
                <!-- Order -->
                <div class="bg-white p-6 rounded-lg shadow border-l-4 border-yellow-500">
                    <p class="text-gray-500 text-sm">Total Pesanan</p>
                    <h3 class="text-3xl font-bold">{{ $totalOrders }}</h3>
                </div>
                <!-- Revenue -->
                <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
                    <p class="text-gray-500 text-sm">Omzet Platform</p>
                    <h3 class="text-2xl font-bold text-green-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                </div>
            </div>

            <!-- TABEL VERIFIKASI TOKO -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-bold text-lg text-gray-800">🏪 Pengajuan Toko Baru (Pending)</h3>
                </div>
                
                @if(session('success'))
                    <div class="bg-green-100 text-green-700 p-3 m-4 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if($pendingShops->isEmpty())
                    <div class="p-8 text-center text-gray-500">
                        Tidak ada pengajuan toko baru saat ini.
                    </div>
                @else
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                            <tr>
                                <th class="px-6 py-3">Nama Toko</th>
                                <th class="px-6 py-3">Pemilik</th>
                                <th class="px-6 py-3">Kota ID</th>
                                <th class="px-6 py-3">Deskripsi</th>
                                <th class="px-6 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($pendingShops as $shop)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-bold text-gray-800">{{ $shop->name }}</td>
                                <td class="px-6 py-4">
                                    {{ $shop->user->full_name }} <br>
                                    <span class="text-xs text-gray-500">{{ $shop->user->email }}</span>
                                </td>
                                <td class="px-6 py-4">{{ $shop->city_id }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 truncate max-w-xs">{{ $shop->description }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <!-- Tombol Reject -->
                                    <form action="{{ route('admin.shop.reject', $shop->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menolak toko ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-bold">Tolak</button>
                                    </form>

                                    <!-- Tombol Approve -->
                                    <form action="{{ route('admin.shop.approve', $shop->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-sm font-bold shadow">
                                            ✅ Setujui
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

        </main>
    </div>

</body>
</html>