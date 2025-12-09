<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CrochetFlow - Rajutan & Pola</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Hide scrollbar for category list */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">

    @include('layouts.navigation')

    <header class="bg-pink-100 py-16">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-800 mb-4 tracking-tight">
                Temukan Karya <span class="text-pink-600">Rajutan</span> Terbaik
            </h1>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                Marketplace khusus untuk pecinta Crochet & Amigurumi. Beli produk jadi atau pola digital langsung dari kreator lokal.
            </p>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        
        <div class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold text-gray-800">Jelajahi Kategori</h2>
                @if(request('category') || request('search'))
                    <a href="{{ route('front.index') }}" class="text-sm text-pink-600 font-semibold hover:underline">
                        Reset Filter
                    </a>
                @endif
            </div>

            <div class="flex space-x-3 overflow-x-auto no-scrollbar pb-2">
                
                <a href="{{ route('front.index') }}" 
                   class="whitespace-nowrap px-6 py-2 rounded-full border transition font-medium
                   {{ !request('category') ? 'bg-pink-600 text-white border-pink-600 shadow-md' : 'bg-white text-gray-600 border-gray-300 hover:border-pink-500 hover:text-pink-500' }}">
                   Semua
                </a>

                @foreach($categories as $category)
                    <a href="{{ route('front.index', array_merge(request()->query(), ['category' => $category->slug])) }}" 
                       class="whitespace-nowrap px-6 py-2 rounded-full border transition font-medium
                       {{ request('category') == $category->slug ? 'bg-pink-600 text-white border-pink-600 shadow-md' : 'bg-white text-gray-600 border-gray-300 hover:border-pink-500 hover:text-pink-500' }}">
                       {{ $category->name }}
                    </a>
                @endforeach

            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            @forelse($products as $product)
            <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition duration-300 overflow-hidden group border border-gray-100 flex flex-col h-full">
                <div class="h-64 bg-gray-100 w-full overflow-hidden relative">
                    <img src="{{ $product->images->first()->image_url ?? 'https://placehold.co/400' }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    
                    <div class="absolute top-3 right-3">
                         @if($product->product_type == 'digital')
                            <span class="bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded shadow-md">
                                PDF
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="p-5 flex flex-col flex-grow">
                    <div class="mb-2">
                        <span class="text-xs text-pink-500 font-bold uppercase tracking-wide">
                            {{ $product->category->name }}
                        </span>
                    </div>
                    
                    <h3 class="text-lg font-bold text-gray-900 leading-tight mb-1 truncate" title="{{ $product->name }}">
                        {{ $product->name }}
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">{{ $product->shop->name }}</p>

                    <div class="mt-auto pt-4 border-t border-gray-50 flex justify-between items-center">
                        <span class="text-xl font-extrabold text-gray-900">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </span>
                    </div>
                    
                    <a href="{{ route('front.product', $product->slug) }}" 
                       class="block mt-4 w-full bg-gray-900 text-white text-center py-2.5 rounded-lg font-semibold hover:bg-pink-600 transition">
                        Lihat Detail
                    </a>
                </div>
            </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 text-lg">Belum ada produk untuk kategori ini.</p>
                    <a href="{{ route('front.index') }}" class="text-pink-600 font-bold hover:underline mt-2 inline-block">Lihat Semua Produk</a>
                </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $products->withQueryString()->links() }}
        </div>
    </main>
    
    <footer class="bg-gray-800 text-white py-8 mt-12 border-t border-gray-700">
        <div class="container mx-auto text-center px-4">
            <h3 class="text-xl font-bold mb-2">🧶 CrochetFlow</h3>
            <p class="text-gray-400 text-sm mb-4">Tempat bertemunya para pecinta rajutan Indonesia.</p>
            <p class="text-gray-500 text-xs">&copy; 2025 CrochetFlow Project. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>