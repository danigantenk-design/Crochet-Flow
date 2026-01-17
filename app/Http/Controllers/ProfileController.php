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

    public function storeAddress(Request $request)
    {
        // 1. CEK DATA YANG DIKIRIM (DEBUGGING)
        // Hapus tanda // di bawah ini jika masih gagal, nanti akan muncul layar hitam isi data
        // dd($request->all()); 

        // 2. Validasi (Sesuaikan dengan name di Form Blade tadi)
        $request->validate([
            'recipient_name' => 'required|string',
            // Kita izinkan 'phone' ATAU 'phone_number' biar aman
            'phone_number'   => 'required_without:phone', 
            'phone'          => 'required_without:phone_number',
            // Kita izinkan 'full_address' ATAU 'address_line'
            'full_address'   => 'required_without:address_line',
            'address_line'   => 'required_without:full_address',
            'postal_code'    => 'required',
        ]);

        $user = Auth::user();
        $isFirst = \App\Models\UserAddress::where('user_id', $user->id)->count() == 0;

        // 3. Ambil data input (Fleksibel)
        // Kalau form kirim 'phone', pakai itu. Kalau 'phone_number', pakai itu.
        $phoneInput = $request->phone ?? $request->phone_number;
        $addressInput = $request->address_line ?? $request->full_address;

        // 4. Simpan ke Database
        \App\Models\UserAddress::create([
            'user_id'        => $user->id,
            'label'          => $request->label ?? 'Rumah',
            'recipient_name' => $request->recipient_name,
            
            // Masukkan ke kolom database yang benar
            'phone_number'   => $phoneInput, 
            'full_address'   => $addressInput,
            'postal_code'    => $request->postal_code,
            
            // HARDCODE CITY ID (Solusi Error 1364)
            'city_id'        => 1, 
            
            'is_primary'     => $isFirst ? 1 : 0
        ]);

        return redirect()->route('profile.show')->with('success', 'Alamat berhasil ditambahkan!');
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