<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers; //trait dari laravel, trait adalah kumpulan method yang bisa dipinjam oleh kelas lain.

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home'; //protected artinya hanya bisa diakses oleh kelas ini dan turunannya, $redirectTo adalah properti yang digunakan untuk menentukan kemana user akan diarahkan setelah berhasil login.
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() 
    { 
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    // // Custom proses setelah login berhasil
    // protected function authenticated(Request $request, $user) //Fungsi ini akan dipanggil setelah user berhasil login. Di sini kita generate OTP, kirim email, dan logout user.
    // { 
    //     $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    //     $user->update(['otp' => $otp]);

    //     Auth::logout(); 

    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();

    //     session(['otp_user_id' => $user->id]);

    //     \Mail::raw("Kode OTP Anda adalah: $otp", function ($message) use ($user) {
    //         $message->to($user->email)
    //                 ->subject('Kode OTP Login');
    //     });

    //     return redirect()->route('otp.verify')
    //         ->with('success', 'Kode OTP telah dikirim ke email ' . $user->email);
    // }


}
