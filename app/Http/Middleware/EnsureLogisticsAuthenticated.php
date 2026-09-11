<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLogisticsAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->get('is_logistics')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
