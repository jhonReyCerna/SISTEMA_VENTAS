@extends('layouts.admin')

@section('title', 'Productos')
@section('page_title', 'Productos')
@section('breadcrumb')
    <li class="breadcrumb-item active">Productos</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Gestión de Productos</h3>
                <div class="card-tools">
                    <a href="{{ route('productos.exportar-excel') }}" class="btn btn-success btn-sm mr-2">
                        <i class="fas fa-file-excel"></i> Exportar Excel
                    </a>
                    <a href="{{ route('productos.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nuevo Producto
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
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Precio Compra</th>
                            <th>Precio Venta</th>
                            <th>Stock</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productos as $producto)
                            <tr>
                                <td>{{ $producto->id_producto }}</td>
                                <td><strong>{{ $producto->nombre }}</strong></td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $producto->categoria ? $producto->categoria->nombre : 'Sin categoría' }}
                                    </span>
                                </td>
                                <td>S/ {{ number_format($producto->precio_compra, 2) }}</td>
                                <td>S/ {{ number_format($producto->precio_venta, 2) }}</td>
                                <td>
                                    <span class="badge {{ $producto->stock > $producto->stock_minimo ? 'badge-success' : 'badge-warning' }}">
                                        {{ $producto->stock }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $producto->estado === 'ACTIVO' ? 'badge-success' : 'badge-danger' }}">
                                        {{ $producto->estado }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('productos.edit', $producto->id_producto) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('productos.destroy', $producto->id_producto) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No hay productos registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>



                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="pagination-block-custom mb-0 p-0" style="background:transparent;box-shadow:none;">
                        @php
                            $elements = $productos->links()->elements[0] ?? [];
                        @endphp
                        @if($productos->lastPage() > 1)
                            <nav class="d-inline-block">
                                <ul class="pagination mb-0">
                                    @foreach ($productos->links()->elements[0] as $page => $url)
                                        @if ($page == $productos->currentPage())
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
                                @if ($productos->onFirstPage())
                                    <li class="page-item disabled"><span class="page-link">&laquo; Anterior</span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $productos->previousPageUrl() }}" rel="prev">&laquo; Anterior</a></li>
                                @endif
                                @if ($productos->hasMorePages())
                                    <li class="page-item"><a class="page-link" href="{{ $productos->nextPageUrl() }}" rel="next">Siguiente &raquo;</a></li>
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
