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
                <div class="d-flex justify-content-center mt-4">
                    {{ $productos->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
