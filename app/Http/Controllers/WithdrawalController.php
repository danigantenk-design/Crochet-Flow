<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    // 1. Tampilkan Form Tarik Dana
    public function create()
    {
        $wallet = Wallet::firstOrCreate(
            ['user_id' => Auth::id()],
            ['balance' => 0]
        );

        return view('shop.withdraw', compact('wallet'));
    }

    // 2. Proses Pengajuan Penarikan
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'bank_name' => 'required|string',
            'account_number' => 'required|numeric',
            'account_holder' => 'required|string',
        ]);

        $user = Auth::user();
        
        // Cek Saldo
        $wallet = Wallet::where('user_id', $user->id)->first();
        if (!$wallet || $wallet->balance < $request->amount) {
            return back()->with('error', 'Saldo tidak mencukupi!');
        }

        // Gunakan Transaksi Database biar aman
        DB::transaction(function () use ($request, $user, $wallet) {
            // A. Kurangi Saldo di Wallet
            $wallet->decrement('balance', $request->amount);

            // B. Buat Record Withdrawal (Status Pending)
            $withdrawal = Withdrawal::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'status' => 'pending',
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'account_holder' => $request->account_holder,
            ]);

            // C. Catat di Mutasi (Uang Keluar)
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'debit',
                'amount' => $request->amount,
                'description' => 'Penarikan Dana ke ' . $request->bank_name,
                'reference_id' => $withdrawal->id,
                'reference_type' => 'withdrawal'
            ]);
        });

        return redirect()->route('shop.finance')->with('success', 'Permintaan penarikan berhasil dikirim! Menunggu persetujuan Admin.');
    }
}