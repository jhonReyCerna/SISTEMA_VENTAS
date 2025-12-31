<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    protected $table = 'metodos_pago';
    protected $primaryKey = 'id_metodo_pago';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'requiere_referencia',
        'activo',
        'comision_porcentaje',
        'comision_fija',
        'orden',
    ];
    
    protected $casts = [
        'requiere_referencia' => 'boolean',
        'activo' => 'boolean',
        'comision_porcentaje' => 'decimal:2',
        'comision_fija' => 'decimal:2',
    ];
    
    // Relaciones
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'metodo_pago_id', 'id_metodo_pago');
    }
    
    public function pagosCuentas()
    {
        return $this->hasMany(PagoCuentaPorCobrar::class, 'metodo_pago_id', 'id_metodo_pago');
    }
    
    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
    
    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden');
    }
    
    // Métodos
    public function calcularComision($monto)
    {
        $comision = 0;
        
        // Comisión por porcentaje
        if ($this->comision_porcentaje > 0) {
            $comision += ($monto * $this->comision_porcentaje) / 100;
        }
        
        // Comisión fija
        if ($this->comision_fija > 0) {
            $comision += $this->comision_fija;
        }
        
        return round($comision, 2);
    }
    
    public function esCredito()
    {
        return strtolower($this->nombre) === 'credito' || strtolower($this->nombre) === 'crédito';
    }
    
    public function getIconoAttribute()
    {
        $iconos = [
            'efectivo' => 'fas fa-money-bill-wave',
            'tarjeta de credito' => 'fas fa-credit-card',
            'tarjeta de debito' => 'fas fa-credit-card',
            'transferencia bancaria' => 'fas fa-university',
            'credito' => 'fas fa-handshake',
        ];
        
        return $iconos[strtolower($this->nombre)] ?? 'fas fa-wallet';
    }
    
    public function getColorAttribute()
    {
        $colores = [
            'efectivo' => 'success',
            'tarjeta de credito' => 'primary',
            'tarjeta de debito' => 'info',
            'transferencia bancaria' => 'warning',
            'credito' => 'secondary',
        ];
        
        return $colores[strtolower($this->nombre)] ?? 'dark';
    }
}
