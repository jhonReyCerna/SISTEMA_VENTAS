@extends('layouts.admin')

@section('title', 'Stock Actual y Alertas')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1>Reporte de Stock Actual y Alertas</h1>
                    <p class="text-muted">Control preventivo de inventario</p>
                </div>
                <a href="{{ route('reportes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Resumen de Alertas -->
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-boxes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Productos</span>
                    <span class="info-box-number">{{ $productos->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-danger">
                <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Stock Bajo</span>
                    <span class="info-box-number">{{ $productos->where('estado_stock', 'BAJO STOCK')->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Stock Normal</span>
                    <span class="info-box-number">{{ $productos->where('estado_stock', 'STOCK NORMAL')->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-warning">
                <span class="info-box-icon"><i class="fas fa-cubes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Unidades Totales</span>
                    <span class="info-box-number">{{ $productos->sum('stock') }}</span>
                </div>
            </div>
        </div>
    </div>

    @if($productos->where('estado_stock', 'BAJO STOCK')->count() > 0)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>¡Atención!</strong> Hay {{ $productos->where('estado_stock', 'BAJO STOCK')->count() }} producto(s) con stock bajo que requieren reabastecimiento.
        </div>
    @endif

    <!-- Tabla de Stock -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-warehouse"></i> Inventario Actual
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-success" onclick="window.print()">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>Producto</th>
                            <th class="text-center">Stock Actual</th>
                            <th class="text-center">Stock Mínimo</th>
                            <th class="text-center">Diferencia</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productos as $producto)
                            <tr class="{{ $producto->estado_stock === 'BAJO STOCK' ? 'table-danger' : '' }}">
                                <td><strong>{{ $producto->nombre }}</strong></td>
                                <td class="text-center">
                                    <span class="badge {{ $producto->estado_stock === 'BAJO STOCK' ? 'badge-danger' : 'badge-success' }} badge-lg">
                                        {{ $producto->stock }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $producto->stock_minimo }}</td>
                                <td class="text-center">
                                    @php
                                        $diferencia = $producto->stock - $producto->stock_minimo;
                                    @endphp
                                    <span class="badge {{ $diferencia <= 0 ? 'badge-danger' : 'badge-info' }}">
                                        {{ $diferencia > 0 ? '+' : '' }}{{ $diferencia }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($producto->estado_stock === 'BAJO STOCK')
                                        <span class="badge badge-danger">
                                            <i class="fas fa-exclamation-triangle"></i> BAJO STOCK
                                        </span>
                                    @else
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle"></i> NORMAL
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($producto->estado_stock === 'BAJO STOCK')
                                        <a href="{{ route('movimientos.create') }}?producto={{ $producto->id_producto }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus"></i> Reabastecer
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No hay productos registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
