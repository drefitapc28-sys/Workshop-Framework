<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class VendorAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.vendor-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $vendor = Vendor::where('idvendor', $user->id)->first();
            
            if ($vendor) {
                return redirect('/vendor/dashboard');
            } else {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun Anda bukan vendor']);
            }
        }

        return back()->withErrors(['email' => 'Email atau password salah']);
    }

    public function showRegisterForm()
    {
        return view('auth.vendor-register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama_vendor' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'kontak' => 'nullable|string',
            'deskripsi' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['nama_vendor'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Vendor::create([
            'idvendor' => $user->id,
            'nama_vendor' => $validated['nama_vendor'],
            'kontak' => $validated['kontak'] ?? null,
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);

        Auth::login($user);
        return redirect('/vendor/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
