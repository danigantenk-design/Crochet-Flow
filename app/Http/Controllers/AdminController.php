<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shop;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Courier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public $msg;

    public function index()
    {
        $totalUsers = User::count();
        $totalShops = Shop::count();
        $totalOrders = Order::count();
        
        // Omzet (Total perputaran uang)
        $totalRevenue = Order::where('status', 'completed')->sum('total_price');
        
        // Profit Admin (Total komisi dari order_items)
        $totalProfit = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->sum('order_items.commission_fee');

        $pendingShops = Shop::with('user')->where('is_verified', false)->get();

        $pendingPayments = Order::with(['user', 'shop'])
                            ->where('status', 'waiting_verification') 
                            ->whereNotNull('payment_proof')
                            ->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalShops', 'totalOrders', 'totalRevenue', 'totalProfit', 'pendingShops', 'pendingPayments'
        ));
    }

    // MANAJEMEN EKSPEDISI
    public function couriers()
    {
        $couriers = Courier::latest()->get();
        return view('admin.couriers.index', compact('couriers'));
    }

    public function storeCourier(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:couriers|max:10',
            'name' => 'required|max:255'
        ]);

        Courier::create([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'is_active' => true
        ]);

        return back()->with('success', 'Ekspedisi berhasil ditambahkan.');
    }

    public function toggleCourier($id)
    {
        $courier = Courier::findOrFail($id);
        $courier->update(['is_active' => !$courier->is_active]);
        return back()->with('success', 'Status ekspedisi berhasil diubah.');
    }

    public function deleteCourier($id)
    {
        Courier::findOrFail($id)->delete();
        return back()->with('success', 'Ekspedisi berhasil dihapus.');
    }

    // DASHBOARD DATA LAINNYA
    public function users() {
        $users = User::latest()->paginate(10);
        return view('admin.users', compact('users'));
    }

    public function shops() {
        $shops = Shop::with('user')->latest()->paginate(10);
        return view('admin.shops', compact('shops'));
    }

    public function orderHistory() {
        $orders = Order::with(['user', 'shop'])->whereIn('status', ['processing', 'shipped', 'completed', 'cancelled'])->latest()->paginate(15);
        return view('admin.orders.history', compact('orders'));
    }

    public function orderDetail($id) {
        $order = Order::with(['user', 'shop', 'items.product'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function withdrawals() {
        $withdrawals = Withdrawal::where('status', 'pending')->with('user.shop')->latest()->get();
        return view('admin.withdrawals', compact('withdrawals'));
    }

    public function withdrawalHistory() {
        $withdrawals = Withdrawal::with('user.shop')->whereIn('status', ['approved', 'rejected'])->latest()->paginate(15);
        return view('admin.withdrawals.history', compact('withdrawals'));
    }

    public function salesReport() {
        $reports = Order::where('status', 'completed')
            ->select(
                DB::raw('SUM(total_price) as revenue'),
                DB::raw('COUNT(*) as total_sales'),
                DB::raw("DATE_FORMAT(created_at, '%M %Y') as month"),
                DB::raw("YEAR(created_at) as year"),
                DB::raw("MONTH(created_at) as month_num")
            )
            ->groupBy('year', 'month_num', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month_num', 'desc')
            ->get();

        return view('admin.reports.index', compact('reports'));
    }

    // LOGIKA VERIFIKASI (Keep existing)
    public function approveShop($id) {
        $shop = Shop::findOrFail($id);
        $shop->update(['is_verified' => true, 'is_active' => true]);
        return back()->with('success', 'Toko disetujui!');
    }

    // app/Http/Controllers/AdminController.php

    public function confirmPayment($id) {
        $order = Order::findOrFail($id);

        $order->update([
            'status' => 'processing', 
            'payment_status' => 'paid'
        ]);

        return back()->with('success', 'Pembayaran diverifikasi. Penjual akan segera memproses pesanan.');
    }
}