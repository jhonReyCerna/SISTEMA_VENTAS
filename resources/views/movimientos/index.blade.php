@extends('layouts.admin')

@section('title', 'Movimientos de Stock')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Movimientos de Stock</h1>
                <a href="{{ route('movimientos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Movimiento
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                            <th>Fecha</th>
                            <th>Motivo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movimientos as $movimiento)
                            <tr>
                                <td>{{ $movimiento->id_movimiento }}</td>
                                <td>{{ $movimiento->producto->nombre }}</td>
                                <td>
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
                                <td>
                                    <strong>{{ $movimiento->cantidad }}</strong>
                                </td>
                                <td>{{ $movimiento->fecha_movimiento->format('d/m/Y H:i') }}</td>
                                <td>{{ $movimiento->motivo ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('movimientos.show', $movimiento->id_movimiento) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay movimientos de stock registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $movimientos->links() }}
        </div>
    </div>
</div>
@endsection
