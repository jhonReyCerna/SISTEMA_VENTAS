@extends('layouts.admin')

@section('title', 'Ver Producto')
@section('page_title', 'Detalles del Producto')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
    <li class="breadcrumb-item active">{{ $producto->nombre }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cube mr-2"></i>Información del Producto
                </h3>
                <div class="card-tools">
                    <span class="badge badge-{{ $producto->estado === 'ACTIVO' ? 'success' : 'danger' }}">
                        {{ $producto->estado }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5><i class="fas fa-info-circle text-info"></i> Información General</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Nombre:</strong></td>
                                <td>{{ $producto->nombre }}</td>
                            </tr>
                            <tr>
                                <td><strong>Categoría:</strong></td>
                                <td>
                                    <span class="badge badge-primary">
                                        {{ $producto->categoria->nombre ?? 'Sin categoría' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Descripción:</strong></td>
                                <td>{{ $producto->descripcion ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-dollar-sign text-success"></i> Precios e Inventario</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Precio Compra:</strong></td>
                                <td>S/ {{ number_format($producto->precio_compra ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Precio Venta:</strong></td>
                                <td><strong class="text-success">S/ {{ number_format($producto->precio_venta ?? 0, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td><strong>Stock Actual:</strong></td>
                                <td>
                                    <span class="badge badge-{{ $producto->stock <= ($producto->stock_minimo ?? 0) ? 'warning' : 'success' }} font-weight-bold">
                                        {{ $producto->stock }} unidades
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Stock Mínimo:</strong></td>
                                <td>{{ $producto->stock_minimo ?? 0 }} unidades</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($producto->stock <= ($producto->stock_minimo ?? 0) && $producto->stock_minimo > 0)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>¡Atención!</strong> Este producto tiene stock bajo. Se recomienda reabastecer pronto.
                </div>
                @endif

                <!-- Estadísticas de margen -->
                @if($producto->precio_compra && $producto->precio_venta)
                <div class="row">
                    <div class="col-12">
                        <h5><i class="fas fa-chart-line text-warning"></i> Análisis de Rentabilidad</h5>
                        @php
                            $margen = $producto->precio_venta - $producto->precio_compra;
                            $porcentajeMargen = $producto->precio_compra > 0 ? ($margen / $producto->precio_compra) * 100 : 0;
                        @endphp
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success">
                                        <i class="fas fa-money-bill"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Margen por Unidad</span>
                                        <span class="info-box-number">S/ {{ number_format($margen, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info">
                                        <i class="fas fa-percentage"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">% Margen</span>
                                        <span class="info-box-number">{{ number_format($porcentajeMargen, 1) }}%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning">
                                        <i class="fas fa-calculator"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Valor Total Stock</span>
                                        <span class="info-box-number">S/ {{ number_format($producto->precio_venta * $producto->stock, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                @if(in_array(Auth::user()->role->nombre, ['Admin', 'Supervisor']))
                <a href="{{ route('productos.edit', $producto->id_producto) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Editar
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection