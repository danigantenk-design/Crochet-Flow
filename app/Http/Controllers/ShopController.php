<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    // ==========================================
    // BAGIAN 1: MANAJEMEN TOKO DASAR
    // ==========================================

    public function index()
    {
        $user = Auth::user();
        if (!$user->shop) return redirect()->route('shop.create');

        $shop = $user->shop;

        if (!$shop->is_verified) return view('shop.pending');

        $products = $shop->products()->latest()->get();
        // Hitung ringkasan order
        $ordersCount = Order::where('shop_id', $shop->id)->where('status', '!=', 'pending')->count();
        $income = Order::where('shop_id', $shop->id)->where('payment_status', 'paid')->sum('total_price');

        return view('shop.index', compact('shop', 'products', 'ordersCount', 'income'));
    }

    public function create()
    {
        if (Auth::user()->shop) return redirect()->route('shop.index');
        return view('shop.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:shops,name',
            'description' => 'nullable|string',
            'city_id' => 'required|integer', 
        ]);

        Shop::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'city_id' => $request->city_id,
            'is_verified' => false, 
        ]);

        $user = Auth::user();
        $user->role = 'seller';
        $user->save();

        return redirect()->route('shop.index')->with('success', 'Selamat! Toko kamu berhasil dibuat.');
    }

    public function edit()
    {
        $shop = Auth::user()->shop;
        return view('shop.edit', compact('shop'));
    }

    public function update(Request $request)
    {
        $shop = Auth::user()->shop;
        $request->validate([
            'name' => 'required|string|max:255|unique:shops,name,'.$shop->id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $shop->name = $request->name;
        $shop->description = $request->description;
        
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('shops', 'public');
            $shop->logo_url = '/storage/' . $path;
        }

        $shop->save();
        return redirect()->route('shop.index')->with('success', 'Profil toko berhasil diperbarui!');
    }

    // ==========================================
    // BAGIAN 2: HALAMAN PUBLIK (PREVIEW)
    // ==========================================
    
    public function show($id)
    {
        // Cari toko berdasarkan ID, load produknya
        $shop = Shop::with(['products' => function($query) {
            $query->where('is_active', true);
        }])->findOrFail($id);

        return view('shops.show', compact('shop'));
    }


    // ==========================================
    // BAGIAN 3: MANAJEMEN PESANAN (CORE FEATURE)
    // ==========================================

    // 1. List Pesanan Masuk
    public function orders()
    {
        $shop = Auth::user()->shop;
        
        // Ambil order yang statusnya BUKAN pending (sudah checkout/bayar)
        // Urutkan dari yang terbaru
        $orders = Order::where('shop_id', $shop->id)
                    ->where('status', '!=', 'pending') 
                    ->with(['items.product', 'user'])
                    ->latest()
                    ->get();

        return view('shop.orders', compact('orders'));
    }

    // 2. Detail Pesanan (Untuk Proses/Input Resi)
    public function showOrder($id)
    {
        $shop = Auth::user()->shop;
        $order = Order::where('shop_id', $shop->id)->where('id', $id)->firstOrFail();
        
        return view('shop.order-detail', compact('order'));
    }

    // 3. Aksi: Proses Pesanan (Dikemas)
    public function processOrder($id)
    {
        $shop = Auth::user()->shop;
        $order = Order::where('shop_id', $shop->id)->where('id', $id)->firstOrFail();

        // Hanya bisa diproses jika status sudah 'pending' (sudah dibayar user tapi blm dikonfirmasi sistem) 
        // atau kita anggap user transfer manual dan seller verifikasi.
        // Di sistem kita, user klik "Sudah Bayar" -> status jadi 'processing'.
        // Jadi seller tinggal melihat yg 'processing'.
        
        // Jika logic pembayaran otomatis: Status awal 'paid', seller ubah ke 'processing'.
        // Jika manual: Status 'processing' (menunggu verifikasi seller).
        
        // Kita anggap seller memverifikasi barang siap dikemas
        $order->update(['status' => 'processing']);

        return back()->with('success', 'Status pesanan diubah menjadi: Sedang Dikemas.');
    }

    // 4. Aksi: Kirim Pesanan (Input Resi)
    public function shipOrder(Request $request, $id)
    {
        $request->validate([
            'tracking_number' => 'required|string|max:50',
            'courier_code' => 'required|string|max:50'
        ]);

        $shop = Auth::user()->shop;
        $order = Order::where('shop_id', $shop->id)->where('id', $id)->firstOrFail();

        // Buat Data Pengiriman
        Shipment::create([
            'order_id' => $order->id,
            'tracking_number' => $request->tracking_number,
            'courier_code' => $request->courier_code,
            'shipped_at' => now(),
        ]);

        // Update Status Order
        $order->update(['status' => 'shipped']);

        return back()->with('success', 'Resi berhasil diinput! Pesanan berstatus: Dikirim.');
    }

    // 5. Cetak Label Pengiriman
    public function printLabel($id)
    {
        $shop = Auth::user()->shop;
        $order = Order::where('shop_id', $shop->id)->where('id', $id)->firstOrFail();

        return view('shop.shipping-label', compact('order', 'shop'));
    }
}