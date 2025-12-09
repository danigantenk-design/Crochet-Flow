<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // 1. Dashboard Utama Admin
    public function index()
    {
        // Ambil Statistik
        $totalUsers = User::count();
        $totalShops = Shop::count();
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', 'completed')->sum('total_price'); // Hitung omzet kotor

        // Ambil Toko yang Belum Diverifikasi (is_verified = 0)
        $pendingShops = Shop::with('user')->where('is_verified', false)->get();

        return view('admin.dashboard', compact('totalUsers', 'totalShops', 'totalOrders', 'totalRevenue', 'pendingShops'));
    }

    // 2. Aksi Menyetujui Toko
    public function approveShop($id)
    {
        $shop = Shop::findOrFail($id);
        
        $shop->is_verified = true;
        $shop->save();

        return back()->with('success', "Toko '{$shop->name}' berhasil diverifikasi!");
    }

    // 3. Aksi Menolak/Hapus Toko (Opsional)
    public function rejectShop($id)
    {
        $shop = Shop::findOrFail($id);
        
        // Kembalikan role user jadi buyer biasa
        $user = $shop->user;
        $user->role = 'buyer';
        $user->save();

        // Hapus toko
        $shop->delete();

        return back()->with('success', "Pengajuan toko ditolak dan dihapus.");
    }
}