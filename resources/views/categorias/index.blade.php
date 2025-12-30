@extends('layouts.admin')

@section('title', 'Categorías')
@section('page_title', 'Categorías')
@section('breadcrumb')
    <li class="breadcrumb-item active">Categorías</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Gestión de Categorías</h3>
                <div class="card-tools">
                    <a href="{{ route('categorias.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nueva Categoría
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
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categorias as $categoria)
                            <tr>
                                <td>{{ $categoria->id_categoria }}</td>
                                <td><strong>{{ $categoria->nombre }}</strong></td>
                                <td>{{ $categoria->descripcion ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ $categoria->estado === 'ACTIVO' ? 'badge-success' : 'badge-danger' }}">
                                        {{ $categoria->estado }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('categorias.edit', $categoria->id_categoria) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('categorias.destroy', $categoria->id_categoria) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro?');">
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
                                <td colspan="5" class="text-center">No hay categorías registradas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $categorias->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
