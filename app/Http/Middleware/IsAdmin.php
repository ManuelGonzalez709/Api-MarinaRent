<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->Tipo === 'admin') {
            return $next($request);
        }

        return response()->json([
            'message' => 'Acceso denegado. Solo los administradores pueden realizar esta acción.'
        ], 403);
    }
}