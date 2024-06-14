<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Comprueba si el usuario no está autenticado
       /*  if (!Auth::check()) {
            // Redirige a la página de inicio de sesión si no está autenticado
            return redirect('/login');
        } */

        // Si el usuario está autenticado, continua con la siguiente solicitud
        return $next($request);
    }
}
