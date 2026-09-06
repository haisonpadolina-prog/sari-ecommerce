<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CourierOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('is_courier')) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Please login using a courier account.',
                ]);
        }

        return $next($request);
    }
}