<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\CartItem as Cart;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function apiIndex()
    {
        $userId = Auth::id();
        
        // PERBAIKAN: Tambahkan 'product.images' agar data gambar ikut terkirim ke mobile
        $cartItems = Cart::with(['product.shop', 'product.images'])
            ->where('user_id', $userId) 
            ->get();

        return response()->json([
            'success' => true,
            'data' => $cartItems
        ]);
    }

    public function apiStore(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);

        // PERBAIKAN: Gunakan auth()->id() dengan kurung
        $cart = Cart::where('user_id', Auth::id())
                    ->where('product_id', $request->product_id)
                    ->first();

        if ($cart) {
            $cart->increment('quantity', $request->quantity);
        } else {
            $cart = Cart::create([
                'user_id' => Auth::id(), // PERBAIKAN: auth()->id()
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'shop_id' => $product->shop_id 
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang',
            'data' => $cart
        ]);
    }

    public function apiUpdate(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        
        // PERBAIKAN: auth()->id()
        $cart = Cart::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $cart->update(['quantity' => $request->quantity]);

        return response()->json([
            'success' => true,
            'message' => 'Jumlah berhasil diperbarui'
        ]);
    }

    public function apiDestroy($id)
    {
        // PERBAIKAN: auth()->id()
        $cart = Cart::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item dihapus dari keranjang'
        ]);
    }
}