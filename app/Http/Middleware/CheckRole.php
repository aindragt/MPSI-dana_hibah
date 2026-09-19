<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * @param  string  ...$slugs  Satu atau lebih role slug yang diizinkan (e.g., 'pengaju', 'admin-kesra')
     */
    public function handle(Request $request, Closure $next, string ...$slugs)
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role->slug, $slugs)) {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
