<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- 1. Import the Auth facade
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User|null $user */
        // 2. Use the Auth facade instead of the helper function
        $user = Auth::user();

        // The rest of the logic is the same
        if (!$user || !$user->hasRole('admin')) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}