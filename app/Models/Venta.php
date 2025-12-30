<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';

    protected $primaryKey = 'id_venta';

    public $timestamps = false;

    protected $fillable = [
        'id_cliente',
        'id_usuario',
        'fecha_venta',
        'total',
        'tipo_comprobante',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'fecha_venta' => 'datetime',
    ];

    /**
     * Get the cliente that owns the venta.
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    /**
     * Get the usuario that owns the venta.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Get the detalles for the venta.
     */
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'id_venta', 'id_venta');
    }
}
