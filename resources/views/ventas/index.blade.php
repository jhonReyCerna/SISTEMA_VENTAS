@extends('layouts.admin')

@section('title', 'Ventas')
@section('page_title', 'Ventas')
@section('breadcrumb')
    <li class="breadcrumb-item active">Ventas</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Gestión de Ventas</h3>
                <div class="card-tools">
                    <a href="{{ route('ventas.exportar-excel') }}" class="btn btn-success btn-sm mr-2">
                        <i class="fas fa-file-excel"></i> Exportar Excel
                    </a>
                    <a href="{{ route('ventas.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nueva Venta
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>{{ $message }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Usuario</th>
                            <th>Comprobante</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventas as $venta)
                            <tr>
                                <td>{{ $venta->id_venta }}</td>
                                <td>{{ $venta->fecha_venta->format('d/m/Y H:i') }}</td>
                                <td>{{ $venta->cliente ? $venta->cliente->nombre : 'N/A' }}</td>
                                <td>{{ $venta->usuario ? $venta->usuario->nombre : 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ $venta->tipo_comprobante === 'BOLETA' ? 'badge-info' : 'badge-warning' }}">
                                        {{ $venta->tipo_comprobante }}
                                    </span>
                                </td>
                                <td><strong>S/ {{ number_format($venta->total, 2) }}</strong></td>
                                <td>
                                    <a href="{{ route('ventas.show', $venta->id_venta) }}" class="btn btn-info btn-sm" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('ventas.pdf', $venta->id_venta) }}" class="btn btn-danger btn-sm" target="_blank" title="Descargar PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <a href="{{ route('ventas.ticket', $venta->id_venta) }}" class="btn btn-secondary btn-sm" target="_blank" title="Imprimir ticket">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <form action="{{ route('ventas.destroy', $venta->id_venta) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay ventas registradas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>



                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="pagination-block-custom mb-0 p-0" style="background:transparent;box-shadow:none;">
                        @php
                            $elements = $ventas->links()->elements[0] ?? [];
                        @endphp
                        @if($ventas->lastPage() > 1)
                            <nav class="d-inline-block">
                                <ul class="pagination mb-0">
                                    @foreach ($ventas->links()->elements[0] as $page => $url)
                                        @if ($page == $ventas->currentPage())
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
                                @if ($ventas->onFirstPage())
                                    <li class="page-item disabled"><span class="page-link">&laquo; Anterior</span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $ventas->previousPageUrl() }}" rel="prev">&laquo; Anterior</a></li>
                                @endif
                                @if ($ventas->hasMorePages())
                                    <li class="page-item"><a class="page-link" href="{{ $ventas->nextPageUrl() }}" rel="next">Siguiente &raquo;</a></li>
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
</div>
@endsection
