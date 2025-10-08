<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        // ambil nama role dari relasi (case insensitive)
        $userRole = strtoupper($user->role->Nama_role ?? $user->role->nama_role ?? '');

        if (!$user || !in_array($userRole, array_map('strtoupper', $roles))) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}
