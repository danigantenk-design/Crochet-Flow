<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - CrochetFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">

    @include('layouts.navigation')

    <main class="container mx-auto px-4 py-8">
        
        <nav class="text-sm text-gray-500 mb-6">
            <a href="{{ route('front.index') }}" class="hover:text-pink-600">Home</a> 
            <span class="mx-2">/</span>
            <span class="text-gray-900">{{ $product->name }}</span>
        </nav>

        <div class="mb-6">
            <a href="javascript:history.back()" class="inline-flex items-center text-gray-600 hover:text-pink-600 transition font-medium text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-0 md:gap-8">
                
                <div class="p-6 bg-gray-50" 
                     x-data="{ 
                        activeImage: '{{ $product->images->first()->image_url ?? 'https://placehold.co/600x600?text=No+Image' }}' 
                     }">
                    
                    <div class="aspect-square w-full bg-white rounded-lg overflow-hidden border border-gray-200 mb-4 relative group">
                        <img :src="activeImage" 
                             alt="{{ $product->name }}" 
                             class="w-full h-full object-cover transition duration-300 group-hover:scale-105">
                        
                        <div class="absolute top-4 left-4">
                            @if($product->product_type == 'digital')
                                <span class="bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                                    📄 Pola Digital (PDF)
                                </span>
                            @else
                                <span class="bg-green-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                                    🧶 Produk Fisik
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($product->images->count() > 1)
                        <div class="flex gap-2 overflow-x-auto pb-2">
                            @foreach($product->images as $image)
                                <button @click="activeImage = '{{ $image->image_url }}'" 
                                        class="w-20 h-20 flex-shrink-0 rounded-md overflow-hidden border-2 transition focus:outline-none"
                                        :class="activeImage === '{{ $image->image_url }}' ? 'border-pink-600 ring-1 ring-pink-600' : 'border-gray-200 hover:border-gray-400'">
                                    <img src="{{ $image->image_url }}" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="p-6 md:p-8 flex flex-col justify-center">
                    
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-pink-600 font-bold text-sm uppercase tracking-wider">
                            {{ $product->category->name ?? 'Uncategorized' }}
                        </span>
                        
                        <a href="{{ route('shop.show', $product->shop->id) }}" class="flex items-center gap-2 text-gray-500 hover:text-gray-800 text-sm font-medium transition">
                            <span>🏪 {{ $product->shop->name }}</span>
                        </a>
                    </div>

                    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4 leading-tight">
                        {{ $product->name }}
                    </h1>

                    <div class="text-3xl font-bold text-gray-900 mb-6">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </div>

                    <div class="prose prose-sm text-gray-600 mb-8 max-w-none">
                        <p class="whitespace-pre-line">{{ $product->description }}</p>
                    </div>

                    <div class="flex items-center gap-2 mb-6 text-sm">
                        @if($product->stock > 0)
                            <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                            <span class="text-green-700 font-bold">Stok Tersedia ({{ $product->stock }})</span>
                        @else
                            <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                            <span class="text-red-700 font-bold">Stok Habis</span>
                        @endif
                        
                        <span class="text-gray-300">|</span>
                        <span class="text-gray-500">Berat: {{ $product->weight }} gram</span>
                    </div>

                    <div class="mt-auto pt-6 border-t border-gray-100">
                        @if($product->stock > 0)
                            <form action="{{ route('cart.store', $product->id) }}" method="POST" class="flex flex-col sm:flex-row gap-4">
                                @csrf
                                
                                <div class="w-full sm:w-32">
                                    <label class="sr-only">Jumlah</label>
                                    <div class="relative flex items-center max-w-[8rem]">
                                        <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" 
                                               class="bg-gray-50 border border-gray-300 text-gray-900 text-center text-lg rounded-lg focus:ring-pink-500 focus:border-pink-500 block w-full py-2.5 font-bold" 
                                               required>
                                    </div>
                                </div>

                                <button type="submit" class="flex-1 bg-gray-900 text-white font-bold py-3 px-6 rounded-lg hover:bg-pink-600 transition duration-300 flex items-center justify-center gap-2 shadow-lg transform active:scale-95">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Tambah Keranjang
                                </button>
                            </form>
                        @else
                            <button disabled class="w-full bg-gray-300 text-gray-500 font-bold py-3 px-6 rounded-lg cursor-not-allowed">
                                Stok Habis
                            </button>
                        @endif
                    </div>

                </div>
            </div>
        </div>

    </main>

    {{-- @include('layouts.footer') --}}
</body>
</html>