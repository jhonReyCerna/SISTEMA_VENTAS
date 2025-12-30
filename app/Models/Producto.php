<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $primaryKey = 'id_producto';

    public $timestamps = false;

    protected $fillable = [
        'id_categoria',
        'nombre',
        'descripcion',
        'precio_compra',
        'precio_venta',
        'stock',
        'stock_minimo',
        'estado',
    ];

    protected $casts = [
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
    ];

    /**
     * Get the category that the product belongs to.
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    /**
     * Get the movimientos for the product.
     */
    public function movimientos()
    {
        return $this->hasMany(MovimientoStock::class, 'id_producto', 'id_producto');
    }
}
