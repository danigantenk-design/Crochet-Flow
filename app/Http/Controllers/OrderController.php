<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage; // Tambahkan import Storage

class OrderController extends Controller
{
    // 1. Tampilkan Halaman Checkout
    public function index()
    {
        $user = Auth::user();
        
        $cartItems = CartItem::with(['product.shop', 'product.images'])
                    ->where('user_id', $user->id)
                    ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index');
        }

        // Grouping Item Berdasarkan Toko
        $groupedCartItems = $cartItems->groupBy(function ($item) {
            return $item->product->shop->id;
        });

        // Ambil alamat user
        $addresses = UserAddress::where('user_id', $user->id)
                    ->orderBy('is_primary', 'desc')
                    ->get();

        // Hitung Total + Ongkir Dummy
        $itemTotal = 0;
        foreach($cartItems as $item) {
            $itemTotal += $item->product->price * $item->quantity;
        }

        // Logic Ongkir Dummy: Rp 10.000 per Toko
        $shippingCostPerShop = 10000; 
        $totalShippingCost = $groupedCartItems->count() * $shippingCostPerShop;

        $grandTotal = $itemTotal + $totalShippingCost;

        return view('checkout', compact('groupedCartItems', 'addresses', 'itemTotal', 'totalShippingCost', 'grandTotal'));
    }

    // 2. Proses Checkout (Buat Order)
    public function store(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:user_addresses,id',
        ]);

        $user = Auth::user();
        $address = UserAddress::find($request->address_id);
        
        $addressSnapshot = "{$address->recipient_name} ({$address->phone_number}) \n{$address->full_address}";

        $cartItems = CartItem::with('product')->where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Keranjang kosong.');
        }

        $groupedItems = $cartItems->groupBy(function ($item) {
            return $item->product->shop_id;
        });

        $shippingCostPerShop = 10000;

        DB::beginTransaction();
        try {
            
            foreach ($groupedItems as $shopId => $items) {
                $shopItemTotal = 0;
                foreach ($items as $item) {
                    $shopItemTotal += $item->product->price * $item->quantity;
                }

                $order = Order::create([
                    'user_id' => $user->id,
                    'shop_id' => $shopId,
                    'invoice_number' => 'INV/' . date('Ymd') . '/' . strtoupper(Str::random(5)),
                    'status' => 'pending',
                    'total_price' => $shopItemTotal + $shippingCostPerShop, 
                    'shipping_cost' => $shippingCostPerShop, 
                    'shipping_address_snapshot' => $addressSnapshot,
                    'payment_status' => 'pending',
                ]);

                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price_at_purchase' => $item->product->price,
                    ]);
                }
            }

            CartItem::where('user_id', $user->id)->delete();

            DB::commit();

            return redirect()->route('dashboard')->with('success', 'Order berhasil dibuat! Silakan lakukan pembayaran.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // 3. History Dashboard (List semua order)
    public function history()
    {
        $orders = Order::with(['items.product', 'shop'])
                    ->where('user_id', Auth::id())
                    ->latest()
                    ->get();

        return view('dashboard', compact('orders'));
    }

    // 4. Tampilkan Detail Order (Invoice)
    public function show($id)
    {
        $order = Order::with(['items.product', 'shop'])
                    ->where('user_id', Auth::id())
                    ->where('id', $id)
                    ->firstOrFail();

        // PERBAIKAN DI SINI: Mengarah ke 'orders.show' (Halaman Invoice Pembeli)
        return view('orders.show', compact('order')); 
    }

    // 5. Proses "Saya Sudah Bayar"
    public function markAsPaid($id)
    {
        $order = Order::where('user_id', Auth::id())->where('id', $id)->firstOrFail();

        if ($order->status == 'pending') {
            $order->update([
                'status' => 'processing',
                'payment_status' => 'paid',
            ]);

            return back()->with('success', 'Terima kasih! Pembayaran terkonfirmasi. Penjual akan segera memproses pesananmu.');
        }

        return back()->with('error', 'Pesanan ini sudah dibayar atau status tidak valid.');
    }

    // 6. Download File Digital (Fixed)
    public function downloadDigitalProduct($orderItemId)
    {
        $orderItem = OrderItem::with(['order', 'product'])->findOrFail($orderItemId);

        // Validasi User & Status Bayar
        if ($orderItem->order->user_id !== Auth::id()) {
            abort(403);
        }
        if ($orderItem->order->payment_status !== 'paid') {
            return back()->with('error', 'Silakan selesaikan pembayaran terlebih dahulu.');
        }

        // Ambil path relatif (Hapus '/storage/')
        $relativePath = str_replace('/storage/', '', $orderItem->product->file_url);

        // Pastikan file ada di disk 'public'
        if (!Storage::disk('public')->exists($relativePath)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        // SOLUSI UTAMA: Gunakan path fisik + response()->download()
        $fullPath = Storage::disk('public')->path($relativePath);
        
        return response()->download($fullPath);
    }
}
//     // 6. Download File Digital (Protected)
//     public function downloadDigitalProduct($orderItemId)
//     {
//         // 1. Cari Item berdasarkan ID
//         $orderItem = OrderItem::with(['order', 'product'])->findOrFail($orderItemId);

//         // 2. Cek Kepemilikan
//         if ($orderItem->order->user_id !== Auth::id()) {
//             abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
//         }

//         // 3. Cek Status Pembayaran
//         if ($orderItem->order->payment_status !== 'paid') {
//             return back()->with('error', 'Silakan selesaikan pembayaran terlebih dahulu.');
//         }

//         // 4. Cek Apakah Produk Memang Digital
//         if ($orderItem->product->product_type !== 'digital' || empty($orderItem->product->file_url)) {
//             return back()->with('error', 'Produk ini tidak memiliki file digital.');
//         }

//         // 5. Proses Download
//         $relativePath = str_replace('/storage/', '', $orderItem->product->file_url);

//         if (!Storage::disk('public')->exists($relativePath)) {
//             return back()->with('error', 'File tidak ditemukan di server. Hubungi penjual.');
//         }

//         // Ambil full path fisik filenya
//         $filePath = Storage::disk('public')->path($relativePath);

//         // Return response download bawaan Laravel (Lebih dikenali IDE)
//         return response()->download($filePath);
//     }
// }