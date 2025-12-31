<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleDevolucion extends Model
{
    protected $table = 'detalle_devoluciones';
    protected $primaryKey = 'id_detalle_devolucion';
    
    protected $fillable = [
        'devolucion_id',
        'producto_id',
        'cantidad_devuelta',
        'precio_unitario',
        'subtotal',
        'observaciones',
        'condicion_producto',
        'retornar_inventario',
    ];
    
    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'retornar_inventario' => 'boolean',
    ];
    
    // Relaciones
    public function devolucion()
    {
        return $this->belongsTo(Devolucion::class, 'devolucion_id', 'id_devolucion');
    }
    
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'id_producto');
    }
    
    // Métodos
    public function getCondicionesDisponibles()
    {
        return [
            'NUEVO' => 'Nuevo/Sin usar',
            'USADO_BUENO' => 'Usado en buen estado',
            'USADO_REGULAR' => 'Usado en estado regular',
            'DAÑADO' => 'Dañado/Defectuoso',
        ];
    }
    
    public function puedeRetornarInventario()
    {
        return in_array($this->condicion_producto, ['NUEVO', 'USADO_BUENO']) && 
               $this->retornar_inventario;
    }
    
    public function getColorCondicionAttribute()
    {
        $colores = [
            'NUEVO' => 'success',
            'USADO_BUENO' => 'info',
            'USADO_REGULAR' => 'warning',
            'DAÑADO' => 'danger',
        ];
        
        return $colores[$this->condicion_producto] ?? 'secondary';
    }
    
    // Boot method para calcular subtotal automáticamente
    protected static function booted()
    {
        static::creating(function ($detalle) {
            $detalle->subtotal = $detalle->cantidad_devuelta * $detalle->precio_unitario;
        });
        
        static::updating(function ($detalle) {
            $detalle->subtotal = $detalle->cantidad_devuelta * $detalle->precio_unitario;
        });
    }
}
