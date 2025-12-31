<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Obtener datos actualizados directamente de la base de datos
            $user = User::find(Auth::id());
            
            // Si el usuario fue eliminado o está inactivo, cerrar sesión
            if (!$user || $user->estado !== 'ACTIVO') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')
                    ->with('error', 'Tu cuenta ha sido desactivada. Por favor contacta al administrador.');
            }
        }

        return $next($request);
    }
}
