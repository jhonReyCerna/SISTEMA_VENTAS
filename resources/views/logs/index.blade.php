@extends('layouts.admin')

@section('title', 'Logs de Actividad')
@section('page_title', 'Auditoría del Sistema')
@section('breadcrumb')
    <li class="breadcrumb-item active">Logs de Actividad</li>
@endsection

@section('content')
<!-- Statistics Cards -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($stats['total_actividades']) }}</h3>
                <p>Total Actividades</p>
            </div>
            <div class="icon">
                <i class="fas fa-list"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ number_format($stats['actividades_hoy']) }}</h3>
                <p>Actividades Hoy</p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar-day"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['usuarios_activos_hoy'] }}</h3>
                <p>Usuarios Activos Hoy</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stats['modulos_mas_usados']->count() }}</h3>
                <p>Módulos Activos</p>
            </div>
            <div class="icon">
                <i class="fas fa-cube"></i>
            </div>
        </div>
    </div>
</div>

<!-- Most Used Modules Chart -->
@if($stats['modulos_mas_usados']->count() > 0)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-2"></i>Módulos Más Utilizados (Últimos 7 días)
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($stats['modulos_mas_usados'] as $modulo)
                    <div class="col-md-2">
                        <div class="info-box">
                            <span class="info-box-icon bg-primary">
                                <i class="fas fa-{{ $modulo->modulo === 'VENTAS' ? 'shopping-cart' : ($modulo->modulo === 'PRODUCTOS' ? 'cube' : ($modulo->modulo === 'USUARIOS' ? 'users' : 'cog')) }}"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ $modulo->modulo }}</span>
                                <span class="info-box-number">{{ $modulo->count }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Filters and Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter mr-2"></i>Filtros y Acciones
                </h3>
                <div class="card-tools">
                    <div class="btn-group">
                        <a href="{{ route('logs.export', request()->query()) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-download"></i> Exportar CSV
                        </a>
                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#cleanLogsModal">
                            <i class="fas fa-trash"></i> Limpiar Logs
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" id="filterForm">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="usuario">Usuario:</label>
                                <select name="usuario" id="usuario" class="form-control">
                                    <option value="">Todos</option>
                                    @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario->id_usuario }}" {{ request('usuario') == $usuario->id_usuario ? 'selected' : '' }}>
                                        {{ $usuario->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="modulo">Módulo:</label>
                                <select name="modulo" id="modulo" class="form-control">
                                    <option value="">Todos</option>
                                    @foreach($modulos as $modulo)
                                    <option value="{{ $modulo }}" {{ request('modulo') == $modulo ? 'selected' : '' }}>
                                        {{ $modulo }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="accion">Acción:</label>
                                <select name="accion" id="accion" class="form-control">
                                    <option value="">Todas</option>
                                    @foreach($acciones as $accion)
                                    <option value="{{ $accion }}" {{ request('accion') == $accion ? 'selected' : '' }}>
                                        {{ $accion }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="fecha_desde">Desde:</label>
                                <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="fecha_hasta">Hasta:</label>
                                <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="search">Buscar:</label>
                                <input type="text" name="search" id="search" class="form-control" placeholder="IP, descripción..." value="{{ request('search') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                            <a href="{{ route('logs.index') }}" class="btn btn-secondary">
                                <i class="fas fa-refresh"></i> Limpiar Filtros
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Activity Logs Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history mr-2"></i>Registro de Actividades ({{ $logs->total() }} registros)
                </h3>
            </div>
            <div class="card-body table-responsive p-0">
                @if($logs->count() > 0)
                <table class="table table-striped table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Fecha/Hora</th>
                            <th>Usuario</th>
                            <th>Módulo</th>
                            <th>Acción</th>
                            <th>Descripción</th>
                            <th>IP</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr>
                            <td>
                                <strong>{{ $log->fecha_hora->format('d/m/Y') }}</strong><br>
                                <small class="text-muted">{{ $log->fecha_hora->format('H:i:s') }}</small>
                            </td>
                            <td>
                                {{ $log->usuario->nombre ?? 'Usuario Eliminado' }}
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $log->modulo }}</span>
                            </td>
                            <td>
                                @php
                                    $badgeClass = match($log->accion) {
                                        'CREAR' => 'badge-success',
                                        'EDITAR' => 'badge-warning',
                                        'ELIMINAR' => 'badge-danger',
                                        'VER', 'LISTAR' => 'badge-info',
                                        default => 'badge-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $log->accion }}</span>
                            </td>
                            <td class="text-truncate" style="max-width: 300px;" title="{{ $log->descripcion }}">
                                {{ Str::limit($log->descripcion, 50) }}
                            </td>
                            <td>
                                <code>{{ $log->ip }}</code>
                            </td>
                            <td>
                                <a href="{{ route('logs.show', $log->id_log) }}" class="btn btn-info btn-xs" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No hay registros de actividad</h4>
                    <p class="text-muted">Los registros de actividad aparecerán aquí cuando los usuarios realicen acciones en el sistema</p>
                </div>
                @endif
            </div>
            @if($logs->hasPages())
            <div class="card-footer">
                {{ $logs->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Clean Logs Modal -->
<div class="modal fade" id="cleanLogsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fas fa-trash mr-2"></i>Limpiar Logs de Actividad
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('logs.clean') }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>¡Atención!</strong> Esta acción eliminará permanentemente los logs de actividad anteriores al período seleccionado.
                    </div>
                    <div class="form-group">
                        <label for="days">Eliminar logs anteriores a:</label>
                        <select name="days" id="days" class="form-control" required>
                            <option value="30">30 días</option>
                            <option value="60">60 días</option>
                            <option value="90">90 días (3 meses)</option>
                            <option value="180">180 días (6 meses)</option>
                            <option value="365">365 días (1 año)</option>
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        Se recomienda exportar los logs antes de eliminarlos para mantener un respaldo.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Limpiar Logs
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto-submit form when filters change
    $('#usuario, #modulo, #accion, #fecha_desde, #fecha_hasta').change(function() {
        $('#filterForm').submit();
    });
    
    // Search with delay
    let searchTimeout;
    $('#search').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            $('#filterForm').submit();
        }, 500);
    });
});
</script>
@endsection