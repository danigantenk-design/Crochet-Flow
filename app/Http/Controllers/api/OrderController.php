<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\CartItem; 

class OrderController extends Controller
{
    // 1. Ambil Riwayat Pesanan Buyer
    // public function apiHistory()
    // {
    //     $orders = Order::with(['items.product', 'shop'])
    //         ->where('user_id', Auth::id()) 
    //         ->latest()
    //         ->get();

    //     return response()->json([
    //         'success' => true,
    //         'data' => $orders
    //     ]);
    // }

  public function apiHistory()
{
    try {
        // PERBAIKAN: Gunakan auth()->id() dengan kurung
        $userId = Auth::id(); 
        
        // Eager Load 'items.product.images' agar gambar muncul di mobile
        $orders = Order::with(['items.product.images', 'shop'])
            ->where('user_id', $userId)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    } catch (\Exception $e) {
        // Jika masih error 500, ini akan memberitahu letak kesalahannya
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
    // 2. Fungsi Checkout (Buat Pesanan Baru dari Mobile)
    public function apiStore(Request $request)
    {
        $request->validate([
            'shop_id' => 'required',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'total_price' => 'required|numeric',
            'cart_ids' => 'required|array', 
            'shipping_address' => 'required|string', 
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $order = Order::create([
                    'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
                    'user_id' => Auth::id(), // PERBAIKAN: kurung
                    'shop_id' => $request->shop_id,
                    'total_price' => $request->total_price,
                    'shipping_address_snapshot' => $request->shipping_address, // Simpan alamat saat checkout
                    'status' => 'pending',
                    'payment_status' => 'unpaid',
                ]);

                foreach ($request->items as $item) {
                    $product = Product::find($item['product_id']);
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price_at_purchase' => $product->price,
                    ]);

                    // TAMBAHAN: Hapus item dari keranjang setelah sukses checkout
                    CartItem::where('user_id', Auth::id())
                        ->where('product_id', $item['product_id'])
                        ->delete();
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Pesanan berhasil dibuat dan keranjang telah dibersihkan',
                    'order' => $order
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // 3. Upload Bukti Pembayaran dari Mobile
    public function apiUploadPayment(Request $request, $id)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        // PERBAIKAN: auth()->id()
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        if ($request->hasFile('payment_proof')) {
            if ($order->payment_proof) {
                Storage::disk('public')->delete($order->payment_proof);
            }

            $path = $request->file('payment_proof')->store('payment_proofs', 'public');
            
            $order->update([
                'payment_proof' => $path,
                'status' => 'waiting_verification'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bukti pembayaran berhasil diunggah.'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'File tidak ditemukan'], 400);
    }

    // 4. Link Download Pola Digital
    public function apiDownload($orderItemId)
    {
        $item = OrderItem::with(['order', 'product'])
            ->whereHas('order', function($q) {
                // PERBAIKAN: auth()->id()
                $q->where('user_id', Auth::id())->where('status', 'completed');
            })
            ->findOrFail($orderItemId);

        if ($item->product->product_type !== 'digital' || !$item->product->file_path) {
            return response()->json(['message' => 'Produk ini bukan produk digital'], 403);
        }

        return response()->json([
            'success' => true,
            'download_url' => asset('storage/' . $item->product->file_path)
        ]);
    }
}