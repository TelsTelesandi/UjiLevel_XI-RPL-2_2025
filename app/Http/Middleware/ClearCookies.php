<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class ClearCookies
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response->getStatusCode() >= 400) {
            Cookie::queue(Cookie::forget('XSRF-TOKEN'));
            Cookie::queue(Cookie::forget('x_cool_event_session'));
            
            if (method_exists($response, 'withCookie')) {
                $response->withCookie(Cookie::forget('XSRF-TOKEN'))
                        ->withCookie(Cookie::forget('x_cool_event_session'));
            }
        }

        return $response;
    }
} 