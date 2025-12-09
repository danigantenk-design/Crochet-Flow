<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Label Pengiriman - {{ $order->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { padding: 0; margin: 0; }
        }
    </style>
</head>
<body class="bg-gray-100 p-8" onload="window.print()">

    <div class="max-w-2xl mx-auto bg-white border-2 border-black p-8 relative">
        <div class="flex justify-between items-start border-b-2 border-black pb-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold uppercase tracking-wider">CrochetFlow</h1>
                <p class="text-sm mt-1">Marketplace Rajutan & Pola</p>
            </div>
            <div class="text-right">
                <p class="font-mono font-bold text-xl">{{ $order->invoice_number }}</p>
                <p class="text-sm text-gray-500">{{ $order->created_at->format('d M Y') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-8 mb-8">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase mb-1">PENGIRIM:</p>
                <p class="font-bold text-lg">{{ $shop->name }}</p>
                <p class="text-sm">{{ $shop->user->email }}</p> 
            </div>

            <div class="border-l-2 border-dashed border-gray-300 pl-8">
                <p class="text-xs font-bold text-gray-500 uppercase mb-1">PENERIMA:</p>
                <div class="text-lg whitespace-pre-line leading-relaxed font-semibold">
                    {{ $order->shipping_address_snapshot }}
                </div>
            </div>
        </div>

        <div class="border-t-2 border-black pt-4">
            <p class="text-xs font-bold text-gray-500 uppercase mb-2">CATATAN ISI PAKET:</p>
            <ul class="list-disc pl-5 text-sm space-y-1">
                @foreach($order->items as $item)
                    @if($item->product->product_type == 'physical')
                        <li>{{ $item->product->name }} ({{ $item->quantity }} pcs)</li>
                    @endif
                @endforeach
            </ul>
        </div>

        <div class="mt-8 text-center text-xs text-gray-400 border-t pt-4">
            Terima kasih telah berbelanja di CrochetFlow. Silakan video unboxing saat paket diterima.
        </div>
    </div>

    <div class="text-center mt-8 no-print">
        <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded font-bold hover:bg-blue-700">Print Label</button>
        <button onclick="window.close()" class="ml-4 text-gray-600 underline">Tutup</button>
    </div>

</body>
</html>