<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class VerifySession
{
    public function handle(Request $request, Closure $next)
    {
        // Jika user terautentikasi tapi tidak ada session role
        if (Auth::check() && !session()->has('user_role')) {
            session(['user_role' => Auth::user()->role]);
        }

        // Jika tidak ada CSRF token, generate baru
        if (!Session::has('_token')) {
            $request->session()->regenerateToken();
        }

        $response = $next($request);

        // Jika response adalah redirect dan ada error
        if ($response->getStatusCode() === 302 && session()->has('errors')) {
            session()->keep(['errors', '_token']);
        }

        return $response;
    }
} 