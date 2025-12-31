<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogActividad;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LogActividadController extends Controller
{
    /**
     * Display a listing of activity logs.
     */
    public function index(Request $request)
    {
        // Solo Admin puede ver todos los logs
        if (Auth::user()->role->nombre !== 'Admin') {
            abort(403, 'No tienes permisos para acceder a esta sección');
        }

        $query = LogActividad::with('usuario');

        // Filtros
        if ($request->filled('usuario')) {
            $query->where('id_usuario', $request->usuario);
        }

        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }

        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_hora', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_hora', '<=', $request->fecha_hasta);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('descripcion', 'LIKE', "%{$search}%")
                  ->orWhere('ip', 'LIKE', "%{$search}%");
            });
        }

        $logs = $query->orderBy('fecha_hora', 'desc')->paginate(50);

        // Data for filters
        $usuarios = User::select('id_usuario', 'nombre')->get();
        $modulos = LogActividad::distinct('modulo')->pluck('modulo');
        $acciones = LogActividad::distinct('accion')->pluck('accion');

        // Statistics
        $stats = [
            'total_actividades' => LogActividad::count(),
            'actividades_hoy' => LogActividad::whereDate('fecha_hora', today())->count(),
            'usuarios_activos_hoy' => LogActividad::whereDate('fecha_hora', today())->distinct('id_usuario')->count(),
            'modulos_mas_usados' => LogActividad::whereDate('fecha_hora', '>=', now()->subDays(7))
                                                ->groupBy('modulo')
                                                ->selectRaw('modulo, count(*) as count')
                                                ->orderBy('count', 'desc')
                                                ->limit(5)
                                                ->get(),
        ];

        return view('logs.index', compact('logs', 'usuarios', 'modulos', 'acciones', 'stats'));
    }

    /**
     * Show detailed log entry.
     */
    public function show($id)
    {
        if (Auth::user()->role->nombre !== 'Admin') {
            abort(403, 'No tienes permisos para acceder a esta sección');
        }

        $log = LogActividad::with('usuario')->findOrFail($id);
        return view('logs.show', compact('log'));
    }

    /**
     * Export logs to CSV.
     */
    public function export(Request $request)
    {
        if (Auth::user()->role->nombre !== 'Admin') {
            abort(403, 'No tienes permisos para realizar esta acción');
        }

        $query = LogActividad::with('usuario');

        // Apply same filters as index
        if ($request->filled('usuario')) {
            $query->where('id_usuario', $request->usuario);
        }
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }
        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_hora', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_hora', '<=', $request->fecha_hasta);
        }

        $logs = $query->orderBy('fecha_hora', 'desc')->get();

        $filename = 'logs_actividad_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            "Content-type" => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add headers
            fputcsv($file, [
                'ID',
                'Usuario',
                'Fecha/Hora',
                'Módulo',
                'Acción',
                'Descripción',
                'IP',
                'User Agent',
            ], ';');

            // Add data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id_log,
                    $log->usuario->nombre ?? 'Usuario Eliminado',
                    $log->fecha_hora,
                    $log->modulo,
                    $log->accion,
                    $log->descripcion,
                    $log->ip,
                    $log->user_agent,
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Clean old logs (Admin only).
     */
    public function clean(Request $request)
    {
        if (Auth::user()->role->nombre !== 'Admin') {
            abort(403, 'No tienes permisos para realizar esta acción');
        }

        $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        $deleted = LogActividad::where('fecha_hora', '<', now()->subDays($request->days))->delete();

        return redirect()->route('logs.index')->with('success', "Se eliminaron {$deleted} registros de actividad anteriores a {$request->days} días.");
    }
}
