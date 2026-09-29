<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware pembatas akses berbasis peran (role-based access control).
 *
 * Pemakaian pada route:
 *     ->middleware('cek.peran:dosen,asdos')
 */
class CekPeran
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): Response  $next
     * @param  string  ...$roles  Daftar peran yang diizinkan (variadic).
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Tamu tidak punya peran sama sekali. Pada route yang juga memakai
        // middleware `auth`, kondisi ini sebenarnya sudah ditangani lebih dulu.
        if ($user === null) {
            abort(403, 'Anda tidak memiliki hak akses!');
        }

        // Rapikan spasi agar penulisan 'cek.peran:dosen, asdos' tetap valid.
        $allowedRoles = array_map('trim', $roles);

        if (! $user->hasRole(...$allowedRoles)) {
            abort(403, 'Anda tidak memiliki hak akses!');
        }

        return $next($request);
    }
}
