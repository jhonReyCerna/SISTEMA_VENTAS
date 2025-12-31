<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Cupon extends Model
{
    protected $table = 'cupones';
    protected $primaryKey = 'id_cupon';
    
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'tipo_descuento',
        'valor_descuento',
        'monto_minimo',
        'descuento_maximo',
        'fecha_inicio',
        'fecha_expiracion',
        'usos_maximos',
        'usos_por_cliente',
        'usos_actuales',
        'activo',
        'primera_compra',
        'productos_incluidos',
        'categorias_incluidas',
        'clientes_incluidos',
        'creado_por',
    ];
    
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_expiracion' => 'date',
        'activo' => 'boolean',
        'primera_compra' => 'boolean',
        'valor_descuento' => 'decimal:2',
        'monto_minimo' => 'decimal:2',
        'descuento_maximo' => 'decimal:2',
        'productos_incluidos' => 'array',
        'categorias_incluidas' => 'array',
        'clientes_incluidos' => 'array',
    ];
    
    // Relaciones
    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por', 'id_usuario');
    }
    
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'cupon_id', 'id_cupon');
    }
    
    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
    
    public function scopeVigentes($query)
    {
        return $query->where('fecha_inicio', '<=', now())
                    ->where('fecha_expiracion', '>=', now());
    }
    
    public function scopePorCodigo($query, $codigo)
    {
        return $query->where('codigo', strtoupper($codigo));
    }
    
    // Métodos
    public static function generarCodigo($longitud = 8)
    {
        do {
            $codigo = strtoupper(Str::random($longitud));
        } while (self::where('codigo', $codigo)->exists());
        
        return $codigo;
    }
    
    public function esValido($cliente = null, $productos = [], $montoTotal = 0)
    {
        // Verificar si está activo
        if (!$this->activo) {
            return ['valido' => false, 'mensaje' => 'Cupón desactivado'];
        }
        
        // Verificar vigencia
        if (!$this->esVigente()) {
            return ['valido' => false, 'mensaje' => 'Cupón expirado o no vigente'];
        }
        
        // Verificar límite de usos globales
        if ($this->usos_maximos > 0 && $this->usos_actuales >= $this->usos_maximos) {
            return ['valido' => false, 'mensaje' => 'Cupón agotado'];
        }
        
        // Verificar monto mínimo
        if ($this->monto_minimo && $montoTotal < $this->monto_minimo) {
            return ['valido' => false, 'mensaje' => "Compra mínima requerida: S/ {$this->monto_minimo}"];
        }
        
        // Verificar cliente específico
        if ($this->clientes_incluidos && $cliente) {
            if (!in_array($cliente->id_cliente, $this->clientes_incluidos)) {
                return ['valido' => false, 'mensaje' => 'Cupón no disponible para este cliente'];
            }
        }
        
        // Verificar si es solo para primera compra
        if ($this->primera_compra && $cliente) {
            $ventasAnteriores = Venta::where('id_cliente', $cliente->id_cliente)->count();
            if ($ventasAnteriores > 0) {
                return ['valido' => false, 'mensaje' => 'Cupón solo válido para primera compra'];
            }
        }
        
        // Verificar límite por cliente
        if ($cliente) {
            $usosCliente = $this->ventas()->where('id_cliente', $cliente->id_cliente)->count();
            if ($usosCliente >= $this->usos_por_cliente) {
                return ['valido' => false, 'mensaje' => 'Has alcanzado el límite de usos de este cupón'];
            }
        }
        
        return ['valido' => true, 'mensaje' => 'Cupón válido'];
    }
    
    public function esVigente()
    {
        $hoy = Carbon::today();
        return $hoy->greaterThanOrEqualTo($this->fecha_inicio) && 
               $hoy->lessThanOrEqualTo($this->fecha_expiracion);
    }
    
    public function calcularDescuento($montoTotal, $productos = [])
    {
        $montoAplicable = $montoTotal;
        
        // Si tiene productos o categorías específicas, calcular solo sobre esos
        if ($this->productos_incluidos || $this->categorias_incluidas) {
            $montoAplicable = 0;
            foreach ($productos as $producto) {
                $incluido = false;
                
                // Verificar si el producto está incluido
                if ($this->productos_incluidos && in_array($producto['id'], $this->productos_incluidos)) {
                    $incluido = true;
                }
                
                // Verificar si la categoría está incluida
                if ($this->categorias_incluidas && in_array($producto['categoria_id'], $this->categorias_incluidas)) {
                    $incluido = true;
                }
                
                if ($incluido) {
                    $montoAplicable += $producto['subtotal'];
                }
            }
        }
        
        $descuento = 0;
        
        if ($this->tipo_descuento === 'PORCENTAJE') {
            $descuento = ($montoAplicable * $this->valor_descuento) / 100;
        } else {
            $descuento = min($this->valor_descuento, $montoAplicable);
        }
        
        // Aplicar límite de descuento máximo si existe
        if ($this->descuento_maximo && $descuento > $this->descuento_maximo) {
            $descuento = $this->descuento_maximo;
        }
        
        return round($descuento, 2);
    }
    
    public function usar()
    {
        $this->increment('usos_actuales');
    }
    
    public function revertirUso()
    {
        if ($this->usos_actuales > 0) {
            $this->decrement('usos_actuales');
        }
    }
}
