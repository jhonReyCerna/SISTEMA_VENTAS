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
            <form method="GET" class="mb-3">
                <div class="form-row">
                    <div class="col">
                        <input type="text" name="producto" class="form-control" placeholder="Producto" value="{{ request('producto') }}">
                    </div>
                    <div class="col">
                        <select name="tipo" class="form-control">
                            <option value="">Tipo</option>
                            <option value="ENTRADA" {{ request('tipo') == 'ENTRADA' ? 'selected' : '' }}>ENTRADA</option>
                            <option value="SALIDA" {{ request('tipo') == 'SALIDA' ? 'selected' : '' }}>SALIDA</option>
                        </select>
                    </div>
                    <div class="col">
                        <input type="date" name="fecha" class="form-control" value="{{ request('fecha') }}">
                    </div>
                    <div class="col">
                        <input type="text" name="motivo" class="form-control" placeholder="Motivo" value="{{ request('motivo') }}">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Filtrar</button>
                    </div>
                </div>
            </form>
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
        <!-- Pagination -->
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center mt-2">
                <div class="pagination-block-custom mb-0 p-0" style="background:transparent;box-shadow:none;">
                    @php
                        $elements = $movimientos->links()->elements[0] ?? [];
                    @endphp
                    @if($movimientos->lastPage() > 1)
                        <nav class="d-inline-block">
                            <ul class="pagination mb-0">
                                @foreach ($movimientos->links()->elements[0] as $page => $url)
                                    @if ($page == $movimientos->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach
                            </ul>
                        </nav>
                    @endif
                </div>
                <div class="pagination-block-custom mb-0 p-0 text-right" style="background:transparent;box-shadow:none;">
                    <nav class="d-inline-block">
                        <ul class="pagination mb-0">
                            @if ($movimientos->onFirstPage())
                                <li class="page-item disabled"><span class="page-link">&laquo; Anterior</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $movimientos->previousPageUrl() }}" rel="prev">&laquo; Anterior</a></li>
                            @endif
                            @if ($movimientos->hasMorePages())
                                <li class="page-item"><a class="page-link" href="{{ $movimientos->nextPageUrl() }}" rel="next">Siguiente &raquo;</a></li>
                            @else
                                <li class="page-item disabled"><span class="page-link">Siguiente &raquo;</span></li>
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
