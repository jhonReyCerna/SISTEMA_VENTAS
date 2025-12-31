@extends('layouts.admin')

@section('title', 'Clientes')
@section('page_title', 'Clientes')
@section('breadcrumb')
    <li class="breadcrumb-item active">Clientes</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Gestión de Clientes</h3>
                <div class="card-tools">
                    <a href="{{ route('clientes.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Nuevo Cliente
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
                            <select name="tipo_documento" class="form-control">
                                <option value="">Tipo Doc.</option>
                                <option value="DNI" {{ request('tipo_documento') == 'DNI' ? 'selected' : '' }}>DNI</option>
                                <option value="RUC" {{ request('tipo_documento') == 'RUC' ? 'selected' : '' }}>RUC</option>
                            </select>
                        </div>
                        <div class="col">
                            <input type="text" name="nombre" class="form-control" placeholder="Nombre" value="{{ request('nombre') }}">
                        </div>
                        <div class="col">
                            <input type="text" name="telefono" class="form-control" placeholder="Teléfono" value="{{ request('telefono') }}">
                        </div>
                        <div class="col">
                            <input type="text" name="numero_documento" class="form-control" placeholder="Nº Documento" value="{{ request('numero_documento') }}">
                        </div>
                        <div class="col">
                            <select name="estado" class="form-control">
                                <option value="">Estado</option>
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
                            <th>Tipo Doc.</th>
                            <th>Nº Documento</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientes as $cliente)
                            <tr>
                                <td>{{ $cliente->id_cliente }}</td>
                                <td>
                                    <span class="badge {{ $cliente->tipo_documento === 'DNI' ? 'badge-info' : 'badge-warning' }}">
                                        {{ $cliente->tipo_documento }}
                                    </span>
                                </td>
                                <td>{{ $cliente->numero_documento }}</td>
                                <td>{{ $cliente->nombre }}</td>
                                <td>{{ $cliente->telefono ?? 'N/A' }}</td>
                                <td>{{ $cliente->email ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ $cliente->estado === 'ACTIVO' ? 'badge-success' : 'badge-danger' }}">
                                        {{ $cliente->estado }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('clientes.edit', $cliente->id_cliente) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('clientes.destroy', $cliente->id_cliente) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro?');">
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
                                <td colspan="8" class="text-center">No hay clientes registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>



                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="pagination-block-custom mb-0 p-0" style="background:transparent;box-shadow:none;">
                        @php
                            $elements = $clientes->links()->elements[0] ?? [];
                        @endphp
                        @if($clientes->lastPage() > 1)
                            <nav class="d-inline-block">
                                <ul class="pagination mb-0">
                                    @foreach ($clientes->links()->elements[0] as $page => $url)
                                        @if ($page == $clientes->currentPage())
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
                                @if ($clientes->onFirstPage())
                                    <li class="page-item disabled"><span class="page-link">&laquo; Anterior</span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $clientes->previousPageUrl() }}" rel="prev">&laquo; Anterior</a></li>
                                @endif
                                @if ($clientes->hasMorePages())
                                    <li class="page-item"><a class="page-link" href="{{ $clientes->nextPageUrl() }}" rel="next">Siguiente &raquo;</a></li>
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
