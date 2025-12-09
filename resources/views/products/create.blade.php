@extends('layouts.seller') {{-- Layout Baru --}}

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-sm border border-gray-200">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h2 class="text-2xl font-bold text-gray-800">Tambah Produk Baru</h2>
        <a href="{{ route('shop.index') }}" class="text-gray-500 hover:text-pink-600 font-bold text-sm">Batal</a>
    </div>

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        </form>
</div>
@endsection