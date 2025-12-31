@extends('layouts.admin')

@section('title', 'Ver Venta')
@section('page_title', 'Detalle de Venta')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
    <li class="breadcrumb-item active">Venta #{{ $venta->id_venta }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Información de la Venta #{{ $venta->id_venta }}</h3>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Datos del Cliente</h5>
                        <p><strong>Nombre:</strong> {{ $venta->cliente->nombre }}</p>
                        <p><strong>Documento:</strong> {{ $venta->cliente->tipo_documento }} - {{ $venta->cliente->numero_documento }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Datos de la Venta</h5>
                        <p><strong>Fecha:</strong> {{ $venta->fecha_venta->format('d/m/Y H:i:s') }}</p>
                        <p><strong>Usuario:</strong> {{ $venta->usuario->nombre }}</p>
                        <p><strong>Comprobante:</strong> 
                            <span class="badge {{ $venta->tipo_comprobante === 'BOLETA' ? 'badge-info' : 'badge-warning' }}">
                                {{ $venta->tipo_comprobante }}
                            </span>
                        </p>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-12">
                        <h4 class="text-right">Total: <strong class="text-success">S/ {{ number_format($venta->total, 2) }}</strong></h4>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <a href="{{ route('ventas.pdf', $venta) }}" class="btn btn-danger" target="_blank">
                    <i class="fas fa-file-pdf"></i> Descargar PDF
                </a>
                <a href="{{ route('ventas.ticket', $venta) }}" class="btn btn-info" target="_blank">
                    <i class="fas fa-print"></i> Imprimir Ticket
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
