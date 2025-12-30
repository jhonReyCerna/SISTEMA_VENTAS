@extends('layouts.admin')

@section('title', 'Reporte de Ventas por Vendedor')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1>Reporte de Ventas por Vendedor</h1>
                    <p class="text-muted">Indicador de productividad del personal</p>
                </div>
                <a href="{{ route('reportes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Resumen General -->
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="info-box bg-primary">
                <span class="info-box-icon"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Vendedores</span>
                    <span class="info-box-number">{{ $ventas_por_usuario->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-shopping-cart"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Ventas</span>
                    <span class="info-box-number">{{ $ventas_por_usuario->sum('total_ventas') }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Monto Total</span>
                    <span class="info-box-number">S/ {{ number_format($ventas_por_usuario->sum('monto_total'), 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de resultados -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-bar"></i> Desempeño por Vendedor
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
                            <th width="10%">Ranking</th>
                            <th>Vendedor</th>
                            <th class="text-center">Total Ventas</th>
                            <th class="text-right">Monto Total</th>
                            <th class="text-right">Promedio por Venta</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventas_por_usuario as $index => $vendedor)
                            <tr>
                                <td class="text-center">
                                    @if($index === 0)
                                        <span class="badge badge-warning badge-lg">
                                            <i class="fas fa-trophy"></i> #1
                                        </span>
                                    @elseif($index === 1)
                                        <span class="badge badge-secondary badge-lg">
                                            <i class="fas fa-medal"></i> #2
                                        </span>
                                    @elseif($index === 2)
                                        <span class="badge badge-dark badge-lg">
                                            <i class="fas fa-award"></i> #3
                                        </span>
                                    @else
                                        <span class="badge badge-light">#{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td><strong>{{ $vendedor->vendedor }}</strong></td>
                                <td class="text-center">
                                    <span class="badge badge-primary">{{ $vendedor->total_ventas }}</span>
                                </td>
                                <td class="text-right">
                                    <strong>S/ {{ number_format($vendedor->monto_total, 2) }}</strong>
                                </td>
                                <td class="text-right">
                                    S/ {{ number_format($vendedor->monto_total / $vendedor->total_ventas, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay datos de ventas disponibles</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($ventas_por_usuario->count() > 0)
                        <tfoot class="bg-light">
                            <tr>
                                <th colspan="2" class="text-right">TOTALES:</th>
                                <th class="text-center">{{ $ventas_por_usuario->sum('total_ventas') }}</th>
                                <th class="text-right">S/ {{ number_format($ventas_por_usuario->sum('monto_total'), 2) }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
