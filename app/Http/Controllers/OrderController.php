<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CartItem;
use App\Models\OrderItem;
use App\Models\UserAddress;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; 

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

        // Cek apakah ada produk fisik
        $hasPhysicalProduct = $cartItems->contains(function ($item) {
            return $item->product->product_type === 'physical';
        });

        $isDigitalOnly = !$hasPhysicalProduct;

        $groupedCartItems = $cartItems->groupBy(function ($item) {
            return $item->product->shop->id;
        });

        $addresses = UserAddress::where('user_id', $user->id)
                    ->orderBy('is_primary', 'desc')
                    ->get();

        $itemTotal = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });

        // Jika hanya digital, ongkir Rp 0
        $shippingCostPerShop = $isDigitalOnly ? 0 : 10000; 
        $totalShippingCost = $groupedCartItems->count() * $shippingCostPerShop;

        $grandTotal = $itemTotal + $totalShippingCost;

        return view('checkout', compact('groupedCartItems', 'addresses', 'itemTotal', 'totalShippingCost', 'grandTotal', 'isDigitalOnly'));
    }

    // 2. Proses Checkout (Buat Order)
    public function store(Request $request)
    {
        $user = Auth::user();
        $selectedIds = explode(',', $request->selected_items);

        $cartItems = CartItem::with('product')
                        ->where('user_id', $user->id)
                        ->whereIn('id', $selectedIds)
                        ->get();

        if ($cartItems->isEmpty()) return back()->with('error', 'Tidak ada barang.');

        $hasPhysical = $cartItems->contains(fn($item) => $item->product->product_type === 'physical');

        if ($hasPhysical) {
            $request->validate(['address_id' => 'required|exists:user_addresses,id']);
        }

        $address = UserAddress::find($request->address_id);
        $groupedItems = $cartItems->groupBy(fn($item) => $item->product->shop_id);

        DB::beginTransaction();
        try {
            foreach ($groupedItems as $shopId => $items) {
                $shopHasPhysical = $items->contains(fn($i) => $i->product->product_type === 'physical');
                $shippingCost = $shopHasPhysical ? 10000 : 0;
                $shopItemTotal = $items->sum(fn($i) => $i->product->price * $i->quantity);

                $order = Order::create([
                    'user_id' => $user->id,
                    'shop_id' => $shopId,
                    'invoice_number' => 'INV/' . date('Ymd') . '/' . strtoupper(Str::random(5)),
                    'status' => 'pending',
                    'total_price' => $shopItemTotal + $shippingCost,
                    'shipping_cost' => $shippingCost,
                    'shipping_address_snapshot' => $address ? $address->full_address : 'Digital Product (No Shipping)',
                    'payment_status' => 'pending',
                ]);

                foreach ($items as $item) {
                    // HITUNG KOMISI DISINI
                    $rate = ($item->product->product_type === 'digital') ? 0.15 : 0.10;
                    $commission = ($item->product->price * $item->quantity) * $rate;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price_at_purchase' => $item->product->price,
                        'commission_fee' => $commission, // SIMPAN KE DATABASE
                    ]);
                }
            }

            CartItem::where('user_id', $user->id)->whereIn('id', $selectedIds)->delete();
            DB::commit();
            return redirect()->route('dashboard')->with('success', 'Order berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
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

        if ($order->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
        abort(403);
    }

    return view('orders.show', compact('order')); 
    }

    // 5. Proses "Saya Sudah Bayar" (Upload Bukti)
    public function markAsPaid(Request $request, Order $order)
    {
        // 1. Validasi Input Gambar
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', 
        ]);

        // 2. Pastikan yang upload adalah pemilik order (Logika Loose Comparison !=)
        if (Auth::id() != $order->user_id) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        // 3. Simpan Gambar
        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('payment-proofs', 'public');
            
            // Simpan path ke database
            $order->update([
                'payment_proof' => $path,
                'status' => 'waiting_verification',
            ]);
        }

        return back()->with('success', 'Bukti pembayaran berhasil dikirim! Menunggu verifikasi.');
    }

    // 6. Download File Digital
    public function downloadDigitalProduct($orderItemId)
    {
        $orderItem = OrderItem::with(['order', 'product'])->findOrFail($orderItemId);

        // Validasi User & Status Bayar
        if ($orderItem->order->user_id != Auth::id()) {
            abort(403);
        }
        
        // Hanya boleh download jika processing, shipped, atau completed
        // (Tergantung kebijakanmu, biasanya setelah 'paid')
        if (!in_array($orderItem->order->status, ['processing', 'shipped', 'completed']) && $orderItem->order->payment_status != 'paid') {
            return back()->with('error', 'Silakan selesaikan pembayaran terlebih dahulu.');
        }

        // Ambil path relatif
        $relativePath = str_replace('/storage/', '', $orderItem->product->file_url);

        // Pastikan file ada
        if (!Storage::disk('public')->exists($relativePath)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        $fullPath = Storage::disk('public')->path($relativePath);
        
        return response()->download($fullPath);
    }

    // 7. Batalkan Pesanan
    public function cancel($id)
    {
        $order = Order::with('items.product')->where('user_id', Auth::id())->where('id', $id)->firstOrFail();

        // Cek Status: Hanya boleh cancel jika masih pending
        if ($order->status == 'pending') {
            
            DB::transaction(function () use ($order) {
                // 1. Kembalikan Stok Barang (Restock)
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                        $item->product->decrement('sold_count', $item->quantity);
                    }
                }

                // 2. Ubah Status jadi Cancelled
                $order->update(['status' => 'cancelled']);
            });

            return back()->with('success', 'Pesanan berhasil dibatalkan. Stok barang telah dikembalikan.');
        }

        return back()->with('error', 'Pesanan tidak dapat dibatalkan karena sudah diproses atau dikirim.');
    }
    

    public function confirmReceived($id)
    {
        $order = Order::with(['shop', 'items'])->findOrFail($id);
        $user = Auth::user();

        // Pastikan hanya pembeli yang bisa konfirmasi
        if ($order->user_id !== $user->id) {
            return back()->with('error', 'Akses ditolak.');
        }

        DB::transaction(function () use ($order) {
            // 1. Update status pesanan jadi SELESAI
            $order->update(['status' => 'completed']);

            // 2. Hitung pendapatan bersih penjual (Total - Komisi)
            // Gunakan fungsi calculateNetIncome yang sudah kita buat di Model Order
            $netAmount = $order->calculateNetIncome();

            // 3. Tambahkan saldo ke dompet penjual
            $sellerWallet = Wallet::firstOrCreate(
                ['user_id' => $order->shop->user_id],
                ['balance' => 0]
            );
            $sellerWallet->increment('balance', $netAmount);

            // 4. Catat riwayat transaksi dompet
            WalletTransaction::create([
                'wallet_id' => $sellerWallet->id,
                'type' => 'credit',
                'amount' => $netAmount,
                'description' => 'Penjualan: ' . $order->invoice_number,
                'reference_id' => $order->id,
                'reference_type' => 'order'
            ]);
        });

        return back()->with('success', 'Pesanan selesai! Saldo telah diteruskan ke penjual.');
    }
}