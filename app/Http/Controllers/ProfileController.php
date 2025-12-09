<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    /**
     * Menampilkan profil user (Read Only).
     */
    public function show(Request $request): View
    {
        // Load data user beserta alamat (jika ada)
        $user = Auth::user()->load('address'); 

        return view('profile.show', [
            'user' => $user,
        ]);
    }

    /**
     * Menampilkan form edit profil.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
            // Kita kirim data alamat agar form terisi otomatis jika sudah ada data
            'address' => $request->user()->address, 
        ]);
    }

    /**
     * Update Data Diri Utama (Nama & Email).
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.Auth::id()],
            'phone_number' => ['nullable', 'string', 'max:20'], // Tambahan untuk no HP user
        ]);

        $request->user()->update($request->only('full_name', 'email', 'phone_number'));

        return back()->with('status', 'profile-updated');
    }

    /**
     * Update Alamat Pengiriman.
     */
    public function updateAddress(Request $request): RedirectResponse
    {
        $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone_number'          => 'required|string|max:20',
            'full_address'   => 'required|string|max:500',
            // city_id nanti diintegrasikan dengan RajaOngkir, sementara nullable/string dulu
            'city_id'        => 'nullable', 
        ]);

        // Gunakan updateOrCreate: Jika belum ada alamat buat baru, jika ada update.
        $request->user()->address()->updateOrCreate(
            ['user_id' => $request->user()->id], // Kunci pencarian
            [
                'recipient_name' => $request->recipient_name,
                'phone_number'          => $request->phone_number,
                'full_address'   => $request->full_address,
                'is_primary'     => true, // Default jadi alamat utama
                // 'city_id'     => $request->city_id, (Nanti diaktifkan saat fitur ongkir)
            ]
        );

        return back()->with('status', 'address-updated');
    }
   public function editAddress()
    {
        $user = auth()->user();
        $user_addresses = $user->address;   // ambil alamat user

        return view('profile.address_edit', compact('user', 'user_addresses'));
    }

public function address()
{
    $user = auth()->user();

    // Ambil alamat user (opcional)
    $user_addresses = $user->address; // atau $user->addresses()->first()

    return view('profile.address', compact('user', 'user_addresses'));
}



}