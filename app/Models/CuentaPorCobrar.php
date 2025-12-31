<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CuentaPorCobrar extends Model
{
    protected $table = 'cuentas_por_cobrar';
    protected $primaryKey = 'id_cuenta_por_cobrar';
    
    protected $fillable = [
        'cliente_id',
        'venta_id',
        'monto_total',
        'monto_pendiente',
        'monto_pagado',
        'fecha_vencimiento',
        'estado',
        'notas',
        'dias_credito',
        'interes_moratorio',
        'interes_acumulado',
        'creado_por',
    ];
    
    protected $casts = [
        'fecha_vencimiento' => 'date',
        'monto_total' => 'decimal:2',
        'monto_pendiente' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
        'interes_moratorio' => 'decimal:2',
        'interes_acumulado' => 'decimal:2',
    ];
    
    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id_cliente');
    }
    
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id', 'id_venta');
    }
    
    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por', 'id_usuario');
    }
    
    public function pagos()
    {
        return $this->hasMany(PagoCuentaPorCobrar::class, 'cuenta_por_cobrar_id', 'id_cuenta_por_cobrar');
    }
    
    // Scopes
    public function scopePendientes($query)
    {
        return $query->whereIn('estado', ['PENDIENTE', 'PARCIAL']);
    }
    
    public function scopeVencidas($query)
    {
        return $query->where('estado', 'VENCIDO')
                    ->orWhere(function($q) {
                        $q->whereIn('estado', ['PENDIENTE', 'PARCIAL'])
                          ->where('fecha_vencimiento', '<', now());
                    });
    }
    
    public function scopePorCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }
    
    public function scopePorVencer($query, $dias = 7)
    {
        return $query->whereIn('estado', ['PENDIENTE', 'PARCIAL'])
                    ->whereBetween('fecha_vencimiento', [now(), now()->addDays($dias)]);
    }
    
    // Métodos
    public function estaVencida()
    {
        return $this->fecha_vencimiento < Carbon::today() && 
               in_array($this->estado, ['PENDIENTE', 'PARCIAL']);
    }
    
    public function diasVencimiento()
    {
        if ($this->estaVencida()) {
            return Carbon::today()->diffInDays($this->fecha_vencimiento);
        }
        return 0;
    }
    
    public function calcularInteresMoratorio()
    {
        if (!$this->estaVencida() || $this->interes_moratorio <= 0) {
            return 0;
        }
        
        $diasVencidos = $this->diasVencimiento();
        $interesAnual = $this->interes_moratorio / 100;
        $interesDiario = $interesAnual / 365;
        
        return round($this->monto_pendiente * $interesDiario * $diasVencidos, 2);
    }
    
    public function actualizarInteres()
    {
        $nuevoInteres = $this->calcularInteresMoratorio();
        if ($nuevoInteres !== $this->interes_acumulado) {
            $this->interes_acumulado = $nuevoInteres;
            $this->save();
        }
    }
    
    public function registrarPago($monto, $metodoPago, $referencia = null, $notas = null, $usuarioId = null)
    {
        $montoPago = min($monto, $this->monto_pendiente + $this->interes_acumulado);
        
        // Crear registro de pago
        $pago = PagoCuentaPorCobrar::create([
            'cuenta_por_cobrar_id' => $this->id_cuenta_por_cobrar,
            'monto_pago' => $montoPago,
            'fecha_pago' => now(),
            'metodo_pago_id' => $metodoPago,
            'referencia' => $referencia,
            'notas' => $notas,
            'interes_pagado' => min($montoPago, $this->interes_acumulado),
            'registrado_por' => $usuarioId ?? auth()->id(),
        ]);
        
        // Actualizar cuenta
        $montoCapital = $montoPago - $pago->interes_pagado;
        $this->monto_pagado += $montoCapital;
        $this->monto_pendiente -= $montoCapital;
        $this->interes_acumulado -= $pago->interes_pagado;
        
        // Actualizar estado
        if ($this->monto_pendiente <= 0) {
            $this->estado = 'PAGADO';
        } elseif ($this->monto_pagado > 0) {
            $this->estado = 'PARCIAL';
        }
        
        $this->save();
        
        return $pago;
    }
    
    public function marcarComoVencida()
    {
        if ($this->estaVencida() && in_array($this->estado, ['PENDIENTE', 'PARCIAL'])) {
            $this->estado = 'VENCIDO';
            $this->save();
        }
    }
    
    public function getMontoTotalConInteresAttribute()
    {
        return $this->monto_pendiente + $this->interes_acumulado;
    }
    
    public function getPorcentajePagadoAttribute()
    {
        if ($this->monto_total <= 0) {
            return 0;
        }
        return round(($this->monto_pagado / $this->monto_total) * 100, 1);
    }
}
