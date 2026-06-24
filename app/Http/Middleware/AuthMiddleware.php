<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menggantikan fungsi config/auth.php di proyek PHP asli.
 * Cek apakah session 'user' ada. Jika tidak, redirect ke halaman login.
 */
class AuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('user')) {
            return redirect()->route('sign-in');
        }

        return $next($request);
    }
}
