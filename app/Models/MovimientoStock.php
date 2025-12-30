<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoStock extends Model
{
    use HasFactory;

    protected $table = 'movimientos_stock';

    protected $primaryKey = 'id_movimiento';

    public $timestamps = false;

    protected $fillable = [
        'id_producto',
        'tipo_movimiento',
        'cantidad',
        'fecha_movimiento',
        'motivo',
    ];

    protected $casts = [
        'fecha_movimiento' => 'datetime',
    ];

    /**
     * Get the producto that owns the movimiento.
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}
