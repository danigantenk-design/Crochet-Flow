<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - CrochetFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

    @include('layouts.navigation')

    <main class="container mx-auto px-4 py-8">

        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 border border-red-200">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4 border border-green-200">
                ✅ {{ session('success') }}
            </div>
        @endif

        <div class="mb-6">
            <a href="javascript:history.back()" class="inline-flex items-center text-gray-600 hover:text-pink-600 transition font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Lanjut Belanja
            </a>
        </div>

        @if($cartItems->isEmpty())
            <div class="text-center py-16 bg-white rounded-lg shadow border border-gray-100">
                <div class="text-6xl mb-4">🛒</div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Keranjang kamu kosong</h3>
                <p class="text-gray-500 mb-6">Yuk isi dengan rajutan-rajutan lucu!</p>
                <a href="/" class="bg-pink-600 text-white px-8 py-3 rounded-full font-bold hover:bg-pink-700 transition shadow-lg">
                    Mulai Belanja
                </a>
            </div>
        @else
            <div class="flex flex-col lg:flex-row gap-8">
                
                <div class="w-full lg:w-3/4">
                    <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-100">
                        <table class="w-full text-left">
                            <thead class="bg-pink-50 text-gray-700 uppercase text-xs font-bold tracking-wider">
                                <tr>
                                    <th class="py-4 px-4">Produk</th>
                                    <th class="py-4 px-4 hidden md:table-cell">Harga</th>
                                    <th class="py-4 px-4 text-center">Jumlah</th>
                                    <th class="py-4 px-4 hidden md:table-cell">Total</th>
                                    <th class="py-4 px-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @php $grandTotal = 0; @endphp
                                @foreach($cartItems as $item)
                                @php 
                                    $subtotal = $item->product->price * $item->quantity;
                                    $grandTotal += $subtotal;
                                @endphp
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-4 px-4">
                                        <div class="flex items-center space-x-4">
                                            <img src="{{ $item->product->images->first()->image_url ?? 'https://placehold.co/100' }}" 
                                                 class="w-16 h-16 object-cover rounded border border-gray-200">
                                            <div>
                                                <p class="font-bold text-gray-800 text-sm md:text-base">{{ $item->product->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $item->product->shop->name }}</p>
                                                <p class="text-pink-600 font-bold text-sm md:hidden mt-1">
                                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-4 px-4 text-gray-600 hidden md:table-cell">
                                        Rp {{ number_format($item->product->price, 0, ',', '.') }}
                                    </td>

                                    <td class="py-4 px-4">
                                        <div class="flex items-center justify-center">
                                            <form action="{{ route('cart.update', $item->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="type" value="decrease">
                                                <button type="submit" class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 hover:bg-pink-100 hover:text-pink-600 font-bold flex items-center justify-center transition">
                                                    -
                                                </button>
                                            </form>

                                            <span class="mx-3 font-semibold w-6 text-center text-gray-800">{{ $item->quantity }}</span>

                                            <form action="{{ route('cart.update', $item->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="type" value="increase">
                                                <button type="submit" class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 hover:bg-pink-100 hover:text-pink-600 font-bold flex items-center justify-center transition">
                                                    +
                                                </button>
                                            </form>
                                        </div>
                                    </td>

                                    <td class="py-4 px-4 font-bold text-pink-600 hidden md:table-cell">
                                        Rp {{ number_format($subtotal, 0, ',', '.') }}
                                    </td>

                                    <td class="py-4 px-4 text-center">
                                        <form action="{{ route('cart.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus barang ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-500 transition tooltip" title="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="w-full lg:w-1/4">
                    <div class="bg-white p-6 rounded-lg shadow border border-gray-100 sticky top-24">
                        <h3 class="font-bold text-gray-800 mb-4 text-lg">Ringkasan Belanja</h3>
                        
                        <div class="flex justify-between mb-2 text-gray-600">
                            <span>Total Barang</span>
                            <span>{{ $cartItems->sum('quantity') }} pcs</span>
                        </div>
                        
                        <div class="border-t border-gray-100 my-4"></div>

                        <div class="flex justify-between mb-6 text-xl font-bold text-gray-900">
                            <span>Total</span>
                            <span class="text-pink-600">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                        </div>

                        <a href="{{ route('checkout.index') }}" class="block w-full">
                            <button class="w-full bg-pink-600 text-white font-bold py-3 rounded-lg hover:bg-pink-700 transition shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                Checkout Sekarang
                            </button>
                        </a>
                    </div>
                </div>

            </div>
        @endif
    </main>

</body>
</html>