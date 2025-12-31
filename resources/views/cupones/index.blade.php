@extends('adminlte::page')

@section('title', 'Gestión de Cupones')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-ticket-alt"></i>
                Gestión de Cupones
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Cupones</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="card-title"><i class="fas fa-list"></i> Lista de Cupones</h3>
                </div>
                <div class="col-md-6 text-right">
                    <div class="btn-group">
                        <a href="{{ route('cupones.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuevo Cupón
                        </a>
                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modalGenerarLote">
                            <i class="fas fa-layer-group"></i> Generar Lote
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Filtros -->
            <form method="GET" action="{{ route('cupones.index') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Buscar por código o nombre..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="tipo" class="form-control">
                            <option value="">Todos los tipos</option>
                            <option value="PORCENTAJE" {{ request('tipo') === 'PORCENTAJE' ? 'selected' : '' }}>
                                Porcentaje
                            </option>
                            <option value="MONTO_FIJO" {{ request('tipo') === 'MONTO_FIJO' ? 'selected' : '' }}>
                                Monto Fijo
                            </option>
                            <option value="ENVIO_GRATIS" {{ request('tipo') === 'ENVIO_GRATIS' ? 'selected' : '' }}>
                                Envío Gratis
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="estado" class="form-control">
                            <option value="">Todos los estados</option>
                            <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activos</option>
                            <option value="vencido" {{ request('estado') === 'vencido' ? 'selected' : '' }}>Vencidos</option>
                            <option value="agotado" {{ request('estado') === 'agotado' ? 'selected' : '' }}>Agotados</option>
                            <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="{{ route('cupones.index') }}" class="btn btn-secondary">
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
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Tipo/Valor</th>
                            <th>Vigencia</th>
                            <th>Uso</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cupones as $cupon)
                            <tr>
                                <td>
                                    <strong class="text-primary">{{ $cupon->codigo }}</strong>
                                    @if($cupon->nombre)
                                        <br><small class="text-muted">{{ $cupon->nombre }}</small>
                                    @endif
                                </td>
                                <td>
                                    {{ $cupon->nombre }}
                                    @if($cupon->descripcion)
                                        <br><small class="text-muted">{{ $cupon->descripcion }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $cupon->getColorTipo() }}">
                                        {{ $cupon->getTipoTexto() }}
                                    </span>
                                    <br>
                                    @if($cupon->tipo_cupon !== 'ENVIO_GRATIS')
                                        <strong>
                                            @if($cupon->tipo_cupon === 'PORCENTAJE')
                                                {{ $cupon->valor_descuento }}%
                                            @else
                                                ${{ number_format($cupon->valor_descuento, 2) }}
                                            @endif
                                        </strong>
                                    @endif
                                    @if($cupon->monto_minimo_compra)
                                        <br><small>Mín: ${{ number_format($cupon->monto_minimo_compra, 2) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <small>
                                        <strong>Desde:</strong> {{ $cupon->fecha_inicio->format('d/m/Y') }}<br>
                                        @if($cupon->fecha_fin)
                                            <strong>Hasta:</strong> {{ $cupon->fecha_fin->format('d/m/Y') }}
                                        @else
                                            <strong>Hasta:</strong> Sin límite
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    <div class="text-center">
                                        <strong>{{ $cupon->veces_usado }}</strong>
                                        @if($cupon->limite_uso)
                                            / {{ $cupon->limite_uso }}
                                        @else
                                            / ∞
                                        @endif
                                        <br>
                                        @if($cupon->limite_uso)
                                            @php
                                                $porcentaje = ($cupon->veces_usado / $cupon->limite_uso) * 100;
                                                $colorBarra = $porcentaje >= 90 ? 'danger' : ($porcentaje >= 70 ? 'warning' : 'success');
                                            @endphp
                                            <div class="progress" style="height: 4px;">
                                                <div class="progress-bar bg-{{ $colorBarra }}" 
                                                     style="width: {{ min($porcentaje, 100) }}%"></div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($cupon->estaActivo())
                                        <span class="badge badge-success">Activo</span>
                                    @elseif($cupon->estaVencido())
                                        <span class="badge badge-warning">Vencido</span>
                                    @elseif($cupon->estaAgotado())
                                        <span class="badge badge-danger">Agotado</span>
                                    @else
                                        <span class="badge badge-secondary">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('cupones.show', $cupon) }}" 
                                           class="btn btn-sm btn-info" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('cupones.edit', $cupon) }}" 
                                           class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('cupones.toggle', $cupon) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-{{ $cupon->activo ? 'secondary' : 'success' }}"
                                                    title="{{ $cupon->activo ? 'Desactivar' : 'Activar' }}">
                                                <i class="fas fa-{{ $cupon->activo ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>
                                        @if($cupon->veces_usado == 0)
                                            <form action="{{ route('cupones.destroy', $cupon) }}" 
                                                  method="POST" class="d-inline" 
                                                  onsubmit="return confirm('¿Está seguro de eliminar este cupón?')">
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
                                        <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                                        <h5>No hay cupones registrados</h5>
                                        <p class="text-muted">Comience creando su primer cupón promocional.</p>
                                        <a href="{{ route('cupones.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Crear Cupón
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            @if($cupones->hasPages())
                <div class="pagination-block-custom mt-4">
                    {{ $cupones->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
    
    <!-- Modal para generar lote de cupones -->
    <div class="modal fade" id="modalGenerarLote" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="{{ route('cupones.generar') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-layer-group"></i> Generar Lote de Cupones
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="cantidad">Cantidad de cupones *</label>
                            <input type="number" name="cantidad" id="cantidad" class="form-control" 
                                   min="1" max="100" required>
                            <small class="form-text text-muted">Máximo 100 cupones por lote</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="tipo_cupon">Tipo de cupón *</label>
                            <select name="tipo_cupon" id="tipo_cupon" class="form-control" required>
                                <option value="PORCENTAJE">Descuento por porcentaje</option>
                                <option value="MONTO_FIJO">Descuento monto fijo</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="valor_descuento">Valor del descuento *</label>
                            <div class="input-group">
                                <input type="number" name="valor_descuento" id="valor_descuento" 
                                       class="form-control" step="0.01" min="0" required>
                                <div class="input-group-append">
                                    <span class="input-group-text" id="simbolo-descuento">%</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_inicio">Fecha inicio *</label>
                                    <input type="date" name="fecha_inicio" id="fecha_inicio" 
                                           class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_fin">Fecha fin</label>
                                    <input type="date" name="fecha_fin" id="fecha_fin" class="form-control">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="limite_uso">Límite de uso por cupón</label>
                                    <input type="number" name="limite_uso" id="limite_uso" 
                                           class="form-control" min="1">
                                    <small class="form-text text-muted">Opcional. Dejar vacío para uso ilimitado</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="monto_minimo_compra">Compra mínima</label>
                                    <input type="number" name="monto_minimo_compra" id="monto_minimo_compra" 
                                           class="form-control" step="0.01" min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-magic"></i> Generar Cupones
                        </button>
                    </div>
                </div>
            </form>
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
        .progress {
            margin-top: 2px;
        }
    </style>
@stop

@section('js')
    <script>
        // Cambiar símbolo según el tipo de cupón
        document.getElementById('tipo_cupon').addEventListener('change', function() {
            const simbolo = document.getElementById('simbolo-descuento');
            simbolo.textContent = this.value === 'PORCENTAJE' ? '%' : '$';
        });
        
        // Confirmación de cambio de estado
        document.querySelectorAll('form[action*="toggle"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                const button = this.querySelector('button');
                const action = button.title.toLowerCase();
                if (!confirm(`¿Está seguro de ${action} este cupón?`)) {
                    e.preventDefault();
                }
            });
        });
    </script>
@stop