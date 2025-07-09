<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class BookingRateLimiter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'booking-attempts:' . ($request->user()?->id ?: $request->ip());

        if (RateLimiter::tooManyAttempts($key, 10)) { // 10 attempts per minute
            return response()->json(['message' => 'Too many booking attempts. Please try again later.'], 429);
        }

        RateLimiter::hit($key);

        return $next($request);
    }
}
