<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminRedirect
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user() !== null && $request->routeIs('home')) {
            return redirect()->route('admin.home');
        }

        return $next($request);
    }
}
