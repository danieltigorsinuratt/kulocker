<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk memproteksi halaman admin.
 * Hanya user dengan role 'admin' yang bisa akses.
 */
class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = session('user');

        if (!$user || ($user['role'] ?? '') !== 'admin') {
            return redirect()->route('sign-in');
        }

        return $next($request);
    }
}
