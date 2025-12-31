@extends('layouts.admin')

@section('title', 'Roles')
@section('page_title', 'Roles')
@section('breadcrumb')
    <li class="breadcrumb-item active">Roles</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header with-border">
                <h3 class="card-title">Gestión de Roles</h3>
                <div class="card-tools pull-right">
                    <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nuevo Rol
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" class="mb-3" style="max-width:350px;">
                    <div class="input-group">
                        <input type="text" name="buscar" class="form-control" placeholder="Buscar nombre o descripción" value="{{ request('buscar') }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Buscar</button>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                                <tr>
                                    <td>{{ $role->id_rol }}</td>
                                    <td><strong>{{ $role->nombre }}</strong></td>
                                    <td>{{ $role->descripcion }}</td>
                                    <td>
                                        <a href="{{ route('roles.edit', $role->id_rol) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <form action="{{ route('roles.destroy', $role->id_rol) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro?')">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No hay roles registrados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="pagination-block-custom mb-0 p-0" style="background:transparent;box-shadow:none;">
                        @php
                            $elements = $roles->toArray()['links'] ?? [];
                        @endphp
                        @if($roles->lastPage() > 1)
                            <nav class="d-inline-block">
                                <ul class="pagination mb-0">
                                    @foreach ($roles->getUrlRange(1, $roles->lastPage()) as $page => $url)
                                        @if ($page == $roles->currentPage())
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
                                @if ($roles->onFirstPage())
                                    <li class="page-item disabled"><span class="page-link">&laquo; Anterior</span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $roles->previousPageUrl() }}" rel="prev">&laquo; Anterior</a></li>
                                @endif
                                @if ($roles->hasMorePages())
                                    <li class="page-item"><a class="page-link" href="{{ $roles->nextPageUrl() }}" rel="next">Siguiente &raquo;</a></li>
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
