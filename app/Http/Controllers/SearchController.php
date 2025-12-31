<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    /**
     * Búsqueda global instantánea con caché Redis
     */
    public function globalSearch(Request $request)
    {
        $query = $request->input('q', '');
        $type = $request->input('type', 'all'); // all, productos, clientes, ventas, usuarios
        $limit = $request->input('limit', 10);

        if (strlen($query) < 2) {
            return response()->json(['results' => [], 'total' => 0]);
        }

        // Clave de caché basada en query, tipo y rol del usuario
        $cacheKey = "search:{$type}:{$query}:" . Auth::user()->role->nombre . ":limit:{$limit}";
        
        // Intentar obtener del caché (Redis)
        $results = Cache::remember($cacheKey, 300, function () use ($query, $type, $limit) {
            return $this->performSearch($query, $type, $limit);
        });

        return response()->json($results);
    }

    /**
     * Realizar búsqueda en base de datos
     */
    private function performSearch($query, $type, $limit)
    {
        $results = [];
        $total = 0;
        $userRole = Auth::user()->role->nombre;

        if ($type === 'all' || $type === 'productos') {
            if (in_array($userRole, ['Admin', 'Supervisor', 'Vendedor'])) {
                $productos = Producto::where('estado', 'ACTIVO')
                    ->where(function($q) use ($query) {
                        $q->where('nombre', 'LIKE', "%{$query}%")
                          ->orWhere('descripcion', 'LIKE', "%{$query}%");
                    })
                    ->with('categoria')
                    ->take($limit)
                    ->get()
                    ->map(function($producto) {
                        return [
                            'id' => $producto->id_producto,
                            'type' => 'producto',
                            'title' => $producto->nombre,
                            'subtitle' => $producto->categoria->nombre ?? 'Sin categoría',
                            'description' => $producto->descripcion,
                            'url' => route('productos.show', $producto->id_producto),
                            'icon' => 'fas fa-cube',
                            'badge' => 'S/ ' . number_format($producto->precio_venta, 2),
                            'stock' => $producto->stock,
                            'stock_low' => $producto->stock <= $producto->stock_minimo
                        ];
                    });
                
                $results = array_merge($results, $productos->toArray());
                $total += $productos->count();
            }
        }

        if ($type === 'all' || $type === 'clientes') {
            if (in_array($userRole, ['Admin', 'Supervisor', 'Vendedor'])) {
                $clientes = Cliente::where('estado', 'ACTIVO')
                    ->where(function($q) use ($query) {
                        $q->where('nombre', 'LIKE', "%{$query}%")
                          ->orWhere('email', 'LIKE', "%{$query}%")
                          ->orWhere('telefono', 'LIKE', "%{$query}%")
                          ->orWhere('documento', 'LIKE', "%{$query}%");
                    })
                    ->take($limit)
                    ->get()
                    ->map(function($cliente) {
                        return [
                            'id' => $cliente->id_cliente,
                            'type' => 'cliente',
                            'title' => $cliente->nombre,
                            'subtitle' => $cliente->email,
                            'description' => $cliente->telefono . ' - ' . $cliente->documento,
                            'url' => route('clientes.show', $cliente->id_cliente),
                            'icon' => 'fas fa-user-tie',
                            'badge' => $cliente->tipo_documento
                        ];
                    });
                
                $results = array_merge($results, $clientes->toArray());
                $total += $clientes->count();
            }
        }

        if ($type === 'all' || $type === 'ventas') {
            if (in_array($userRole, ['Admin', 'Supervisor', 'Vendedor'])) {
                $ventas = Venta::with(['cliente', 'usuario'])
                    ->where(function($q) use ($query) {
                        $q->where('numero_venta', 'LIKE', "%{$query}%")
                          ->orWhereHas('cliente', function($clienteQuery) use ($query) {
                              $clienteQuery->where('nombre', 'LIKE', "%{$query}%");
                          });
                    })
                    ->take($limit)
                    ->get()
                    ->map(function($venta) {
                        return [
                            'id' => $venta->id_venta,
                            'type' => 'venta',
                            'title' => 'Venta #' . $venta->numero_venta,
                            'subtitle' => $venta->cliente->nombre ?? 'Cliente no disponible',
                            'description' => 'Vendedor: ' . ($venta->usuario->nombre ?? 'N/A'),
                            'url' => route('ventas.show', $venta->id_venta),
                            'icon' => 'fas fa-shopping-cart',
                            'badge' => 'S/ ' . number_format($venta->total, 2),
                            'date' => $venta->fecha_venta->format('d/m/Y')
                        ];
                    });
                
                $results = array_merge($results, $ventas->toArray());
                $total += $ventas->count();
            }
        }

        if ($type === 'all' || $type === 'usuarios') {
            if ($userRole === 'Admin') {
                $usuarios = User::with('role')
                    ->where(function($q) use ($query) {
                        $q->where('nombre', 'LIKE', "%{$query}%")
                          ->orWhere('email', 'LIKE', "%{$query}%")
                          ->orWhere('usuario', 'LIKE', "%{$query}%");
                    })
                    ->take($limit)
                    ->get()
                    ->map(function($usuario) {
                        return [
                            'id' => $usuario->id_usuario,
                            'type' => 'usuario',
                            'title' => $usuario->nombre,
                            'subtitle' => $usuario->email,
                            'description' => 'Usuario: ' . $usuario->usuario,
                            'url' => route('usuarios.show', $usuario->id_usuario),
                            'icon' => 'fas fa-user',
                            'badge' => $usuario->role->nombre ?? 'Sin rol',
                            'status' => $usuario->estado
                        ];
                    });
                
                $results = array_merge($results, $usuarios->toArray());
                $total += $usuarios->count();
            }
        }

        return [
            'results' => $results,
            'total' => $total,
            'query' => $query,
            'cached' => false // Primera vez, no viene de caché
        ];
    }

    /**
     * Búsqueda de productos para autocomplete
     */
    public function searchProducts(Request $request)
    {
        $query = $request->input('q', '');
        $limit = $request->input('limit', 20);
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $cacheKey = "products_search:{$query}:limit:{$limit}";
        
        $productos = Cache::remember($cacheKey, 300, function () use ($query, $limit) {
            return Producto::where('estado', 'ACTIVO')
                ->where(function($q) use ($query) {
                    $q->where('nombre', 'LIKE', "%{$query}%")
                      ->orWhere('descripcion', 'LIKE', "%{$query}%");
                })
                ->with('categoria')
                ->take($limit)
                ->get()
                ->map(function($producto) {
                    return [
                        'id' => $producto->id_producto,
                        'text' => $producto->nombre,
                        'price' => $producto->precio_venta,
                        'stock' => $producto->stock,
                        'category' => $producto->categoria->nombre ?? 'Sin categoría',
                        'description' => $producto->descripcion
                    ];
                });
        });
        
        return response()->json($productos);
    }

    /**
     * Limpiar caché de búsquedas
     */
    public function clearSearchCache()
    {
        if (Auth::user()->role->nombre !== 'Admin') {
            abort(403);
        }
        
        try {
            // Usar Redis facade directamente
            $keys = Redis::keys('*search:*');
            if ($keys && count($keys) > 0) {
                Redis::del($keys);
            }
            
            return response()->json([
                'message' => 'Caché de búsquedas limpiado exitosamente',
                'keys_deleted' => count($keys)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al limpiar caché: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de caché
     */
    public function getCacheStats()
    {
        if (Auth::user()->role->nombre !== 'Admin') {
            abort(403);
        }
        
        try {
            // Usar Redis facade directamente
            $info = Redis::info();
            $searchKeys = Redis::keys('*search:*');
            
            // Obtener información específica de memoria y estadísticas
            $memoryInfo = is_array($info) ? $info : [];
            
            return response()->json([
                'total_keys' => count($searchKeys),
                'memory_used' => $memoryInfo['used_memory_human'] ?? 'No disponible',
                'connected_clients' => $memoryInfo['connected_clients'] ?? 0,
                'hits' => $memoryInfo['keyspace_hits'] ?? 0,
                'misses' => $memoryInfo['keyspace_misses'] ?? 0,
                'hit_rate' => isset($memoryInfo['keyspace_hits']) && isset($memoryInfo['keyspace_misses']) && ($memoryInfo['keyspace_hits'] + $memoryInfo['keyspace_misses']) > 0
                    ? round(($memoryInfo['keyspace_hits'] / ($memoryInfo['keyspace_hits'] + $memoryInfo['keyspace_misses'])) * 100, 2) . '%'
                    : '0%',
                'redis_version' => $memoryInfo['redis_version'] ?? 'N/A',
                'uptime_in_seconds' => $memoryInfo['uptime_in_seconds'] ?? 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener estadísticas de Redis',
                'message' => $e->getMessage(),
                'total_keys' => 0,
                'memory_used' => 'Error',
                'connected_clients' => 'Error',
                'hits' => 0,
                'misses' => 0,
                'hit_rate' => '0%'
            ], 500);
        }
    }
}
