<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Descuento extends Model
{
    protected $table = 'descuentos';
    protected $primaryKey = 'id_descuento';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'valor',
        'aplicacion',
        'cliente_id',
        'producto_id',
        'categoria_id',
        'monto_minimo',
        'cantidad_minima',
        'fecha_inicio',
        'fecha_fin',
        'activo',
        'usos_maximos',
        'usos_actuales',
    ];
    
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
        'valor' => 'decimal:2',
        'monto_minimo' => 'decimal:2',
    ];
    
    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id_cliente');
    }
    
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'id_producto');
    }
    
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id', 'id_categoria');
    }
    
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'descuento_id', 'id_descuento');
    }
    
    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
    
    public function scopeVigentes($query)
    {
        return $query->where('fecha_inicio', '<=', now())
                    ->where('fecha_fin', '>=', now());
    }
    
    public function scopeParaCliente($query, $clienteId)
    {
        return $query->where(function($q) use ($clienteId) {
            $q->where('aplicacion', 'GENERAL')
              ->orWhere(function($subQ) use ($clienteId) {
                  $subQ->where('aplicacion', 'CLIENTE')
                       ->where('cliente_id', $clienteId);
              });
        });
    }
    
    // Métodos
    public function esAplicable($cliente = null, $producto = null, $categoria = null, $monto = 0, $cantidad = 0)
    {
        // Verificar si está activo y vigente
        if (!$this->activo || !$this->esVigente()) {
            return false;
        }
        
        // Verificar límite de usos
        if ($this->usos_maximos && $this->usos_actuales >= $this->usos_maximos) {
            return false;
        }
        
        // Verificar monto mínimo
        if ($this->monto_minimo && $monto < $this->monto_minimo) {
            return false;
        }
        
        // Verificar cantidad mínima
        if ($this->cantidad_minima && $cantidad < $this->cantidad_minima) {
            return false;
        }
        
        // Verificar aplicación
        switch ($this->aplicacion) {
            case 'GENERAL':
                return true;
            case 'CLIENTE':
                return $cliente && $cliente->id_cliente == $this->cliente_id;
            case 'PRODUCTO':
                return $producto && $producto->id_producto == $this->producto_id;
            case 'CATEGORIA':
                return $categoria && $categoria->id_categoria == $this->categoria_id;
            default:
                return false;
        }
    }
    
    public function esVigente()
    {
        $hoy = Carbon::today();
        return $hoy->greaterThanOrEqualTo($this->fecha_inicio) && 
               $hoy->lessThanOrEqualTo($this->fecha_fin);
    }
    
    public function calcularDescuento($monto)
    {
        if ($this->tipo === 'PORCENTAJE') {
            return round(($monto * $this->valor) / 100, 2);
        } else {
            return min($this->valor, $monto);
        }
    }
    
    public function incrementarUso()
    {
        $this->increment('usos_actuales');
    }
    
    public function decrementarUso()
    {
        if ($this->usos_actuales > 0) {
            $this->decrement('usos_actuales');
        }
    }
}
