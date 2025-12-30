@extends('layouts.admin')

@section('title', 'Reportes')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h1>Reportes del Sistema</h1>
            <p class="text-muted">Seleccione el tipo de reporte que desea generar</p>
        </div>
    </div>

    <div class="row">
        <!-- Reporte 1: Ventas por Fecha -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-calendar-alt fa-3x text-primary"></i>
                    </div>
                    <h5 class="card-title text-center">Ventas por Rango de Fechas</h5>
                    <p class="card-text text-muted">
                        Analiza el comportamiento de ventas en un periodo determinado. Visualiza todas las transacciones con detalles de cliente, vendedor y montos.
                    </p>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('reportes.ventas-por-fecha') }}" class="btn btn-primary">
                        <i class="fas fa-chart-line"></i> Ver Reporte
                    </a>
                </div>
            </div>
        </div>

        <!-- Reporte 2: Ventas por Usuario -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-user-tie fa-3x text-success"></i>
                    </div>
                    <h5 class="card-title text-center">Ventas por Vendedor</h5>
                    <p class="card-text text-muted">
                        Indicador de productividad del personal. Muestra el desempeño de cada vendedor con cantidad de ventas y montos totales.
                    </p>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('reportes.ventas-por-usuario') }}" class="btn btn-success">
                        <i class="fas fa-chart-bar"></i> Ver Reporte
                    </a>
                </div>
            </div>
        </div>

        <!-- Reporte 3: Productos Más Vendidos -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-trophy fa-3x text-warning"></i>
                    </div>
                    <h5 class="card-title text-center">Productos Más Vendidos</h5>
                    <p class="card-text text-muted">
                        Sirve para toma de decisiones en reposición de stock. Identifica los productos con mayor rotación y generación de ingresos.
                    </p>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('reportes.productos-mas-vendidos') }}" class="btn btn-warning">
                        <i class="fas fa-star"></i> Ver Reporte
                    </a>
                </div>
            </div>
        </div>

        <!-- Reporte 4: Stock y Alertas -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger"></i>
                    </div>
                    <h5 class="card-title text-center">Stock Actual y Alertas</h5>
                    <p class="card-text text-muted">
                        Control preventivo de inventario. Detecta productos con stock bajo o crítico que requieren reabastecimiento urgente.
                    </p>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('reportes.stock-alertas') }}" class="btn btn-danger">
                        <i class="fas fa-boxes"></i> Ver Reporte
                    </a>
                </div>
            </div>
        </div>

        <!-- Reporte 5: Kardex -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-exchange-alt fa-3x text-info"></i>
                    </div>
                    <h5 class="card-title text-center">Kardex de Movimientos</h5>
                    <p class="card-text text-muted">
                        Historial completo de movimientos de stock. Rastrea entradas y salidas de productos con fechas y motivos.
                    </p>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('reportes.kardex') }}" class="btn btn-info">
                        <i class="fas fa-list-alt"></i> Ver Reporte
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
