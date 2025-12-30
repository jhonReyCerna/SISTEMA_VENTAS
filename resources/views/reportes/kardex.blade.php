@extends('layouts.admin')

@section('title', 'Kardex de Movimientos')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h1>Reporte Kardex de Movimientos de Stock</h1>
            <p class="text-muted">Historial completo de movimientos de stock</p>
        </div>
    </div>

    <!-- Filtro de Producto -->
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('reportes.kardex') }}" method="GET" class="form-inline">
                <div class="form-group mr-3">
                    <label for="id_producto" class="mr-2">Filtrar por Producto:</label>
                    <select name="id_producto" id="id_producto" class="form-control" style="min-width: 300px;">
                        <option value="">-- Todos los productos --</option>
                        @foreach($productos as $producto)
                            <option value="{{ $producto->id_producto }}" {{ $id_producto == $producto->id_producto ? 'selected' : '' }}>
                                {{ $producto->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                @if($id_producto)
                    <a href="{{ route('reportes.kardex') }}" class="btn btn-secondary ml-2">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                @endif
                <a href="{{ route('reportes.index') }}" class="btn btn-secondary ml-2">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </form>
        </div>
    </div>

    <!-- Resumen -->
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-list"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Movimientos</span>
                    <span class="info-box-number">{{ $movimientos->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-arrow-down"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Entradas</span>
                    <span class="info-box-number">{{ $movimientos->where('tipo_movimiento', 'ENTRADA')->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-danger">
                <span class="info-box-icon"><i class="fas fa-arrow-up"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Salidas</span>
                    <span class="info-box-number">{{ $movimientos->where('tipo_movimiento', 'SALIDA')->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-warning">
                <span class="info-box-icon"><i class="fas fa-balance-scale"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Movimiento Neto</span>
                    <span class="info-box-number">
                        {{ $movimientos->where('tipo_movimiento', 'ENTRADA')->sum('cantidad') - $movimientos->where('tipo_movimiento', 'SALIDA')->sum('cantidad') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de movimientos -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-exchange-alt"></i> Historial de Movimientos
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-success" onclick="window.print()">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="bg-light">
                        <tr>
                            <th>Producto</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Cantidad</th>
                            <th>Fecha</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movimientos as $movimiento)
                            <tr>
                                <td><strong>{{ $movimiento->producto }}</strong></td>
                                <td class="text-center">
                                    @if($movimiento->tipo_movimiento === 'ENTRADA')
                                        <span class="badge badge-success">
                                            <i class="fas fa-arrow-down"></i> ENTRADA
                                        </span>
                                    @else
                                        <span class="badge badge-danger">
                                            <i class="fas fa-arrow-up"></i> SALIDA
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <strong>
                                        @if($movimiento->tipo_movimiento === 'ENTRADA')
                                            <span class="text-success">+{{ $movimiento->cantidad }}</span>
                                        @else
                                            <span class="text-danger">-{{ $movimiento->cantidad }}</span>
                                        @endif
                                    </strong>
                                </td>
                                <td>{{ date('d/m/Y H:i', strtotime($movimiento->fecha_movimiento)) }}</td>
                                <td>{{ $movimiento->motivo ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay movimientos registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
