<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\Category;
use App\Models\User; 
use App\Models\Wallet; // Jangan lupa import Wallet
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
        $ordersCount = Order::where('shop_id', $shop->id)
                            ->whereIn('status', ['processing'])
                            ->count();

        $wallet = \App\Models\Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );
                            
        $income = Order::where('shop_id', $shop->id)
                   ->where('status', 'completed')
                   ->get()
                   ->sum(function($order) {
                       return $order->calculateNetIncome();
                   });

        $pendingIncome = Order::where('shop_id', $shop->id)
                        ->whereIn('status', ['shipped'])
                        ->where('payment_status', 'paid')
                        ->get()
                        ->sum(function($order) {
                            return $order->calculateNetIncome();
                        });

        $totalIncome = Order::where('shop_id', $shop->id)
                        ->where('status', 'completed')
                        ->get()
                        ->sum(fn($order) => $order->calculateNetIncome());

        $ordersCount = Order::where('shop_id', $shop->id)
                            ->where('status', 'processing')
                            ->count();

        return view('shop.index', compact('shop', 'products', 'ordersCount', 'income', 'totalIncome', 'pendingIncome', 'wallet'));
}


    public function create()
    {
        if (Auth::user()->shop) return redirect()->route('shop.index');
        
        $categories = Category::all();
        return view('shop.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'name' => 'required|string|max:255|unique:shops,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description' => 'nullable|string',
            'phone' => 'required|numeric|digits_between:10,15',      
            'address' => 'required|string|min:10',
            'city_id' => 'required|integer', 
        ]);

        // 2. Upload Gambar
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('shop-images', 'public');
        }

        // 3. Simpan Database (SUDAH DIPERBAIKI: Masukkan phone, address, image)
        Shop::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'city_id' => $request->city_id,
            'is_verified' => false, 
            'is_active' => 0,
            
            // Data Penting yang kemarin error
            'image' => $imagePath,       
            'phone' => $request->phone,   
            'address' => $request->address,
        ]);
        
        // Ubah role user jadi seller
        $user = User::findOrFail(Auth::id()); 
        $user->role = 'seller';
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Toko berhasil didaftarkan! Menunggu verifikasi Admin.');
    }

    public function edit()
    {
        $shop = Auth::user()->shop;
        return view('shop.edit', compact('shop'));
    }

    // UPDATE PROFIL & REKENING BANK
    public function update(Request $request)
    {
        $shop = Auth::user()->shop;

        $request->validate([
            'name' => 'required|string|max:255|unique:shops,name,'.$shop->id,
            'description' => 'nullable|string',
            'phone' => 'required|numeric',
            'address' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            // Validasi Bank
            'bank_name' => 'nullable|string',
            'account_number' => 'nullable|numeric',
            'account_holder' => 'nullable|string',
        ]);

        // Update Info Dasar
        $shop->name = $request->name;
        $shop->slug = Str::slug($request->name);
        $shop->description = $request->description;
        $shop->phone = $request->phone;
        $shop->address = $request->address;

        // Update Info Bank (PENTING untuk Withdraw)
        $shop->bank_name = $request->bank_name;
        $shop->account_number = $request->account_number;
        $shop->account_holder = $request->account_holder;
        
        // Update Gambar
        if ($request->hasFile('image')) {
            if ($shop->image) {
                Storage::disk('public')->delete($shop->image);
            }
            $shop->image = $request->file('image')->store('shop-images', 'public');
        }

        $shop->save();
        
        return redirect()->route('shop.index')->with('success', 'Profil toko & rekening berhasil diperbarui!');
    }

    // ==========================================
    // BAGIAN 2: HALAMAN PUBLIK (PREVIEW)
    // ==========================================
    
    public function show($id)
    {
        $shop = Shop::with(['products' => function($query) {
            $query->where('is_active', true);
        }])->findOrFail($id);

        return view('shop.show', compact('shop'));
    }

    // ==========================================
    // BAGIAN 3: MANAJEMEN PESANAN (CORE FEATURE)
    // ==========================================

    // 1. List Pesanan Masuk
    public function orders()
    {
        $shop = Auth::user()->shop;
        
        // Penjual HANYA boleh melihat pesanan yang statusnya:
        // 'processing' (Perlu dikemas), 'shipped' (Dikirim), 'completed' (Selesai)
        $orders = Order::where('shop_id', $shop->id)
                      ->whereIn('status', ['processing', 'shipped', 'completed']) 
                      ->with(['user', 'items.product']) 
                      ->latest()
                      ->paginate(10);

        return view('shop.orders', compact('orders'));
    }

    // 2. Detail Pesanan
    public function showOrder($id)
    {
        $shop = Auth::user()->shop;
        $order = Order::where('shop_id', $shop->id)->where('id', $id)->firstOrFail();
        
        return view('shop.order-detail', compact('order'));
    }

    // 3. Proses Pesanan (Terima Order)
    public function processOrder($id)
    {
        $shop = Auth::user()->shop;
        $order = Order::where('shop_id', $shop->id)->findOrFail($id);

        if ($order->status == 'waiting_verification') {
            $order->update(['status' => 'processing']);
            return back()->with('success', 'Pesanan diterima! Segera kemas barang.');
        }

        return back()->with('error', 'Status pesanan tidak valid.');
    }

    // 4. Kirim Barang (Input Resi)
    public function shipOrder(Request $request, $id)
    {
        $request->validate([
            'tracking_number' => 'required|string|max:50',
            'courier'         => 'required|string|max:50', 
        ]);

        $shop = Auth::user()->shop;

        // Pastikan order status processing
        $order = Order::where('shop_id', $shop->id)
                      ->where('id', $id)
                      ->where('status', 'processing') 
                      ->firstOrFail();

        // Simpan Data Pengiriman
        Shipment::create([
            'order_id'        => $order->id,
            'tracking_number' => $request->tracking_number,
            'courier_code'    => $request->courier,
            'status'          => 'shipping',
            'service_type'    => 'REG'
        ]);

        // Update Order jadi Shipped & Simpan Resi di tabel Order juga (untuk display cepat)
        $order->update([
            'status' => 'shipped',
            'tracking_number' => $request->tracking_number
        ]);

        return back()->with('success', 'Barang berhasil dikirim! Resi telah disimpan.');
    }

    // 5. Cetak Label (Opsional)
    public function printLabel($id)
    {
        $shop = Auth::user()->shop;
        $order = Order::where('shop_id', $shop->id)->where('id', $id)->firstOrFail();

        return view('shop.shipping-label', compact('order', 'shop'));
    }

    // ==========================================
    // BAGIAN 4: KEUANGAN (FINANCE)
    // ==========================================

    public function finance()
    {
        $user = Auth::user();
        $shop = $user->shop;

        // Ambil/Buat Wallet otomatis jika belum ada
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        // Ambil Riwayat Transaksi
        $transactions = $wallet->transactions()->latest()->paginate(10);

                $pendingIncome = Order::where('shop_id', $shop->id)
                        ->whereIn('status', ['shipped'])
                        ->where('payment_status', 'paid')
                        ->get()
                        ->sum(function($order) {
                            return $order->calculateNetIncome();
                        });

        return view('shop.finance', compact('wallet', 'transactions', 'pendingIncome'));
    }
}