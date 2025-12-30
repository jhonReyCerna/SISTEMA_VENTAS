@extends('layouts.admin')

@section('title', 'Usuarios')
@section('page_title', 'Usuarios')
@section('breadcrumb')
    <li class="breadcrumb-item active">Usuarios</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Gestión de Usuarios</h3>
                <div class="card-tools">
                    <a href="{{ route('usuarios.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nuevo Usuario
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
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
                            <tr>
                                <td>{{ $usuario->id_usuario }}</td>
                                <td>{{ $usuario->nombre }}</td>
                                <td>{{ $usuario->email }}</td>
                                <td>
                                    <span class="badge badge-primary">
                                        {{ $usuario->role ? $usuario->role->nombre : 'Sin rol' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $usuario->estado === 'ACTIVO' ? 'badge-success' : 'badge-danger' }}">
                                        {{ $usuario->estado }}
                                    </span>
                                </td>
                                <td>{{ $usuario->fecha_creacion ? $usuario->fecha_creacion->format('d/m/Y H:i') : 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('usuarios.edit', $usuario->id_usuario) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('usuarios.destroy', $usuario->id_usuario) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro?');">
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
                                <td colspan="7" class="text-center">No hay usuarios registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $usuarios->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
