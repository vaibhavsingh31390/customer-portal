<?php

namespace App\Http\Middleware;

use App\Support\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->session()->get('user');

        if (! $user || ! UserRole::isAdmin($user->user_code)) {
            abort(403);
        }

        return $next($request);
    }
}
