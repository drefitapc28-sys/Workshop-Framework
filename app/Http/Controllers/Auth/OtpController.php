<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{
    /**
     * Tampilkan halaman verifikasi OTP
     */
    public function showVerifyForm()
    {
        // Cek apakah ada user yang sedang verifikasi OTP
        if (!session('otp_user_id')) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        return view('auth.verify-otp');
    }

    /**
     * Verifikasi kode OTP
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $userId = session('otp_user_id');
        
        if (!$userId) {
            return redirect()->route('login')
                ->with('error', 'Sesi telah berakhir. Silakan login kembali.');
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'User tidak ditemukan.');
        }

        // Verifikasi OTP
        if ($user->otp === $request->otp) {
            // OTP benar, hapus OTP dan login user
            $user->update(['otp' => null]);
            
            // Hapus session otp_user_id
            session()->forget('otp_user_id');
            
            // Login user
            Auth::login($user);
            
            // Regenerate session
            $request->session()->regenerate();

            return redirect()->intended(route('home'))
                ->with('success', 'Login berhasil!');
        }

        return back()->withErrors([
            'otp' => 'Kode OTP tidak valid.',
        ]);
    }

    /**
     * Kirim ulang OTP
     */
    public function resend()
    {
        $userId = session('otp_user_id');
        
        if (!$userId) {
            return redirect()->route('login')
                ->with('error', 'Sesi telah berakhir. Silakan login kembali.');
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'User tidak ditemukan.');
        }

        // Generate OTP baru
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->update(['otp' => $otp]);

        // Kirim OTP ke email
        \Mail::raw("Kode OTP Anda adalah: $otp", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Kode OTP Login');
        });

        return back()->with('success', 'Kode OTP baru telah dikirim ke email Anda.');
    }
}