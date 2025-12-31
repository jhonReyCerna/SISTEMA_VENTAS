<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    /**
     * Obtener notificaciones del usuario autenticado
     */
    public function obtenerNotificaciones()
    {
        $notificaciones = Notificacion::where('id_usuario', Auth::id())
                                    ->orWhereNull('id_usuario') // Notificaciones globales
                                    ->orderBy('fecha_creacion', 'desc')
                                    ->limit(10)
                                    ->get();

        return response()->json([
            'notificaciones' => $notificaciones,
            'no_leidas' => $notificaciones->where('leida', false)->count()
        ]);
    }

    /**
     * Marcar notificación como leída
     */
    public function marcarLeida($id)
    {
        $notificacion = Notificacion::where('id_notificacion', $id)
                                  ->where(function($query) {
                                      $query->where('id_usuario', Auth::id())
                                            ->orWhereNull('id_usuario');
                                  })
                                  ->first();

        if ($notificacion) {
            $notificacion->marcarComoLeida();
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Notificación no encontrada'], 404);
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public function marcarTodasLeidas()
    {
        Notificacion::where('id_usuario', Auth::id())
                   ->orWhereNull('id_usuario')
                   ->where('leida', false)
                   ->update(['leida' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Eliminar notificación
     */
    public function eliminar($id)
    {
        $notificacion = Notificacion::where('id_notificacion', $id)
                                  ->where(function($query) {
                                      $query->where('id_usuario', Auth::id())
                                            ->orWhereNull('id_usuario');
                                  })
                                  ->first();

        if ($notificacion) {
            $notificacion->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Notificación no encontrada'], 404);
    }

    /**
     * Obtener contador de notificaciones no leídas
     */
    public function contadorNoLeidas()
    {
        $contador = Notificacion::where('id_usuario', Auth::id())
                               ->orWhereNull('id_usuario')
                               ->where('leida', false)
                               ->count();

        return response()->json(['contador' => $contador]);
    }

    /**
     * Vista de todas las notificaciones
     */
    public function index()
    {
        $notificaciones = Notificacion::where('id_usuario', Auth::id())
                                    ->orWhereNull('id_usuario')
                                    ->orderBy('fecha_creacion', 'desc')
                                    ->paginate(20);

        return view('notificaciones.index', compact('notificaciones'));
    }
}
