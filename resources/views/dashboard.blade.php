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

<!-- Comparativa Anual -->
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <h4 class="text-bold">S/ {{ number_format($ventas_anio_actual, 2) }}</h4>
                        <p class="text-muted mb-0">Ventas {{ now()->year }}</p>
                    </div>
                    <div class="col-md-4">
                        <h4 class="text-bold">
                            @if($crecimiento_anual >= 0)
                                <i class="fas fa-arrow-up text-success"></i>
                                <span class="text-success">{{ number_format($crecimiento_anual, 1) }}%</span>
                            @else
                                <i class="fas fa-arrow-down text-danger"></i>
                                <span class="text-danger">{{ number_format($crecimiento_anual, 1) }}%</span>
                            @endif
                        </h4>
                        <p class="text-muted mb-0">Crecimiento vs {{ now()->year - 1 }}</p>
                    </div>
                    <div class="col-md-4">
                        <h4 class="text-bold text-secondary">S/ {{ number_format($ventas_anio_anterior, 2) }}</h4>
                        <p class="text-muted mb-0">Ventas {{ now()->year - 1 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Small boxes (Stat boxes) con animación -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info stat-card">
            <div class="inner">
                <h3><span class="counter" data-target="{{ $total_ventas }}">0</span></h3>
                <p>Ventas Totales</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            @if(in_array(Auth::user()->role->nombre, ['Admin', 'Supervisor', 'Vendedor']))
            <a href="{{ route('ventas.index') }}" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
            @else
            <span class="small-box-footer">Ventas registradas</span>
            @endif
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success stat-card">
            <div class="inner">
                <h3>S/ <span class="counter" data-target="{{ $total_ingresos }}">0</span></h3>
                <p>Ingresos Totales</p>
            </div>
            <div class="icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            @if(in_array(Auth::user()->role->nombre, ['Admin', 'Supervisor']))
            <a href="{{ route('reportes.ventas-por-fecha') }}" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
            @else
            <span class="small-box-footer">Total facturado</span>
            @endif
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning stat-card">
            <div class="inner">
                <h3><span class="counter" data-target="{{ $total_clientes }}">0</span></h3>
                <p>Clientes Activos</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            @if(in_array(Auth::user()->role->nombre, ['Admin', 'Supervisor', 'Vendedor']))
            <a href="{{ route('clientes.index') }}" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
            @else
            <span class="small-box-footer">Clientes registrados</span>
            @endif
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger stat-card">
            <div class="inner">
                <h3><span class="counter" data-target="{{ $total_productos }}">0</span></h3>
                <p>Productos Activos</p>
            </div>
            <div class="icon">
                <i class="fas fa-cube"></i>
            </div>
            @if(in_array(Auth::user()->role->nombre, ['Admin', 'Supervisor']))
            <a href="{{ route('productos.index') }}" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
            @else
            <span class="small-box-footer">Productos disponibles</span>
            @endif
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
    <!-- Gráfico de Ventas por Mes -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-line"></i> Ventas de los Últimos 12 Meses</h3>
            </div>
            <div class="card-body">
                <canvas id="ventasPorMesChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Gráfico de Productos Más Vendidos -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-trophy"></i> Top 5 Productos</h3>
            </div>
            <div class="card-body">
                <canvas id="topProductosChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tablas -->
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

@section('extra_css')
<style>
    .stat-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }
    .counter {
        display: inline-block;
    }
</style>
@endsection

@section('extra_js')
<script>
    // Animación de contadores
    document.addEventListener('DOMContentLoaded', function() {
        const counters = document.querySelectorAll('.counter');
        
        counters.forEach(counter => {
            const target = parseFloat(counter.getAttribute('data-target'));
            const duration = 2000; // 2 segundos
            const step = target / (duration / 16); // 60fps
            let current = 0;
            
            const updateCounter = () => {
                current += step;
                if (current < target) {
                    counter.textContent = Math.floor(current).toLocaleString('es-PE');
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target.toLocaleString('es-PE', {minimumFractionDigits: 0, maximumFractionDigits: 2});
                }
            };
            
            updateCounter();
        });
    });

    // Gráfico de Ventas por Mes
    const ventasCtx = document.getElementById('ventasPorMesChart').getContext('2d');
    const ventasChart = new Chart(ventasCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($meses_labels) !!},
            datasets: [{
                label: 'Ventas (S/)',
                data: {!! json_encode($ventas_por_mes) !!},
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.4,
                fill: true,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'S/ ' + context.parsed.y.toLocaleString('es-PE', {minimumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'S/ ' + value.toLocaleString('es-PE');
                        }
                    }
                }
            }
        }
    });

    // Gráfico de Top Productos (Donut)
    const productosCtx = document.getElementById('topProductosChart').getContext('2d');
    const productosChart = new Chart(productosCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($productos_chart_labels) !!},
            datasets: [{
                label: 'Unidades Vendidas',
                data: {!! json_encode($productos_chart_data) !!},
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + ' unidades';
                        }
                    }
                }
            }
        }
    });

    // Si acabamos de hacer login, limpiar completamente el historial del login
    (function() {
        if (window.history && window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    })();
</script>
@endsection
