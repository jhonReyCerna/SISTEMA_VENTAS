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

                <form method="GET" class="mb-3">
                    <div class="form-row">
                        <div class="col">
                            <input type="text" name="nombre" class="form-control" placeholder="Nombre" value="{{ request('nombre') }}">
                        </div>
                        <div class="col">
                            <input type="text" name="email" class="form-control" placeholder="Email" value="{{ request('email') }}">
                        </div>
                        <div class="col">
                            <select name="rol" class="form-control">
                                <option value="">Todos los roles</option>
                                @foreach($roles as $rol)
                                    <option value="{{ $rol->id_rol }}" {{ request('rol') == $rol->id_rol ? 'selected' : '' }}>{{ $rol->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <select name="estado" class="form-control">
                                <option value="">Todos los estados</option>
                                <option value="ACTIVO" {{ request('estado') == 'ACTIVO' ? 'selected' : '' }}>Activo</option>
                                <option value="INACTIVO" {{ request('estado') == 'INACTIVO' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Filtrar</button>
                        </div>
                    </div>
                </form>
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
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="pagination-block-custom mb-0 p-0" style="background:transparent;box-shadow:none;">
                        {{-- Links de páginas (números) --}}
                        @php
                            $elements = $usuarios->links()->elements[0] ?? [];
                        @endphp
                        @if($usuarios->lastPage() > 1)
                            <nav class="d-inline-block">
                                <ul class="pagination mb-0">
                                    @foreach ($usuarios->links()->elements[0] as $page => $url)
                                        @if ($page == $usuarios->currentPage())
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
                        {{-- Botones Anterior/Siguiente --}}
                        <nav class="d-inline-block">
                            <ul class="pagination mb-0">
                                {{-- Anterior --}}
                                @if ($usuarios->onFirstPage())
                                    <li class="page-item disabled"><span class="page-link">&laquo; Anterior</span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $usuarios->previousPageUrl() }}" rel="prev">&laquo; Anterior</a></li>
                                @endif
                                {{-- Siguiente --}}
                                @if ($usuarios->hasMorePages())
                                    <li class="page-item"><a class="page-link" href="{{ $usuarios->nextPageUrl() }}" rel="next">Siguiente &raquo;</a></li>
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
