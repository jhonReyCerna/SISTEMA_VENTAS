@extends('layouts.admin')

@section('title', 'Detalle del Movimiento')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h1>Detalle del Movimiento #{{ $movimiento->id_movimiento }}</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Información del Movimiento</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">ID Movimiento:</th>
                            <td>{{ $movimiento->id_movimiento }}</td>
                        </tr>
                        <tr>
                            <th>Tipo de Movimiento:</th>
                            <td>
                                @if($movimiento->tipo_movimiento === 'ENTRADA')
                                    <span class="badge badge-success badge-lg">
                                        <i class="fas fa-arrow-down"></i> ENTRADA
                                    </span>
                                @else
                                    <span class="badge badge-danger badge-lg">
                                        <i class="fas fa-arrow-up"></i> SALIDA
                                    </span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Cantidad:</th>
                            <td><strong class="h4">{{ $movimiento->cantidad }}</strong></td>
                        </tr>
                        <tr>
                            <th>Fecha del Movimiento:</th>
                            <td>{{ $movimiento->fecha_movimiento->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Motivo:</th>
                            <td>{{ $movimiento->motivo ?? '-' }}</td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <h5>Información del Producto</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">ID Producto:</th>
                            <td>{{ $movimiento->producto->id_producto }}</td>
                        </tr>
                        <tr>
                            <th>Nombre:</th>
                            <td><strong>{{ $movimiento->producto->nombre }}</strong></td>
                        </tr>
                        <tr>
                            <th>Categoría:</th>
                            <td>{{ $movimiento->producto->categoria->nombre ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Stock Actual:</th>
                            <td>
                                @if($movimiento->producto->stock <= $movimiento->producto->stock_minimo)
                                    <span class="badge badge-danger">{{ $movimiento->producto->stock }}</span>
                                    <small class="text-danger">(Stock bajo)</small>
                                @else
                                    <span class="badge badge-success">{{ $movimiento->producto->stock }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                @if($movimiento->producto->estado === 'ACTIVO')
                                    <span class="badge badge-success">ACTIVO</span>
                                @else
                                    <span class="badge badge-secondary">INACTIVO</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mt-3">
                <a href="{{ route('movimientos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver al Listado
                </a>
                <a href="{{ route('productos.show', $movimiento->producto->id_producto) }}" class="btn btn-info">
                    <i class="fas fa-box"></i> Ver Producto
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
