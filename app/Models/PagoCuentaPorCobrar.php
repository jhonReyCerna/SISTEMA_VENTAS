<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoCuentaPorCobrar extends Model
{
    protected $table = 'pagos_cuentas_por_cobrar';
    protected $primaryKey = 'id_pago';
    
    protected $fillable = [
        'cuenta_por_cobrar_id',
        'monto_pago',
        'fecha_pago',
        'metodo_pago_id',
        'referencia',
        'notas',
        'interes_pagado',
        'registrado_por',
    ];
    
    protected $casts = [
        'fecha_pago' => 'date',
        'monto_pago' => 'decimal:2',
        'interes_pagado' => 'decimal:2',
    ];
    
    // Relaciones
    public function cuentaPorCobrar()
    {
        return $this->belongsTo(CuentaPorCobrar::class, 'cuenta_por_cobrar_id', 'id_cuenta_por_cobrar');
    }
    
    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'metodo_pago_id', 'id_metodo_pago');
    }
    
    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por', 'id_usuario');
    }
    
    // Scopes
    public function scopePorFecha($query, $fechaInicio, $fechaFin = null)
    {
        if ($fechaFin) {
            return $query->whereBetween('fecha_pago', [$fechaInicio, $fechaFin]);
        }
        return $query->whereDate('fecha_pago', $fechaInicio);
    }
    
    public function scopePorMetodoPago($query, $metodoPagoId)
    {
        return $query->where('metodo_pago_id', $metodoPagoId);
    }
    
    // Métodos
    public function getMontoCapitalAttribute()
    {
        return $this->monto_pago - $this->interes_pagado;
    }
    
    public function anular($usuarioId = null, $motivo = null)
    {
        // Solo se puede anular si no ha pasado mucho tiempo
        if ($this->created_at->diffInHours(now()) > 24) {
            throw new \Exception('No se puede anular un pago después de 24 horas.');
        }
        
        // Revertir el pago en la cuenta por cobrar
        $cuenta = $this->cuentaPorCobrar;
        $cuenta->monto_pagado -= $this->monto_capital;
        $cuenta->monto_pendiente += $this->monto_capital;
        $cuenta->interes_acumulado += $this->interes_pagado;
        
        // Actualizar estado de la cuenta
        if ($cuenta->monto_pagado <= 0) {
            $cuenta->estado = 'PENDIENTE';
        } else {
            $cuenta->estado = 'PARCIAL';
        }
        
        $cuenta->save();
        
        // Eliminar el registro de pago
        $this->delete();
        
        return true;
    }
}
