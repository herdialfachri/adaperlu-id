<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        $user = $request->user();

        // Pastikan user login & cek field role_id
        if (!$user || $user->role_id != $role) {
            return response()->json(['message' => 'Forbidden. Only admin can access.'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}