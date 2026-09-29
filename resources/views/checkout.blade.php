<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - CrochetFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">

    <nav class="bg-white shadow-md mb-8">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="/" class="text-2xl font-bold text-pink-600">🧶 CrochetFlow</a>
        </div>
    </nav>

    <main class="container mx-auto px-4 pb-12"
          x-data="{ 
              selectedAddress: '{{ $addresses->first()->id ?? '' }}' 
          }">
        
        <div class="mt-6 mb-4">
            <a href="{{ route('cart.index') }}" class="inline-flex items-center text-gray-500 hover:text-pink-600 transition font-medium text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Keranjang
            </a>
        </div>

        <h1 class="text-3xl font-bold mb-8 text-gray-800">
            {{ $isDigitalOnly ? 'Konfirmasi Pesanan Digital' : 'Checkout Pengiriman' }}
        </h1>

        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 border border-red-200">{{ session('error') }}</div>
        @endif

        <form action="{{ route('checkout.store') }}" method="POST" class="lg:flex lg:space-x-8">
            @csrf

            {{-- INPUT PENTING: Data Barang yang Dipilih --}}
            <input type="hidden" name="selected_items" value="{{ request('selected_items') }}">

            <div class="lg:w-2/3 space-y-6">
                
                {{-- 1. BAGIAN ALAMAT --}}
                @if(!$isDigitalOnly)
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h2 class="text-lg font-bold mb-4 flex items-center text-gray-800">
                        <span class="bg-pink-100 text-pink-600 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm">1</span>
                        Alamat Pengiriman
                    </h2>

                    @if($addresses->isEmpty())
                        <div class="text-center py-6 bg-red-50 rounded border border-red-100">
                            <p class="text-red-600 mb-2">Kamu belum mengatur alamat pengiriman.</p>
                            <a href="{{ route('profile.address.create') }}" class="inline-block bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700">
                                + Tambah Alamat
                            </a>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($addresses as $addr)
                            <label class="flex items-start p-4 border rounded-lg cursor-pointer transition hover:bg-gray-50"
                                   :class="selectedAddress == '{{ $addr->id }}' ? 'border-pink-500 bg-pink-50 ring-1 ring-pink-500' : 'border-gray-200'">
                                
                                <input type="radio" 
                                       name="address_id" 
                                       value="{{ $addr->id }}" 
                                       x-model="selectedAddress"
                                       class="mt-1 mr-3 text-pink-600 focus:ring-pink-500">
                                
                                <div class="flex-1">
                                    <div class="flex justify-between">
                                        <span class="font-bold text-gray-800">{{ $addr->recipient_name }}</span>
                                        @if($addr->is_primary)
                                            <span class="text-xs bg-pink-200 text-pink-800 px-2 py-0.5 rounded">Utama</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">{{ $addr->full_address }}</p>
                                    <p class="text-sm text-gray-500 mt-1">📞 {{ $addr->phone_number }}</p>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    @endif
                </div>
                @else
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-xl shadow-sm">
                    <div class="flex items-center">
                        <span class="text-2xl mr-3">📧</span>
                        <div>
                            <h3 class="font-bold text-blue-800">Pesanan Digital</h3>
                            <p class="text-sm text-blue-700 leading-relaxed">
                                Pesanan Anda hanya berisi produk digital (pola rajut). Tidak memerlukan pengiriman fisik. Link download akan tersedia segera setelah pembayaran diverifikasi.
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                {{-- 2. RINGKASAN PESANAN --}}
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h2 class="text-lg font-bold mb-4 flex items-center text-gray-800">
                        <span class="bg-pink-100 text-pink-600 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm">
                            {{ $isDigitalOnly ? '1' : '2' }}
                        </span>
                        Ringkasan Pesanan
                    </h2>
                    
                    <div class="space-y-6">
                        @foreach($groupedCartItems as $shopId => $items)
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-500">🏪</span>
                                        <span class="font-bold text-gray-700">{{ $items[0]->product->shop->name }}</span>
                                    </div>
                                </div>

                                <div class="p-4 divide-y divide-gray-100">
                                    @foreach($items as $item)
                                    <div class="py-3 flex justify-between items-start">
                                        <div class="flex gap-3">
                                            <img src="{{ $item->product->image_url }}"  
                                            fetchpriority="high" loading="lazy"
                                                 alt="{{ $item->product->name }}"
                                                 class="w-14 h-14 rounded object-cover border bg-gray-100">
                                            <div>
                                                <p class="font-semibold text-gray-800 text-sm">{{ $item->product->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $item->quantity }} x Rp {{ number_format($item->product->price, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                        <div class="font-bold text-gray-700 text-sm">
                                            Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="bg-gray-50 px-4 py-2 flex justify-between items-center text-sm">
                                    <span class="text-gray-600">Ongkos Kirim</span>
                                    <span class="font-bold {{ $shopHasPhysical = $items->contains(fn($i) => $i->product->product_type == 'physical') ? 'text-pink-700' : 'text-green-600' }}">
                                        {{ $shopHasPhysical ? 'Rp 10.000' : 'Gratis (Digital)' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- 3. KOLOM KANAN: TOTAL & QRIS --}}
            <div class="lg:w-1/3 mt-6 lg:mt-0">
                <div class="sticky top-4 space-y-4">
                    {{-- KARTU RINCIAN PEMBAYARAN --}}
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="text-lg font-bold mb-6 text-gray-800">Rincian Pembayaran</h3>
                        
                        <div class="space-y-3 text-sm text-gray-600 mb-6">
                            <div class="flex justify-between">
                                <span>Total Harga Barang</span>
                                <span>Rp {{ number_format($itemTotal, 0, ',', '.') }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span>Total Ongkos Kirim</span>
                                <span>{{ $totalShippingCost > 0 ? 'Rp ' . number_format($totalShippingCost, 0, ',', '.') : 'Gratis' }}</span>
                            </div>

                            <div class="flex justify-between text-xs text-gray-400 border-t pt-3">
                                <span>Biaya Layanan</span>
                                <span>Gratis</span>
                            </div>
                        </div>

                        <div class="border-t border-dashed border-gray-300 pt-4 flex justify-between mb-6">
                            <span class="text-lg font-bold text-gray-800">Total Tagihan</span>
                            <span class="text-xl font-extrabold text-pink-600">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                        </div>

                        @if(!$isDigitalOnly && $addresses->isEmpty())
                             <button disabled class="w-full bg-gray-300 text-white font-bold py-3 rounded-lg cursor-not-allowed">
                                Pilih Alamat Dulu
                            </button>
                        @else
                            <button type="submit" class="w-full bg-gray-900 text-white font-bold py-3.5 rounded-lg hover:bg-gray-800 transition shadow-lg transform active:scale-95">
                                Konfirmasi Pembayaran
                            </button>
                        @endif
                    </div>

                    {{-- KARTU QRIS (DI BAWAH RINCIAN) --}}
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4 text-center">Metode Pembayaran: QRIS</p>
                        
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                            {{-- Tampilan Gambar QRIS --}}
                            <div class="flex justify-center mb-4">
                                <img src="{{ asset('images/qris_pembayaran.jpg') }}" 
                                fetchpriority="high" loading="lazy"
                                    alt="QRIS CrochetFlow" 
                                    class="w-48 h-auto shadow-sm border-4 border-white rounded-lg">
                            </div>

                            {{-- Tombol Unduh QRIS --}}
                            <a href="{{ asset('images/qris_pembayaran.jpg') }}" 
                               download="QRIS-CrochetFlow.jpg" 
                               class="flex items-center justify-center gap-2 w-full py-2.5 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Unduh QRIS
                            </a>
                        </div>

                        <div class="mt-4 p-3 bg-pink-50 rounded-lg">
                            <p class="text-[12px] text-pink-700 leading-relaxed">
                                <strong>Cara Bayar:</strong> <br>
                                1. Scan QRIS di atas dengan aplikasi Bank/E-wallet <br>
                                2. Klik tombol <b>Konfirmasi Pembayaran</b> <br>
                                3. Kirim bukti transfer di halaman Dashboard.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </main>
</body>
</html>