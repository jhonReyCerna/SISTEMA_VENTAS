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
                <div class="d-flex justify-content-center mt-4">
                    {{ $clientes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
