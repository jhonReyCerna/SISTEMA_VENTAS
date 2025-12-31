<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';
    protected $primaryKey = 'id_notificacion';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'tipo',
        'titulo',
        'mensaje',
        'icono',
        'color',
        'url',
        'leida',
        'fecha_creacion'
    ];

    protected $casts = [
        'leida' => 'boolean',
        'fecha_creacion' => 'datetime'
    ];

    /**
     * Usuario al que pertenece la notificación
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Marcar notificación como leída
     */
    public function marcarComoLeida()
    {
        $this->update(['leida' => true]);
    }

    /**
     * Crear notificación de stock bajo
     */
    public static function crearAlertaStockBajo($producto, $usuario = null)
    {
        return self::create([
            'id_usuario' => $usuario ? $usuario->id_usuario : null,
            'tipo' => 'stock_bajo',
            'titulo' => '⚠️ Stock Bajo',
            'mensaje' => "El producto '{$producto->nombre}' tiene stock bajo ({$producto->stock} unidades)",
            'icono' => 'fa-exclamation-triangle',
            'color' => 'warning',
            'url' => route('productos.show', $producto->id_producto)
        ]);
    }

    /**
     * Crear notificación de nueva venta
     */
    public static function crearNotificacionVenta($venta, $usuario = null)
    {
        return self::create([
            'id_usuario' => $usuario ? $usuario->id_usuario : null,
            'tipo' => 'venta_nueva',
            'titulo' => '💰 Nueva Venta',
            'mensaje' => "Nueva venta registrada por S/ " . number_format($venta->total, 2),
            'icono' => 'fa-shopping-cart',
            'color' => 'success',
            'url' => route('ventas.show', $venta->id_venta)
        ]);
    }

    /**
     * Crear notificación del sistema
     */
    public static function crearNotificacionSistema($titulo, $mensaje, $usuario = null, $color = 'info')
    {
        return self::create([
            'id_usuario' => $usuario ? $usuario->id_usuario : null,
            'tipo' => 'sistema',
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'icono' => 'fa-info-circle',
            'color' => $color
        ]);
    }
}
