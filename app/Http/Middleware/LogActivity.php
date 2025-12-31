<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LogActividad;

class LogActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Solo loguear si el usuario está autenticado y la respuesta es exitosa
        if (Auth::check() && $response->getStatusCode() < 400) {
            $this->logActivity($request, $response);
        }

        return $response;
    }

    /**
     * Log user activity.
     */
    protected function logActivity(Request $request, $response)
    {
        $method = $request->method();
        $route = $request->route();
        
        if (!$route) return;
        
        $routeName = $route->getName();
        $uri = $request->getPathInfo();
        
        // Skip logging for certain routes
        $skipRoutes = [
            'notificaciones.api',
            'notificaciones.contador',
            'security.sessions.terminate',
        ];
        
        if (in_array($routeName, $skipRoutes) || str_contains($uri, '/api/')) {
            return;
        }
        
        $accion = $this->determinarAccion($method, $routeName, $uri);
        $modulo = $this->determinarModulo($routeName, $uri);
        
        if ($accion && $modulo) {
            LogActividad::logCrear(
                $modulo,
                $accion,
                $this->generarDescripcion($accion, $modulo, $request),
                $request->all()
            );
        }
    }

    /**
     * Determine action based on HTTP method and route.
     */
    protected function determinarAccion($method, $routeName, $uri)
    {
        // Based on HTTP method
        switch ($method) {
            case 'GET':
                if (str_contains($routeName, '.index') || str_contains($uri, '/index')) {
                    return 'LISTAR';
                } elseif (str_contains($routeName, '.show') || str_contains($uri, '/show')) {
                    return 'VER';
                } elseif (str_contains($routeName, '.create') || str_contains($uri, '/create')) {
                    return 'ACCESO_CREAR';
                } elseif (str_contains($routeName, '.edit') || str_contains($uri, '/edit')) {
                    return 'ACCESO_EDITAR';
                } else {
                    return 'ACCESO';
                }
            case 'POST':
                return 'CREAR';
            case 'PUT':
            case 'PATCH':
                return 'EDITAR';
            case 'DELETE':
                return 'ELIMINAR';
            default:
                return null;
        }
    }

    /**
     * Determine module based on route name or URI.
     */
    protected function determinarModulo($routeName, $uri)
    {
        // Extract module from route name
        if ($routeName) {
            $parts = explode('.', $routeName);
            $module = $parts[0] ?? null;
            
            $moduleMap = [
                'usuarios' => 'USUARIOS',
                'roles' => 'ROLES',
                'clientes' => 'CLIENTES',
                'categorias' => 'CATEGORIAS',
                'productos' => 'PRODUCTOS',
                'ventas' => 'VENTAS',
                'movimientos' => 'MOVIMIENTOS_STOCK',
                'reportes' => 'REPORTES',
                'notificaciones' => 'NOTIFICACIONES',
                'security' => 'SEGURIDAD',
                'dashboard' => 'DASHBOARD',
                'profile' => 'PERFIL',
            ];
            
            return $moduleMap[$module] ?? strtoupper($module);
        }
        
        // Extract module from URI
        $pathParts = explode('/', trim($uri, '/'));
        $firstPart = $pathParts[0] ?? null;
        
        return $firstPart ? strtoupper($firstPart) : 'SISTEMA';
    }

    /**
     * Generate human-readable description.
     */
    protected function generarDescripcion($accion, $modulo, $request)
    {
        $userName = Auth::user()->nombre ?? 'Usuario';
        $route = $request->route();
        $resourceId = null;
        
        // Try to get resource ID from route parameters
        if ($route) {
            $parameters = $route->parameters();
            $resourceId = $parameters['id'] ?? $parameters['usuario'] ?? $parameters['producto'] ?? 
                         $parameters['cliente'] ?? $parameters['categoria'] ?? $parameters['venta'] ?? null;
        }
        
        $descriptions = [
            'LISTAR' => "accedió al listado de {$modulo}",
            'VER' => $resourceId ? "visualizó {$modulo} ID: {$resourceId}" : "visualizó {$modulo}",
            'CREAR' => "creó nuevo registro en {$modulo}",
            'EDITAR' => $resourceId ? "editó {$modulo} ID: {$resourceId}" : "editó {$modulo}",
            'ELIMINAR' => $resourceId ? "eliminó {$modulo} ID: {$resourceId}" : "eliminó {$modulo}",
            'ACCESO_CREAR' => "accedió al formulario de creación de {$modulo}",
            'ACCESO_EDITAR' => $resourceId ? "accedió al formulario de edición de {$modulo} ID: {$resourceId}" : "accedió al formulario de edición de {$modulo}",
            'ACCESO' => "accedió a {$modulo}",
        ];
        
        return $userName . ' ' . ($descriptions[$accion] ?? "realizó acción {$accion} en {$modulo}");
    }
}
