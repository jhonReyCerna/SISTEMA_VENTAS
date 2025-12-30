@extends('layouts.admin')

@section('title', 'Productos Más Vendidos')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1>Reporte de Productos Más Vendidos</h1>
                    <p class="text-muted">Sirve para toma de decisiones en reposición de stock</p>
                </div>
                <a href="{{ route('reportes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Resumen -->
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-boxes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Productos Vendidos</span>
                    <span class="info-box-number">{{ $productos->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-warning">
                <span class="info-box-icon"><i class="fas fa-cubes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Unidades Vendidas</span>
                    <span class="info-box-number">{{ $productos->sum('cantidad_vendida') }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Generado</span>
                    <span class="info-box-number">S/ {{ number_format($productos->sum('total_generado'), 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de resultados -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-star"></i> Ranking de Productos
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
                            <th width="10%">Posición</th>
                            <th>Producto</th>
                            <th class="text-center">Cantidad Vendida</th>
                            <th class="text-right">Total Generado</th>
                            <th class="text-right">Precio Promedio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productos as $index => $producto)
                            <tr>
                                <td class="text-center">
                                    @if($index === 0)
                                        <span class="badge badge-warning badge-lg">
                                            <i class="fas fa-trophy"></i> TOP 1
                                        </span>
                                    @elseif($index === 1)
                                        <span class="badge badge-secondary badge-lg">
                                            <i class="fas fa-medal"></i> TOP 2
                                        </span>
                                    @elseif($index === 2)
                                        <span class="badge badge-dark badge-lg">
                                            <i class="fas fa-award"></i> TOP 3
                                        </span>
                                    @else
                                        <span class="badge badge-light">#{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td><strong>{{ $producto->producto }}</strong></td>
                                <td class="text-center">
                                    <span class="badge badge-primary badge-lg">{{ $producto->cantidad_vendida }}</span> unidades
                                </td>
                                <td class="text-right">
                                    <strong>S/ {{ number_format($producto->total_generado, 2) }}</strong>
                                </td>
                                <td class="text-right">
                                    S/ {{ number_format($producto->total_generado / $producto->cantidad_vendida, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay productos vendidos aún</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($productos->count() > 0)
                        <tfoot class="bg-light">
                            <tr>
                                <th colspan="2" class="text-right">TOTALES:</th>
                                <th class="text-center">{{ $productos->sum('cantidad_vendida') }} unidades</th>
                                <th class="text-right">S/ {{ number_format($productos->sum('total_generado'), 2) }}</th>
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
