@extends('adminlte::page')

@section('title', 'Gestión de Descuentos')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-percentage"></i>
                Gestión de Descuentos
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Descuentos</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="card-title"><i class="fas fa-list"></i> Lista de Descuentos</h3>
                </div>
                <div class="col-md-6 text-right">
                    <a href="{{ route('descuentos.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Descuento
                    </a>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Filtros -->
            <form method="GET" action="{{ route('descuentos.index') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Buscar descuentos..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <select name="tipo" class="form-control">
                                <option value="">Todos los tipos</option>
                                <option value="PORCENTAJE" {{ request('tipo') === 'PORCENTAJE' ? 'selected' : '' }}>
                                    Porcentaje
                                </option>
                                <option value="MONTO_FIJO" {{ request('tipo') === 'MONTO_FIJO' ? 'selected' : '' }}>
                                    Monto Fijo
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <select name="activo" class="form-control">
                                <option value="">Todos los estados</option>
                                <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activos</option>
                                <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivos</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="{{ route('descuentos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
            
            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th>Aplica a</th>
                            <th>Vigencia</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($descuentos as $descuento)
                            <tr>
                                <td>
                                    <strong>{{ $descuento->nombre }}</strong>
                                    @if($descuento->descripcion)
                                        <br><small class="text-muted">{{ $descuento->descripcion }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $descuento->tipo_descuento === 'PORCENTAJE' ? 'info' : 'success' }}">
                                        {{ $descuento->tipo_descuento === 'PORCENTAJE' ? 'Porcentaje' : 'Monto Fijo' }}
                                    </span>
                                </td>
                                <td>
                                    @if($descuento->tipo_descuento === 'PORCENTAJE')
                                        {{ $descuento->valor_descuento }}%
                                    @else
                                        ${{ number_format($descuento->valor_descuento, 2) }}
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-secondary">
                                        {{ ucwords(str_replace('_', ' ', strtolower($descuento->aplica_a))) }}
                                    </span>
                                    @if($descuento->cliente)
                                        <br><small>{{ $descuento->cliente->nombre }}</small>
                                    @elseif($descuento->producto)
                                        <br><small>{{ $descuento->producto->nombre }}</small>
                                    @elseif($descuento->categoria)
                                        <br><small>{{ $descuento->categoria->nombre }}</small>
                                    @endif
                                </td>
                                <td>
                                    <small>
                                        <strong>Desde:</strong> {{ $descuento->fecha_inicio->format('d/m/Y') }}<br>
                                        @if($descuento->fecha_fin)
                                            <strong>Hasta:</strong> {{ $descuento->fecha_fin->format('d/m/Y') }}
                                            @if($descuento->fecha_fin < now())
                                                <br><span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Vencido</span>
                                            @endif
                                        @else
                                            <strong>Hasta:</strong> Sin límite
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    @if($descuento->activo && $descuento->estaVigente())
                                        <span class="badge badge-success">Activo</span>
                                    @elseif(!$descuento->activo)
                                        <span class="badge badge-danger">Inactivo</span>
                                    @else
                                        <span class="badge badge-warning">Vencido</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('descuentos.show', $descuento) }}" 
                                           class="btn btn-sm btn-info" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('descuentos.edit', $descuento) }}" 
                                           class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('descuentos.toggle', $descuento) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-{{ $descuento->activo ? 'secondary' : 'success' }}"
                                                    title="{{ $descuento->activo ? 'Desactivar' : 'Activar' }}">
                                                <i class="fas fa-{{ $descuento->activo ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>
                                        @if(!$descuento->tieneVentasAsociadas())
                                            <form action="{{ route('descuentos.destroy', $descuento) }}" 
                                                  method="POST" class="d-inline" 
                                                  onsubmit="return confirm('¿Está seguro de eliminar este descuento?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="py-4">
                                        <i class="fas fa-percentage fa-3x text-muted mb-3"></i>
                                        <h5>No hay descuentos registrados</h5>
                                        <p class="text-muted">Comience creando su primer descuento.</p>
                                        <a href="{{ route('descuentos.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Crear Descuento
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            @if($descuentos->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $descuentos->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@stop

@section('css')
    <style>
        .table td {
            vertical-align: middle;
        }
        .btn-group .btn {
            margin-right: 2px;
        }
    </style>
@stop

@section('js')
    <script>
        // Confirmación de cambio de estado
        document.querySelectorAll('form[action*="toggle"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                const button = this.querySelector('button');
                const action = button.title.toLowerCase();
                if (!confirm(`¿Está seguro de ${action} este descuento?`)) {
                    e.preventDefault();
                }
            });
        });
    </script>
@stop