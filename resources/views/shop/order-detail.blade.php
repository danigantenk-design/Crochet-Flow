<x-app-layout>

@section('content')
<div class="container mx-auto px-4 py-8">
    <a href="{{ route('shop.orders') }}" class="text-gray-500 hover:text-pink-600 mb-4 inline-block">&larr; Kembali ke List Pesanan</a>
    
    <div class="flex justify-between items-start mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Detail Pesanan: {{ $order->invoice_number }}</h2>
        
        <a href="{{ route('shop.orders.label', $order->id) }}" target="_blank" class="bg-gray-200 text-gray-700 px-4 py-2 rounded font-bold hover:bg-gray-300 flex items-center gap-2">
            🖨️ Cetak Label
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-4 rounded mb-6">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-6 rounded-lg shadow border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-4">Daftar Produk</h3>
                @foreach($order->items as $item)
                <div class="flex gap-4 mb-4 border-b pb-4 last:border-0 last:pb-0">
                    <img src="{{ $item->product->images->first()->image_url ?? 'https://placehold.co/100' }}" class="w-16 h-16 rounded object-cover">
                    <div>
                        <p class="font-bold">{{ $item->product->name }}</p>
                        <p class="text-sm text-gray-600">{{ $item->quantity }} x Rp {{ number_format($item->price_at_purchase, 0, ',', '.') }}</p>
                        @if($item->product->product_type == 'digital')
                            <span class="text-xs bg-blue-100 text-blue-600 px-2 py-0.5 rounded">Digital (PDF)</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <div class="bg-white p-6 rounded-lg shadow border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-2">Alamat Pengiriman</h3>
                <div class="bg-gray-50 p-4 rounded text-sm whitespace-pre-line">
                    {{ $order->shipping_address_snapshot }}
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-lg shadow border-t-4 border-pink-600 sticky top-4">
                <h3 class="font-bold text-lg mb-4">Aksi Pesanan</h3>

                @if($order->status == 'processing')
                    <div class="bg-blue-50 p-4 rounded mb-4">
                        <p class="text-blue-800 text-sm font-bold mb-1">Status: Perlu Dikirim</p>
                        <p class="text-blue-600 text-xs">Silakan kemas barang dan input nomor resi.</p>
                    </div>

                    <form action="{{ route('shop.orders.ship', $order->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Kurir</label>
                            <select name="courier_code" class="w-full border rounded p-2">
                                <option value="JNE">JNE</option>
                                <option value="J&T">J&T</option>
                                <option value="SiCepat">SiCepat</option>
                                <option value="POS">POS Indonesia</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nomor Resi</label>
                            <input type="text" name="tracking_number" class="w-full border rounded p-2" required placeholder="Contoh: JP123456789">
                        </div>
                        <button type="submit" class="w-full bg-pink-600 text-white font-bold py-2 rounded hover:bg-pink-700">
                            Kirim Pesanan 🚀
                        </button>
                    </form>

                @elseif($order->status == 'shipped')
                    <div class="bg-purple-50 p-4 rounded mb-4 text-center">
                        <p class="text-purple-800 text-sm font-bold mb-1">Status: Sedang Dikirim</p>
                        <p class="text-gray-600 text-xs">Menunggu pesanan diterima pembeli.</p>
                    </div>
                    
                    @if($order->shipment)
                        <div class="border p-3 rounded bg-gray-50 text-center">
                            <p class="text-xs text-gray-500">Resi {{ $order->shipment->courier_code }}</p>
                            <p class="font-mono font-bold text-lg">{{ $order->shipment->tracking_number }}</p>
                        </div>
                    @endif

                @elseif($order->status == 'completed')
                    <div class="bg-green-100 text-green-800 p-4 rounded text-center font-bold">
                        ✅ Transaksi Selesai
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
</x-app-layout>