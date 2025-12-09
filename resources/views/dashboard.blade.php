<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pesanan - CrochetFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

    @include('layouts.navigation')

    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                📦 Riwayat Pesanan Saya
            </h2>
        </div>
    </header>

    <main class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-200 text-green-700 p-4 rounded-lg mb-6 flex items-center gap-2">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if($orders->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg text-center py-16">
                    <div class="text-6xl mb-4">🛍️</div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Belum ada riwayat pesanan</h3>
                    <p class="text-gray-500 mb-6">Yuk mulai jelajahi koleksi rajutan terbaik!</p>
                    <a href="{{ route('front.index') }}" class="bg-pink-600 text-white px-6 py-2 rounded-full font-bold hover:bg-pink-700 transition">
                        Mulai Belanja
                    </a>
                </div>
            
            @else
                <div class="space-y-6">
                    @foreach($orders as $order)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 hover:shadow-md transition">
                            <div class="p-6">
                                <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
                                    
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="text-sm font-bold text-gray-500">
                                                {{ $order->created_at->format('d M Y') }}
                                            </span>
                                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">
                                                {{ $order->invoice_number }}
                                            </span>
                                        </div>
                                        
                                        <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                                            <span>🏪 {{ $order->shop->name ?? 'Toko Tidak Ditemukan' }}</span>
                                        </h3>
                                        
                                        <p class="text-sm text-gray-500 mt-1">
                                            Total Belanja: <span class="font-bold text-gray-800">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                                            <span class="text-xs text-gray-400">({{ $order->items->count() }} Barang)</span>
                                        </p>
                                    </div>

                                    <div>
                                        @if($order->status == 'pending')
                                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-bold border border-yellow-200 flex items-center gap-1">
                                                ⏳ Menunggu Bayar
                                            </span>
                                        @elseif($order->status == 'processing')
                                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-bold border border-blue-200 flex items-center gap-1">
                                                📦 Sedang Dikemas
                                            </span>
                                        @elseif($order->status == 'shipped')
                                            <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-bold border border-purple-200 flex items-center gap-1">
                                                🚚 Sedang Dikirim
                                            </span>
                                        @else
                                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-bold border border-green-200 flex items-center gap-1">
                                                ✅ Selesai
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('orders.show', $order->id) }}" 
                                           class="{{ $order->status == 'pending' ? 'bg-pink-600 hover:bg-pink-700 text-white' : 'bg-gray-800 hover:bg-gray-700 text-white' }} px-4 py-2 rounded-lg font-bold text-sm transition shadow-sm">
                                            {{ $order->status == 'pending' ? '💳 Bayar Sekarang' : '📄 Lihat Detail' }}
                                        </a>
                                    </div>

                                </div>
                                
                                <div class="mt-4 pt-4 border-t border-gray-50 flex items-center gap-3">
                                    @if($order->items->first())
                                        <img src="{{ $order->items->first()->product->images->first()->image_url ?? 'https://placehold.co/50' }}" 
                                             class="w-10 h-10 rounded object-cover border">
                                        <span class="text-sm text-gray-600 truncate max-w-xs">
                                            {{ $order->items->first()->product->name }} 
                                            @if($order->items->count() > 1)
                                                <span class="text-xs text-gray-400">+{{ $order->items->count() - 1 }} barang lainnya</span>
                                            @endif
                                        </span>
                                    @endif
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </main>

</body>
</html>