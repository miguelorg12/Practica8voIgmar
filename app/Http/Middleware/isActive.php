<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class isActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
            $user = Auth::user();
            Log::info("Middleware isActive: ".$user->status) ;
            // Verifica si el usuario está activo
            if ($user->status !== 'A') {
                // Si el usuario no está activo, cierra la sesión y redirige al login con un mensaje de error
                Auth::logout();
                return redirect()->route('login')->with('error', 'Tu cuenta no está activa.');
            }
        

        return $next($request);
        
    }
}
