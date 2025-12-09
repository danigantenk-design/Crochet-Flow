<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // ==========================================================
    // REGISTER
    // ==========================================================
    public function showRegisterForm()
    {
        // Jika sudah login, jangan kasih akses halaman daftar
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'full_name' => $request->full_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => 'buyer', // Default user baru adalah Buyer
        ]);

        Auth::login($user);

        // Redirect ke halaman depan (Marketplace)
        return redirect()->route('front.index');
    }

    // ==========================================================
    // LOGIN
    // ==========================================================
    
    // --- METHOD INI YANG HILANG TADI ---
    public function showLoginForm()
    {
        // Jika user iseng buka /login padahal sudah login
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Cek Role untuk Redirect yang benar
            return $this->redirectBasedOnRole();
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    // ==========================================================
    // LOGOUT
    // ==========================================================
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }

    //Helper kecil untuk logika redirect biar rapi
    private function redirectBasedOnRole()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Untuk Buyer & Seller, lempar ke Halaman Depan (Belanja)
        // intended() mengembalikan user ke halaman yang mau dibuka sebelum dipaksa login
        return redirect()->intended(route('front.index'));
    }
}