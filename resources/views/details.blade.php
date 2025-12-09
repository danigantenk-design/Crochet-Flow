<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - CrochetFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

    <!-- <nav class="bg-white shadow-md mb-8">
        <div class="flex items-center space-x-4">
    @auth
        <span class="text-gray-700 text-sm">Halo, <b>{{ Auth::user()->full_name }}</b></span>
        
        <form action="{{ route('logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="text-gray-500 hover:text-red-500 text-sm">Logout</button>
        </form>
    @else
        <a href="{{ route('login') }}" class="text-gray-600 hover:text-pink-600 px-3">Login</a>
        <a href="{{ route('register') }}" class="bg-pink-600 text-white px-4 py-2 rounded-lg hover:bg-pink-700">Daftar</a>
    @endauth
</div>
    </nav> -->

    @include('layouts.navigation')

    <div class="container mx-auto px-4 mt-6 mb-2">
    <a href="javascript:history.back()" class="inline-flex items-center text-gray-500 hover:text-pink-600 transition font-medium text-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Kembali
    </a>
</div>

    <main class="container mx-auto px-4">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="md:flex">
                
                <div class="md:w-1/2">
                    <img src="{{ $product->images->first()->image_url ?? 'https://placehold.co/600' }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-96 object-cover bg-gray-100">
                </div>

                <div class="md:w-1/2 p-8">
                    <div class="flex items-center space-x-2 mb-4">
                        <span class="bg-pink-100 text-pink-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                            {{ $product->category->name }}
                        </span>
                        <span class="text-gray-400 text-sm">•</span>
                        <span class="text-gray-500 text-sm">Dijual oleh <b>{{ $product->shop->name }}</b></span>
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                    
                    <p class="text-4xl font-bold text-pink-600 mb-6">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </p>

                    <div class="prose text-gray-600 mb-6">
                        <h3 class="font-bold text-gray-800">Deskripsi:</h3>
                        <p>{{ $product->description }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-8 text-sm text-gray-600">
                        <div>Berat: <span class="font-semibold">{{ $product->weight }} gram</span></div>
                        <div>Stok: <span class="font-semibold">{{ $product->stock }} pcs</span></div>
                        <div>Tipe: <span class="font-semibold uppercase">{{ $product->product_type }}</span></div>
                    </div>

                    <div class="border-t pt-6">
                       <!-- Perhatikan action-nya mengarah ke route cart.store dengan parameter ID produk -->
<form action="{{ route('cart.store', $product->id) }}" method="POST" class="flex space-x-4">
    @csrf
    
    <!-- Input Quantity -->
    <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" 
           class="w-20 border rounded-lg px-3 py-2 text-center focus:ring-pink-500 focus:border-pink-500">
    
    <!-- Tombol Submit -->
    <!-- Tambahkan pengecekan login -->
    @auth
        <button type="submit" class="flex-1 bg-pink-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-pink-700 transition">
            + Masukkan Keranjang
        </button>
    @else
        <a href="{{ route('login') }}" class="flex-1 bg-gray-400 text-white text-center font-bold py-3 px-6 rounded-lg hover:bg-gray-500 transition">
            Login untuk Membeli
        </a>
    @endauth
</form>
                    </div>

                </div>
            </div>
        </div>
    </main>

</body>
</html>              