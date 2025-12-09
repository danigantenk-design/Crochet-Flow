<x-app-layout>

@section('content')
<div class="bg-white border-b">
    <div class="container mx-auto px-4 py-8 text-center">
        @if($shop->logo_url)
            <img src="{{ $shop->logo_url }}" class="w-24 h-24 rounded-full mx-auto object-cover border-4 border-pink-100 mb-4">
        @else
            <div class="w-24 h-24 rounded-full mx-auto bg-purple-600 text-white flex items-center justify-center text-4xl font-bold border-4 border-purple-100 mb-4">
                {{ substr($shop->name, 0, 1) }}
            </div>
        @endif
        
        <h1 class="text-3xl font-bold text-gray-800">{{ $shop->name }}</h1>
        <p class="text-gray-500 mt-2 max-w-2xl mx-auto">{{ $shop->description ?? 'Toko ini belum memiliki deskripsi.' }}</p>
        
        @if($shop->is_verified)
            <span class="inline-block mt-3 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full font-bold">
                ✓ Toko Terverifikasi
            </span>
        @endif
    </div>
</div>

<div class="container mx-auto px-4 py-12">
    <h2 class="text-xl font-bold mb-6">Produk dari {{ $shop->name }}</h2>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($shop->products as $product)
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                <div class="h-48 bg-gray-200 w-full">
                    <img src="{{ $product->images->first()->image_url ?? 'https://placehold.co/400' }}" class="w-full h-full object-cover">
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-gray-900 truncate">{{ $product->name }}</h3>
                    <p class="text-gray-900 mt-1">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    <a href="{{ route('front.product', $product->slug) }}" class="block mt-3 text-sm text-center border border-gray-300 py-1 rounded hover:bg-gray-50">Lihat Detail</a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500">
                Toko ini belum memiliki produk.
            </div>
        @endforelse
    </div>
</div>
</x-app-layout>