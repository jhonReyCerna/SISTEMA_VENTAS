@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<!-- Mensaje de error si no tiene permisos -->
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <h5><i class="icon fas fa-ban"></i> Acceso Denegado</h5>
        {{ session('error') }}
    </div>
@endif

<!-- Small boxes (Stat boxes) -->
<div class="row">
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $total_ventas }}</h3>
                <p>Ventas Totales</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <a href="{{ route('ventas.index') }}" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-success">
            <div class="inner">
                <h3>S/ {{ number_format($total_ingresos, 2) }}</h3>
                <p>Ingresos Totales</p>
            </div>
            <div class="icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <a href="{{ route('reportes.ventas-por-fecha') }}" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $total_clientes }}</h3>
                <p>Clientes Activos</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="{{ route('clientes.index') }}" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $total_productos }}</h3>
                <p>Productos Activos</p>
            </div>
            <div class="icon">
                <i class="fas fa-cube"></i>
            </div>
            <a href="{{ route('productos.index') }}" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<!-- Segunda fila de estadísticas -->
<div class="row">
    <div class="col-lg-4 col-6">
        <div class="info-box bg-gradient-info">
            <span class="info-box-icon"><i class="fas fa-calendar-day"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Ventas de Hoy</span>
                <span class="info-box-number">{{ $ventas_hoy }}</span>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-6">
        <div class="info-box bg-gradient-success">
            <span class="info-box-icon"><i class="fas fa-calendar-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Ingresos del Mes</span>
                <span class="info-box-number">S/ {{ number_format($ventas_mes, 2) }}</span>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-6">
        <div class="info-box {{ $productos_stock_bajo > 0 ? 'bg-gradient-danger' : 'bg-gradient-secondary' }}">
            <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Alertas de Stock</span>
                <span class="info-box-number">{{ $productos_stock_bajo }}</span>
            </div>
        </div>
    </div>
</div>

<!-- /.row -->
<div class="row">
    <!-- Ventas Recientes -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header border-transparent">
                <h3 class="card-title"><i class="fas fa-shopping-cart"></i> Ventas Recientes</h3>
                <div class="card-tools">
                    <a href="{{ route('ventas.index') }}" class="btn btn-sm btn-primary">Ver todas</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm m-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ventas_recientes as $venta)
                                <tr>
                                    <td><a href="{{ route('ventas.show', $venta->id_venta) }}">#{{ $venta->id_venta }}</a></td>
                                    <td>{{ $venta->cliente->nombre }}</td>
                                    <td><span class="badge badge-success">S/ {{ number_format($venta->total, 2) }}</span></td>
                                    <td>{{ $venta->fecha_venta->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No hay ventas registradas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos Más Vendidos -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header border-transparent">
                <h3 class="card-title"><i class="fas fa-trophy"></i> Productos Más Vendidos</h3>
                <div class="card-tools">
                    <a href="{{ route('reportes.productos-mas-vendidos') }}" class="btn btn-sm btn-warning">Ver reporte</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm m-0">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Unidades</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productos_mas_vendidos as $producto)
                                <tr>
                                    <td>{{ $producto->producto }}</td>
                                    <td class="text-center"><span class="badge badge-primary">{{ $producto->cantidad_vendida }}</span></td>
                                    <td class="text-right">S/ {{ number_format($producto->total_generado, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No hay datos disponibles</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tercera fila -->
<div class="row">
    <!-- Mejores Clientes -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header border-transparent">
                <h3 class="card-title"><i class="fas fa-star"></i> Mejores Clientes</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm m-0">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th class="text-center">Compras</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mejores_clientes as $cliente)
                                <tr>
                                    <td>{{ $cliente->nombre }}</td>
                                    <td class="text-center"><span class="badge badge-info">{{ $cliente->total_compras }}</span></td>
                                    <td class="text-right"><strong>S/ {{ number_format($cliente->monto_total, 2) }}</strong></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No hay datos disponibles</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas de Stock -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header border-transparent">
                <h3 class="card-title"><i class="fas fa-exclamation-triangle text-danger"></i> Alertas de Stock Bajo</h3>
                <div class="card-tools">
                    <a href="{{ route('reportes.stock-alertas') }}" class="btn btn-sm btn-danger">Ver todas</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm m-0">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Stock</th>
                                <th class="text-center">Mínimo</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($alertas_stock as $producto)
                                <tr class="text-danger">
                                    <td><strong>{{ $producto->nombre }}</strong></td>
                                    <td class="text-center"><span class="badge badge-danger">{{ $producto->stock }}</span></td>
                                    <td class="text-center">{{ $producto->stock_minimo }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('movimientos.create') }}" class="btn btn-xs btn-primary" title="Reabastecer">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-success">
                                        <i class="fas fa-check-circle"></i> Todos los productos tienen stock adecuado
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra_js')
<script>
    // Si acabamos de hacer login, limpiar completamente el historial del login
    (function() {
        // Reemplazar la entrada actual del historial para que no guarde el login
        if (window.history && window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    })();
</script>
@endsection
