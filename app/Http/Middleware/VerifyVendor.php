<?php

namespace App\Http\Middleware;

use App\Models\Vendor;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyVendor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $vendor = Vendor::where('idvendor', auth()->id())->first();

        if (!$vendor) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Akun Anda bukan vendor');
        }

        view()->share('vendorAuth', $vendor);
        return $next($request);
    }
}
