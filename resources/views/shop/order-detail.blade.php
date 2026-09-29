@extends('layouts.seller')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">

    {{-- Tombol Kembali --}}
    <a href="{{ route('shop.orders') }}" class="inline-flex items-center text-gray-500 hover:text-pink-600 mb-6 transition font-medium text-sm">
        <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Daftar Pesanan
    </a>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- KOLOM KIRI: Detail Produk --}}
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex justify-between items-start mb-4 border-b pb-4">
                    <div>
                        <h3 class="font-bold text-xl text-gray-800">Invoice #{{ $order->invoice_number }}</h3>
                        <p class="text-sm text-gray-500">{{ $order->created_at->format('d F Y, H:i') }} WIB</p>
                    </div>
                    <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">
                        {{ str_replace('_', ' ', $order->status) }}
                    </span>
                </div>

                <div class="space-y-4">
                    @foreach($order->items as $item)
                    <div class="flex gap-4">
                        <img src="{{ $item->product->image_url }}" fetchpriority="high" loading="lazy" class="w-16 h-16 rounded bg-gray-100 object-cover border">
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-800 text-sm">{{ $item->product->name }}</h4>
                            <p class="text-xs text-gray-500">{{ $item->quantity }} x Rp {{ number_format($item->price_at_purchase, 0, ',', '.') }}</p>
                        </div>
                        <div class="text-right font-bold text-gray-700 text-sm">
                            Rp {{ number_format($item->price_at_purchase * $item->quantity, 0, ',', '.') }}
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-6 pt-4 border-t flex justify-between items-center">
                    <span class="font-bold text-gray-600">Total Pembayaran</span>
                    <span class="font-bold text-xl text-pink-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Info Pengiriman --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">🚚 Info Pengiriman</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500 text-xs uppercase font-bold tracking-wider">Penerima</p>
                        <p class="font-bold text-gray-800 text-base">{{ $order->user->full_name }}</p>
                        <p class="text-gray-600">{{ $order->user->email }}</p>
                        <p class="text-gray-600">{{ $order->user->phone_number ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs uppercase font-bold tracking-wider">Alamat Tujuan</p>
                        {{-- Mengambil dari kolom shipping_address_snapshot --}}
                        <p class="text-gray-800 leading-relaxed">
                            @if($order->shipping_address_snapshot)
                                {{ $order->shipping_address_snapshot }}
                            @else
                                {{-- Fallback jika snapshot kosong, ambil dari tabel user_addresses lewat relasi --}}
                                {{ $order->user->address->address_line ?? 'Alamat belum diatur' }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Aksi --}}
        <div class="space-y-6">
            
            {{-- STATUS: WAITING VERIFICATION --}}
            @if($order->status == 'waiting_verification')
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                <h4 class="font-bold text-yellow-800 mb-2">⚡ Perlu Diproses</h4>
                <p class="text-sm text-yellow-700 mb-4">Pembeli sudah melakukan checkout. Konfirmasi jika stok tersedia dan siap kirim.</p>
                
                <form action="{{ route('shop.order.process', $order->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow transition">
                        ✅ Terima Pesanan
                    </button>
                </form>
            </div>
            @endif

            {{-- STATUS: PROCESSING (INPUT RESI) --}}
            @if($order->status == 'processing')
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                <h4 class="font-bold text-blue-800 mb-2">📦 Siap Dikirim</h4>
                <p class="text-sm text-blue-700 mb-4">Silakan kemas barang dan masukkan nomor resi pengiriman di bawah ini.</p>
                
                <form action="{{ route('shop.order.ship', $order->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="block text-xs font-bold text-blue-800 mb-1">Pilih Kurir</label>
                        <select name="courier" class="w-full text-sm border-blue-200 rounded focus:ring-blue-500">
                            <option value="JNE">JNE</option>
                            <option value="J&T">J&T</option>
                            <option value="Sicepat">Sicepat</option>
                            <option value="Pos Indonesia">Pos Indonesia</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-blue-800 mb-1">Nomor Resi</label>
                        <input type="text" name="tracking_number" required placeholder="Contoh: JP12345678" class="w-full text-sm border-blue-200 rounded focus:ring-blue-500">
                    </div>
                    <button type="submit" onclick="return confirm('Pastikan resi benar. Lanjutkan?')" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow transition">
                        🚚 Kirim Barang
                    </button>
                </form>
            </div>
            @endif

            {{-- STATUS: SHIPPED --}}
            @if($order->status == 'shipped')
            <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center">
                <div class="bg-green-100 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fa-solid fa-truck-fast text-green-600 text-xl"></i>
                </div>
                <h4 class="font-bold text-green-800">Sedang Dikirim</h4>
                <p class="text-sm text-green-700 mt-2">
                    Resi: <span class="font-mono font-bold bg-white px-2 py-1 rounded border border-green-200">{{ $order->tracking_number ?? '-' }}</span>
                </p>
                <p class="text-xs text-gray-500 mt-4">Menunggu pembeli mengkonfirmasi penerimaan barang.</p>
            </div>
            @endif

            {{-- STATUS: COMPLETED --}}
            @if($order->status == 'completed')
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 text-center">
                <div class="bg-green-500 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3 text-white">
                    <i class="fa-solid fa-check text-xl"></i>
                </div>
                <h4 class="font-bold text-gray-800">Pesanan Selesai</h4>
                <p class="text-sm text-gray-500 mt-2">Dana telah diteruskan ke saldo dompet Anda.</p>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection