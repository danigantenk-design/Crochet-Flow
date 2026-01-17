<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\UserAddress; // Pastikan Model Address di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // 1. Tampilkan Keranjang
    public function index(Request $request)
{
    $cartItems = CartItem::with('product')
                ->where('user_id', Auth::id())
                ->get();

    // Jika permintaan datang dari API/Android
    if ($request->expectsJson() || $request->is('api/*')) {
        return response()->json([
            'success' => true,
            'data'    => $cartItems
        ]);
    }

    // Jika dari web browser
    return view('cart', compact('cartItems'));
}

    // 2. Tambah ke Keranjang
    public function store(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        
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

    // 3. Update Quantity (Increase/Decrease)
    public function update(Request $request, $id)
    {
        $item = CartItem::with('product')->where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        
        $type = $request->input('type');

        if ($type === 'increase') {
            if ($item->quantity < $item->product->stock) {
                $item->increment('quantity');
            } else {
                return back()->with('error', 'Stok produk ini sudah maksimal!');
            }
        } elseif ($type === 'decrease') {
            if ($item->quantity > 1) {
                $item->decrement('quantity');
            }
        }

        return back();
    }

    // 4. Hapus Item
    public function destroy($id)
    {
        $item = CartItem::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $item->delete();

        return back()->with('success', 'Barang dihapus dari keranjang.');
    }

// === FITUR BARU: PERSIAPAN CHECKOUT ===
    public function checkout(Request $request)
    {
        // 1. Validasi: Pastikan ada barang yang dipilih
        if (!$request->has('selected_items') || empty($request->selected_items)) {
            return redirect()->route('cart.index')->with('error', 'Pilih minimal satu produk untuk di-checkout.');
        }

        $user = Auth::user();
        $selectedItemIds = explode(',', $request->selected_items); 

        // 2. Ambil Data Cart Item yang dipilih (Muat relasi product)
        $cartItems = CartItem::with(['product.shop', 'product.images'])
                    ->where('user_id', $user->id)
                    ->whereIn('id', $selectedItemIds)
                    ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Produk yang dipilih tidak valid.');
        }

        // --- [START] LOGIKA PRODUK DIGITAL ---
        // Cek apakah ada setidaknya satu produk fisik
        $hasPhysicalProduct = $cartItems->contains(function ($item) {
            return $item->product->product_type === 'physical';
        });

        // Variabel penanda untuk Blade agar tidak error "Undefined variable"
        $isDigitalOnly = !$hasPhysicalProduct;
        // --- [END] LOGIKA PRODUK DIGITAL ---

        // 3. Grouping per Toko
        $groupedCartItems = $cartItems->groupBy(function ($item) {
            return $item->product->shop->id;
        });

        // 4. Ambil Alamat User (Hanya perlu jika ada produk fisik)
        $addresses = UserAddress::where('user_id', $user->id)
                    ->orderBy('is_primary', 'desc')
                    ->get();

        // 5. Hitung Total Item
        $itemTotal = 0;
        foreach($cartItems as $item) {
            $itemTotal += $item->product->price * $item->quantity;
        }

        // --- [LOGIKA ONGKIR CERDAS] ---
        // Jika hanya produk digital (isDigitalOnly = true), maka ongkir 0.
        $shippingCostPerShop = $isDigitalOnly ? 0 : 10000; 
        
        $totalShippingCost = $groupedCartItems->count() * $shippingCostPerShop;
        $grandTotal = $itemTotal + $totalShippingCost;

        // Pastikan variabel 'isDigitalOnly' dikirim ke view compact()
        return view('checkout', compact(
            'groupedCartItems', 
            'addresses', 
            'itemTotal', 
            'totalShippingCost', 
            'grandTotal', 
            'selectedItemIds',
            'isDigitalOnly' // <-- Penting untuk menghilangkan error di Blade
        ));
    }
    
    
}