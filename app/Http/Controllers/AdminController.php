<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shop;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public $msg;
    public function index()
    {
        // Statistik
        $totalUsers = User::count();
        $totalShops = Shop::count();
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', 'completed')->sum('total_price');

        // 1. Toko yang Belum Diverifikasi
        $pendingShops = Shop::with('user')->where('is_verified', false)->get();

        // 2. Pembayaran yang Menunggu Verifikasi
        $pendingPayments = Order::with(['user', 'shop'])
                            ->where('status', 'waiting_verification') 
                            ->whereNotNull('payment_proof')
                            ->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalShops', 'totalOrders', 'totalRevenue', 'pendingShops', 'pendingPayments'
        ));
    }

    // Hapus User
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        // Mencegah admin menghapus dirinya sendiri
        if ($user->id == Auth::id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }

        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }

    // Reject Toko
    public function rejectShop($id)
    {
        $shop = Shop::findOrFail($id);
        $shop->user->role = 'buyer'; // Balikin role user
        $shop->user->save();
        $shop->delete();
        return back()->with('success', "Pengajuan toko ditolak.");
    }

    // VERIFIKASI PEMBAYARAN
    public function confirmPayment($id)
{
    $order = Order::with(['items.product', 'shop'])->findOrFail($id);

    DB::transaction(function () use ($order) {
        if ($order->isFullDigital()) {
            $order->update([
                'status' => 'completed',
                'payment_status' => 'paid'
            ]);

            $sellerWallet = Wallet::firstOrCreate(['user_id' => $order->shop->user_id], ['balance' => 0]);
            $netAmount = $order->calculateNetIncome();
            $sellerWallet->increment('balance', $netAmount);

            WalletTransaction::create([
                'wallet_id' => $sellerWallet->id,
                'type' => 'credit',
                'amount' => $netAmount,
                'description' => 'Penjualan Digital Order #' . $order->invoice_number,
                'reference_id' => $order->id,
                'reference_type' => 'order'
            ]);
            
            $this->msg = "Pembayaran Digital Terverifikasi. Saldo masuk ke penjual.";
        } else {
            $order->update([
                'status' => 'processing',
                'payment_status' => 'paid'
            ]);
            $this->msg = "Pembayaran Fisik Terverifikasi. Pesanan diteruskan ke penjual.";
        }
    });

    return back()->with('success', $this->msg);
}

    // Halaman Daftar Penarikan Dana
    public function withdrawals()
    {
        // Ambil data withdrawal yang statusnya pending
        $withdrawals = Withdrawal::where('status', 'pending')
                                 ->with('user.shop') // Load relasi user & toko
                                 ->latest()
                                 ->get();

        return view('admin.withdrawals', compact('withdrawals'));
    }

    // Setujui Penarikan
    public function approveWithdrawal($id)
    {
        $withdrawal = Withdrawal::findOrFail($id);
        
        // Ubah status jadi approved
        $withdrawal->update(['status' => 'approved']);

        return back()->with('success', 'Penarikan disetujui! Dana dianggap telah ditransfer.');
    }

    // Tolak Penarikan (Refund Saldo)
    public function rejectWithdrawal($id)
    {
        $withdrawal = Withdrawal::findOrFail($id);

        DB::transaction(function () use ($withdrawal) {
            // A. Ubah status jadi rejected
            $withdrawal->update(['status' => 'rejected']);

            // B. KEMBALIKAN SALDO ke Dompet User
            $wallet = Wallet::where('user_id', $withdrawal->user_id)->first();
            if ($wallet) {
                $wallet->increment('balance', $withdrawal->amount);

                // C. Catat Mutasi Pengembalian (Refund)
                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => 'credit', // Uang Masuk Kembali
                    'amount' => $withdrawal->amount,
                    'description' => 'Pengembalian Dana (Penarikan Ditolak)',
                    'reference_id' => $withdrawal->id,
                    'reference_type' => 'withdrawal_refund'
                ]);
            }
        });

        return back()->with('success', 'Penarikan ditolak dan saldo telah dikembalikan ke pengguna.');
    }

    // ==========================================
    // MANAJEMEN USERS
    // ==========================================
    public function users()
    {
        $users = User::latest()->paginate(10);
        return view('admin.users', compact('users'));
    }

    // ==========================================
    // MANAJEMEN TOKO (SHOPS)
    // ==========================================
    
    public function shops()
    {
        // Ambil semua toko beserta data pemiliknya
        $shops = Shop::with('user')->latest()->paginate(10);
        return view('admin.shops', compact('shops'));
    }

    public function deleteShop($id)
    {
        $shop = Shop::findOrFail($id);
        $shop->delete(); 
        return back()->with('success', 'Toko berhasil dihapus permanen.');
    }

    // Method untuk menyetujui/mengaktifkan toko
    public function approveShop($id)
    {
        $shop = Shop::findOrFail($id);
        
        $shop->update([
            'is_verified' => true,
            'is_active' => true 
        ]);
        $shop->save();

        return back()->with('success', 'Toko berhasil disetujui!');
    }
}