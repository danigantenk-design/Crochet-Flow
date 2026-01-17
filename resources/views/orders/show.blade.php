<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->invoice_number }} - CrochetFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-800">

    @include('layouts.navigation')

    <main class="container mx-auto px-4 py-8 pb-20">
        
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-500 hover:text-pink-600 transition font-medium text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Pesanan Saya
            </a>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-200 text-red-700 p-4 rounded-lg mb-6 flex items-center gap-2">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-100 border border-green-200 text-green-700 p-4 rounded-lg mb-6 flex items-center gap-2">
                {{ session('success') }}
            </div>
        @endif

        <div class="lg:flex lg:space-x-8">
            <div class="lg:w-2/3 space-y-6">
                
                {{-- Header Status --}}
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500">Nomor Invoice</p>
                        <h1 class="text-xl font-bold text-gray-800">{{ $order->invoice_number }}</h1>
                        <p class="text-xs text-gray-400">{{ $order->created_at->format('d M Y, H:i') }} WIB</p>
                    </div>
                    <div>
                        @if($order->status == 'pending')
                            @if($order->payment_proof)
                                {{-- Tambahkan kondisi ini agar tidak lari ke @else --}}
                                <span class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded-full font-bold text-sm border border-yellow-200">
                                    ⏳ Menunggu Verifikasi
                                </span>
                            @else
                                <span class="bg-red-100 text-red-800 px-4 py-2 rounded-full font-bold text-sm border border-red-200">
                                    💳 Belum Dibayar
                                </span>
                            @endif
                        {{-- Tambahkan kondisi eksplisit untuk status lainnya --}}
                        @elseif($order->status == 'waiting_verification')
                            <span class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded-full font-bold text-sm border border-yellow-200">
                                ⏳ Menunggu Verifikasi
                            </span>
                        @elseif($order->status == 'processing')
                            <span class="bg-blue-100 text-blue-800 px-4 py-2 rounded-full font-bold text-sm border border-blue-200">
                                📦 Sedang Diproses
                            </span>
                        @elseif($order->status == 'shipped')
                            <span class="bg-purple-100 text-purple-800 px-4 py-2 rounded-full font-bold text-sm border border-purple-200">
                                🚚 Dikirim
                            </span>
                        @elseif($order->status == 'completed')
                            <span class="bg-green-100 text-green-800 px-4 py-2 rounded-full font-bold text-sm border border-green-200">
                                ✅ Selesai
                            </span>
                        @elseif($order->status == 'cancelled')
                            <span class="bg-gray-200 text-gray-800 px-4 py-2 rounded-full font-bold text-sm border border-gray-300">
                                ❌ Dibatalkan
                            </span>
                        @else
                            {{-- Kondisi jika ada status aneh yang tidak terdefinisi --}}
                            <span class="bg-gray-100 text-gray-600 px-4 py-2 rounded-full font-bold text-sm border border-gray-200">
                                Status: {{ $order->status }}
                            </span>
                        @endif
                        
                    </div>
                </div>

                {{-- Daftar Produk --}}
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center gap-2 mb-4 pb-2 border-b">
                        <span class="text-gray-500 text-sm">Toko:</span>
                        <span class="font-bold text-pink-600">{{ $order->shop->name }}</span>
                    </div>

                    <div class="space-y-6">
                        @foreach($order->items as $item)
                        <div class="flex justify-between items-start border-b pb-6 last:border-0 last:pb-0">
                            <div class="flex gap-4 w-full">
                                <img src="{{ $item->product->image_url }}" class="w-20 h-20 rounded object-cover border bg-gray-50 flex-shrink-0">
                                
                                <div class="flex-1">
                                    <div class="flex justify-between">
                                        <div>
                                            <p class="font-bold text-gray-800">{{ $item->product->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $item->quantity }} x Rp {{ number_format($item->price_at_purchase, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="font-bold text-gray-700">
                                            Rp {{ number_format($item->price_at_purchase * $item->quantity, 0, ',', '.') }}
                                        </div>
                                    </div>
                                    
                                    {{-- AKSES PRODUK DIGITAL --}}
                                    @if($item->product->product_type == 'digital')
                                        <div class="mt-3 flex items-center gap-3">
                                            <span class="text-[10px] bg-blue-100 text-blue-600 px-2 py-1 rounded font-bold uppercase">📄 Digital PDF</span>
                                            
                                            @if(in_array($order->status, ['processing', 'shipped', 'completed']) || $order->payment_status == 'paid')
                                                <a href="{{ route('orders.download', $item->id) }}" class="flex items-center gap-1 text-xs bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 transition font-bold shadow-sm">
                                                    ⬇️ Download Pola
                                                </a>
                                            @elseif($order->status != 'cancelled')
                                                <span class="text-xs text-gray-400 italic">*Tersedia setelah pembayaran diverifikasi</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Informasi Alamat / Digital --}}
                @php
                    $hasPhysical = $order->items->contains(fn($i) => $i->product->product_type === 'physical');
                @endphp
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h2 class="font-bold text-gray-800 mb-2">{{ $hasPhysical ? 'Alamat Pengiriman' : 'Informasi Pesanan Digital' }}</h2>
                    <div class="bg-gray-50 p-4 rounded border border-gray-200 text-sm text-gray-700 whitespace-pre-line leading-relaxed">
                        @if($hasPhysical)
                            {{ $order->shipping_address_snapshot }}
                        @else
                            <div class="flex items-start gap-3 text-blue-700">
                                <span class="text-xl">📧</span>
                                <p>Pesanan ini adalah <strong>Produk Digital</strong>. Tidak ada pengiriman fisik ke alamat. Link download tersedia pada daftar produk di atas segera setelah admin memverifikasi pembayaran Anda.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar Rincian --}}
            <div class="lg:w-1/3 mt-6 lg:mt-0">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 sticky top-4">
                    <h3 class="font-bold text-lg text-gray-800 mb-4">Rincian Tagihan</h3>
                    <div class="space-y-2 text-sm text-gray-600 mb-4 pb-4 border-b">
                        <div class="flex justify-between">
                            <span>Subtotal Produk</span>
                            <span>Rp {{ number_format($order->total_price - $order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between font-bold {{ $order->shipping_cost > 0 ? 'text-gray-600' : 'text-green-600' }}">
                            <span>Ongkos Kirim</span>
                            <span>{{ $order->shipping_cost > 0 ? 'Rp '.number_format($order->shipping_cost, 0, ',', '.') : 'Gratis' }}</span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center mb-6">
                        <span class="font-bold text-gray-800">Total Bayar</span>
                        <span class="text-2xl font-extrabold text-pink-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>

                    @if($order->status == 'pending' && !$order->payment_proof)
                        <form action="{{ route('orders.pay', $order->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf @method('PATCH')
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Upload Bukti Transfer</label>
                                <input type="file" name="payment_proof" required class="w-full text-xs border border-gray-300 rounded-lg p-2">
                            </div>
                            <button type="submit" class="w-full bg-pink-600 text-white font-bold py-3 rounded-lg hover:bg-pink-700 shadow-lg">Kirim Bukti Pembayaran</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </main>
</body>
</html>