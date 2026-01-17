<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pesanan - CrochetFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans text-gray-900">

    @include('layouts.navigation')

    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">📦 Riwayat Pesanan Saya</h2>
        </div>
    </header>

    <main class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-200 text-green-700 p-4 rounded-lg mb-6">{{ session('success') }}</div>
            @endif

            @if($orders->isEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg text-center py-16">
                    <div class="text-6xl mb-4">🛍️</div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Belum ada riwayat pesanan</h3>
                    <a href="{{ route('front.index') }}" class="bg-pink-600 text-white px-6 py-2 rounded-full font-bold hover:bg-pink-700 transition">Mulai Belanja</a>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($orders as $order)
                        @php
                            $isFullDigital = $order->items->every(fn($i) => $i->product->product_type == 'digital');
                            $canDownload = (in_array($order->status, ['processing', 'shipped', 'completed']) || $order->payment_status == 'paid');
                        @endphp

                        <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 hover:shadow-md transition">
                            <div class="p-6">
                                <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
                                    
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="text-sm font-bold text-gray-500">{{ $order->created_at->format('d M Y') }}</span>
                                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded font-mono">{{ $order->invoice_number }}</span>
                                        </div>
                                        <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                                            <span>🏪 {{ $order->shop->name }}</span>
                                            @if($isFullDigital)
                                                <span class="bg-blue-100 text-blue-600 px-2 py-0.5 rounded text-[10px] font-bold">📂 DIGITAL</span>
                                            @endif
                                        </h3>
                                        <p class="text-sm text-gray-500 mt-1">Total: <span class="font-bold text-gray-800">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span></p>
                                    </div>

                                    {{-- <div>
                                        @if($order->status == 'pending')
                                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-bold border border-yellow-200">⏳ Menunggu Bayar</span>
                                        @elseif($order->status == 'processing')
                                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-bold border border-blue-200">📦 Diproses</span>
                                        @elseif($order->status == 'shipped')
                                            <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-bold border border-purple-200">🚚 Dikirim</span>
                                        @elseif($order->status == 'completed')
                                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-bold border border-green-200">✅ Selesai</span>
                                        @else
                                            <span class="bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-sm font-bold border border-gray-300">❌ Batal</span>
                                        @endif
                                    </div> --}}

                                    <div class="flex items-center gap-3">
                                        {{-- 1. Status Pesanan --}}
                                        <div>
                                            @if($order->status == 'pending')
                                                <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-bold border border-yellow-200 whitespace-nowrap">⏳ Menunggu Bayar</span>
                                            @elseif($order->status == 'processing')
                                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-bold border border-blue-200 whitespace-nowrap">📦 Diproses</span>
                                            @elseif($order->status == 'shipped')
                                                <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-xs font-bold border border-purple-200 whitespace-nowrap">🚚 Dikirim</span>
                                            @elseif($order->status == 'completed')
                                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold border border-green-200 whitespace-nowrap">✅ Selesai</span>
                                            @else
                                                <span class="bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-xs font-bold border border-gray-300 whitespace-nowrap">❌ Batal</span>
                                            @endif
                                        </div>

                                        {{-- 2. Tombol Utama (Terima Pesanan / Bayar / Download) --}}
                                        <div class="flex items-center gap-2">
                                            @if($order->status == 'shipped')
                                                <form action="{{ route('orders.confirm-received', $order->id) }}" method="POST" class="m-0">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                        onclick="return confirm('Apakah Anda yakin paket sudah diterima dengan baik?')"
                                                        class="bg-green-600 text-white px-4 py-2 rounded-full font-bold text-xs hover:bg-green-700 shadow-sm transition flex items-center gap-1 whitespace-nowrap">
                                                        <i class="fa-solid fa-check-double"></i> Terima Pesanan
                                                    </button>
                                                </form>
                                            @elseif($order->status == 'pending' && !$order->payment_proof)
                                                <a href="{{ route('orders.show', $order->id) }}" class="bg-pink-600 text-white px-4 py-2 rounded-full font-bold text-xs hover:bg-pink-700 shadow-sm transition whitespace-nowrap">💳 Bayar</a>
                                            @elseif($isFullDigital && $canDownload)
                                                <a href="{{ route('orders.show', $order->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-full font-bold text-xs hover:bg-blue-700 shadow-sm transition whitespace-nowrap">⬇️ Download PDF</a>
                                            @endif

                                            {{-- 3. Tombol Detail (Selalu Ada) --}}
                                            <a href="{{ route('orders.show', $order->id) }}" class="bg-gray-50 text-gray-700 px-4 py-2 rounded-full font-bold text-xs hover:bg-gray-200 border border-gray-200 transition whitespace-nowrap">📄 Detail</a>
                                        </div>
                                    </div>
                                </div>

                                {{-- Thumbnail Produk --}}
                                <div class="mt-4 pt-4 border-t border-gray-50 flex items-center gap-3">
                                    @if($order->items->first())
                                        <img src="{{ $order->items->first()->product->image_url }}" class="w-12 h-12 rounded object-cover border bg-gray-50">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-700">{{ $order->items->first()->product->name }}</span>
                                            @if($order->items->count() > 1)
                                                <span class="text-xs text-gray-400">+{{ $order->items->count() - 1 }} barang lainnya</span>
                                            @endif
                                        </div>
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