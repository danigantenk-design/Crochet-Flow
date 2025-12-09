<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = CartItem::with('product.images')
                    ->where('user_id', Auth::id())
                    ->get();

        return view('cart', compact('cartItems'));
    }

    public function store(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        
        // Validasi input
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Stok tidak mencukupi!');
        }

        $existingItem = CartItem::where('user_id', Auth::id())
                                ->where('product_id', $productId)
                                ->first();

        if ($existingItem) {
            // Cek total stok jika ditambah
            if (($existingItem->quantity + $request->quantity) > $product->stock) {
                 return back()->with('error', 'Stok maksimal tercapai!');
            }
            $existingItem->increment('quantity', $request->quantity);
        } else {
            CartItem::create([
                'user_id' => Auth::id(),
                'product_id' => $productId,
                'quantity' => $request->quantity
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Produk berhasil masuk keranjang!');
    }

    // === FITUR BARU: UPDATE QUANTITY ===
    public function update(Request $request, $id)
    {
        $item = CartItem::with('product')->where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        
        // Ambil tipe aksi dari form (increase / decrease)
        $type = $request->input('type');

        if ($type === 'increase') {
            // Cek stok sebelum nambah
            if ($item->quantity < $item->product->stock) {
                $item->increment('quantity');
            } else {
                return back()->with('error', 'Stok produk ini sudah maksimal!');
            }
        } elseif ($type === 'decrease') {
            // Pastikan tidak kurang dari 1
            if ($item->quantity > 1) {
                $item->decrement('quantity');
            }
        }

        return back();
    }
    // ===================================

    public function destroy($id)
    {
        $item = CartItem::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $item->delete();

        return back()->with('success', 'Barang dihapus dari keranjang.');
    }
}