@extends('layouts.admin')

@section('title', 'Reporte de Ventas por Fecha')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h1>Reporte de Ventas por Rango de Fechas</h1>
            <p class="text-muted">Permite analizar el comportamiento de ventas en un periodo determinado</p>
        </div>
    </div>

    <!-- Filtro de fechas -->
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('reportes.ventas-por-fecha') }}" method="GET" class="form-inline">
                <div class="form-group mr-3">
                    <label for="fecha_inicio" class="mr-2">Fecha Inicio:</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ $fecha_inicio }}">
                </div>
                <div class="form-group mr-3">
                    <label for="fecha_fin" class="mr-2">Fecha Fin:</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ $fecha_fin }}">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filtrar
                </button>
                <a href="{{ route('reportes.index') }}" class="btn btn-secondary ml-2">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </form>
        </div>
    </div>

    <!-- Resumen -->
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-shopping-cart"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Ventas</span>
                    <span class="info-box-number">{{ $ventas->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Monto Total</span>
                    <span class="info-box-number">S/ {{ number_format($total_general, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-warning">
                <span class="info-box-icon"><i class="fas fa-calculator"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Promedio por Venta</span>
                    <span class="info-box-number">S/ {{ $ventas->count() > 0 ? number_format($total_general / $ventas->count(), 2) : '0.00' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de resultados -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i> Detalle de Ventas
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
                            <th>ID Venta</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Vendedor</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventas as $venta)
                            <tr>
                                <td>{{ $venta->id_venta }}</td>
                                <td>{{ date('d/m/Y H:i', strtotime($venta->fecha_venta)) }}</td>
                                <td>{{ $venta->cliente }}</td>
                                <td>{{ $venta->vendedor }}</td>
                                <td class="text-right"><strong>S/ {{ number_format($venta->total, 2) }}</strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay ventas en el periodo seleccionado</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($ventas->count() > 0)
                        <tfoot class="bg-light">
                            <tr>
                                <th colspan="4" class="text-right">TOTAL GENERAL:</th>
                                <th class="text-right">S/ {{ number_format($total_general, 2) }}</th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
