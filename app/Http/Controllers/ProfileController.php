<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserAddress; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    /**
     * Menampilkan profil user dan daftar alamat.
     */
    public function show(Request $request): View
    {
        // Pastikan model User punya relasi 'addresses' (jamak)
        $user = User::with('addresses')->findOrFail(Auth::id()); 

        return view('profile.show', [
            'user' => $user,
        ]);
    }

    /**
     * Form edit data diri utama.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update Data Diri Utama.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.Auth::id()],
            'phone_number' => ['nullable', 'string', 'max:20'], 
        ]);

        $request->user()->update($request->only('full_name', 'email', 'phone_number'));

        return back()->with('status', 'profile-updated');
    }

    // ==========================================================
    // LOGIKA ALAMAT (TAMBAH, EDIT, HAPUS)
    // ==========================================================

    public function createAddress(): View
    {
        return view('profile.address_create');
    }

    public function storeAddress(Request $request): RedirectResponse
    {
        $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone_number'   => 'required|string|max:20',
            'full_address'   => 'required|string|max:500',
            'label'          => 'nullable|string|max:100', 
            'postal_code'    => 'required|string|max:10', // Wajib Validasi
            'city_id'        => 'nullable|integer',
        ]);

        $user = Auth::id();
        
        // Cek apakah ini alamat pertama?
        $isFirstAddress = !UserAddress::where('user_id', $user)->exists(); 

        // 1. Ambil data dari request (JANGAN LUPA postal_code)
        $data = $request->only('recipient_name', 'phone_number', 'full_address', 'label', 'postal_code', 'city_id');
        
        // 2. Set default label jika kosong
        if (empty($data['label'])) {
             $data['label'] = 'Rumah';
        }
        
        // 3. Tambahkan data system
        $data['user_id'] = $user;
        $data['is_primary'] = $isFirstAddress;
        
        // 4. Simpan
        UserAddress::create($data);

        return redirect()->route('profile.show')->with('status', 'address-created');
    }

    /**
     * Menampilkan Form Edit Alamat (Menggunakan Model Binding)
     * Pastikan route di web.php menggunakan parameter {id} -> /profile/address/{id}/edit
     */
    public function editAddress($id): View
    {
        $address = UserAddress::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        return view('profile.address_edit', compact('address'));
    }

    /**
     * Update Alamat
     */
    public function updateAddress(Request $request, $id): RedirectResponse
    {
        $address = UserAddress::where('user_id', Auth::id())->where('id', $id)->firstOrFail();

        $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone_number'   => 'required|string|max:20',
            'full_address'   => 'required|string|max:500',
            'postal_code'    => 'required|string|max:10',
            'label'          => 'nullable|string|max:100', 
        ]);
        
        $data = $request->only('recipient_name', 'phone_number', 'full_address', 'label', 'postal_code');

        if (empty($data['label'])) {
            $data['label'] = 'Rumah';
        }

        $address->update($data);
        
        return redirect()->route('profile.show')->with('status', 'address-updated');
    }

    /**
     * Hapus Alamat
     */
    public function destroyAddress($id): RedirectResponse
    {
        $address = UserAddress::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $address->delete();

        return back()->with('status', 'address-deleted');
    }
}