<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_id'   => 'required|exists:orders,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'required|string|max:1000',
        ]);

        // 2. Cek Keamanan: Pastikan User benar-benar pemilik Order tersebut
        $order = Order::where('id', $request->order_id)
                      ->where('user_id', Auth::id())
                      ->where('status', 'completed') // Hanya boleh review jika status completed
                      ->first();

        if (!$order) {
            return back()->with('error', 'Anda tidak berhak mengulas pesanan ini.');
        }

        // 3. Cek Duplikasi: Apakah user sudah pernah review produk ini di order ini?
        $existingReview = Review::where('user_id', Auth::id())
                                ->where('product_id', $request->product_id)
                                ->where('order_id', $request->order_id)
                                ->first();

        if ($existingReview) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk produk ini.');
        }

        // 4. Simpan Review
        Review::create([
            'user_id'    => Auth::id(),
            'product_id' => $request->product_id,
            'order_id'   => $request->order_id,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
        ]);

        return back()->with('success', 'Terima kasih! Ulasan Anda berhasil dikirim.');
    }
}