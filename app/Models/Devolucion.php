<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Devolucion extends Model
{
    protected $table = 'devoluciones';
    protected $primaryKey = 'id_devolucion';
    
    protected $fillable = [
        'numero_devolucion',
        'venta_id',
        'cliente_id',
        'fecha_devolucion',
        'tipo_devolucion',
        'motivo',
        'descripcion_motivo',
        'monto_devuelto',
        'estado',
        'accion_tomada',
        'notas_internas',
        'procesado_por',
        'fecha_procesado',
        'creado_por',
    ];
    
    protected $casts = [
        'fecha_devolucion' => 'date',
        'fecha_procesado' => 'datetime',
        'monto_devuelto' => 'decimal:2',
    ];
    
    // Relaciones
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id', 'id_venta');
    }
    
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id_cliente');
    }
    
    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por', 'id_usuario');
    }
    
    public function procesadoPor()
    {
        return $this->belongsTo(User::class, 'procesado_por', 'id_usuario');
    }
    
    public function detalles()
    {
        return $this->hasMany(DetalleDevolucion::class, 'devolucion_id', 'id_devolucion');
    }
    
    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'PENDIENTE');
    }
    
    public function scopeAprobadas($query)
    {
        return $query->where('estado', 'APROBADO');
    }
    
    public function scopePorCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }
    
    public function scopePorFecha($query, $fechaInicio, $fechaFin = null)
    {
        if ($fechaFin) {
            return $query->whereBetween('fecha_devolucion', [$fechaInicio, $fechaFin]);
        }
        return $query->whereDate('fecha_devolucion', $fechaInicio);
    }
    
    // Métodos
    public static function generarNumero()
    {
        $fecha = now()->format('Ymd');
        $ultimo = self::where('numero_devolucion', 'like', "DEV{$fecha}%")
                     ->orderBy('numero_devolucion', 'desc')
                     ->first();
        
        if ($ultimo) {
            $ultimoNumero = intval(substr($ultimo->numero_devolucion, -4));
            $nuevoNumero = $ultimoNumero + 1;
        } else {
            $nuevoNumero = 1;
        }
        
        return "DEV{$fecha}" . str_pad($nuevoNumero, 4, '0', STR_PAD_LEFT);
    }
    
    public function aprobar($usuarioId = null, $accionTomada = null, $notas = null)
    {
        $this->estado = 'APROBADO';
        $this->accion_tomada = $accionTomada;
        $this->notas_internas = $notas;
        $this->procesado_por = $usuarioId ?? auth()->id();
        $this->fecha_procesado = now();
        $this->save();
        
        // Si la acción es devolver al inventario, actualizar stock
        if ($accionTomada === 'REEMBOLSO' || $accionTomada === 'CAMBIO') {
            $this->devolverInventario();
        }
        
        return $this;
    }
    
    public function rechazar($usuarioId = null, $notas = null)
    {
        $this->estado = 'RECHAZADO';
        $this->notas_internas = $notas;
        $this->procesado_por = $usuarioId ?? auth()->id();
        $this->fecha_procesado = now();
        $this->save();
        
        return $this;
    }
    
    public function procesar($usuarioId = null)
    {
        if ($this->estado !== 'APROBADO') {
            throw new \Exception('La devolución debe estar aprobada para ser procesada.');
        }
        
        $this->estado = 'PROCESADO';
        if (!$this->procesado_por) {
            $this->procesado_por = $usuarioId ?? auth()->id();
        }
        if (!$this->fecha_procesado) {
            $this->fecha_procesado = now();
        }
        $this->save();
        
        return $this;
    }
    
    public function devolverInventario()
    {
        foreach ($this->detalles as $detalle) {
            if ($detalle->retornar_inventario) {
                $producto = $detalle->producto;
                $cantidadADevolver = $detalle->cantidad_devuelta;
                
                // Solo devolver si el producto está en buen estado
                if (in_array($detalle->condicion_producto, ['NUEVO', 'USADO_BUENO'])) {
                    $producto->increment('stock', $cantidadADevolver);
                    
                    // Registrar movimiento de stock
                    MovimientoStock::create([
                        'id_producto' => $producto->id_producto,
                        'tipo_movimiento' => 'ENTRADA',
                        'cantidad' => $cantidadADevolver,
                        'motivo' => 'DEVOLUCION',
                        'referencia_id' => $this->id_devolucion,
                        'observaciones' => "Devolución #{$this->numero_devolucion} - {$detalle->condicion_producto}",
                        'id_usuario' => $this->procesado_por ?? $this->creado_por,
                    ]);
                }
            }
        }
    }
    
    public function agregarDetalle($productoId, $cantidad, $precioUnitario, $observaciones = null, $condicion = 'NUEVO')
    {
        return DetalleDevolucion::create([
            'devolucion_id' => $this->id_devolucion,
            'producto_id' => $productoId,
            'cantidad_devuelta' => $cantidad,
            'precio_unitario' => $precioUnitario,
            'subtotal' => $cantidad * $precioUnitario,
            'observaciones' => $observaciones,
            'condicion_producto' => $condicion,
        ]);
    }
    
    public function calcularMontoTotal()
    {
        $this->monto_devuelto = $this->detalles()->sum('subtotal');
        $this->save();
        return $this->monto_devuelto;
    }
    
    public function getMotivosDisponibles()
    {
        return [
            'DEFECTUOSO' => 'Producto defectuoso',
            'NO_CONFORME' => 'No conforme con la descripción',
            'CAMBIO_MENTE' => 'Cambio de opinión',
            'ERROR_VENDEDOR' => 'Error del vendedor',
            'GARANTIA' => 'Garantía del producto',
            'OTRO' => 'Otro motivo',
        ];
    }
    
    public function getAccionesDisponibles()
    {
        return [
            'REEMBOLSO' => 'Reembolso de dinero',
            'CAMBIO' => 'Cambio por otro producto',
            'CREDITO_TIENDA' => 'Crédito en tienda',
            'REPARACION' => 'Reparación del producto',
        ];
    }
    
    public function getDiasTranscurridos()
    {
        return $this->venta->fecha_venta->diffInDays($this->fecha_devolucion);
    }
}
