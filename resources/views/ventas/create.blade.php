@extends('layouts.admin')

@section('title', 'Nueva Venta')
@section('page_title', 'Nueva Venta')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
    <li class="breadcrumb-item active">Crear</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Registrar Nueva Venta</h3>
            </div>
            <form action="{{ route('ventas.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_cliente">Cliente</label>
                                <select class="form-control @error('id_cliente') is-invalid @enderror" id="id_cliente" name="id_cliente" required>
                                    <option value="">Selecciona un cliente</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id_cliente }}" {{ old('id_cliente') == $cliente->id_cliente ? 'selected' : '' }}>
                                            {{ $cliente->nombre }} - {{ $cliente->numero_documento }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_cliente')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo_comprobante">Tipo Comprobante</label>
                                <select class="form-control @error('tipo_comprobante') is-invalid @enderror" id="tipo_comprobante" name="tipo_comprobante" required>
                                    <option value="">Selecciona tipo</option>
                                    <option value="BOLETA" {{ old('tipo_comprobante') == 'BOLETA' ? 'selected' : '' }}>BOLETA</option>
                                    <option value="FACTURA" {{ old('tipo_comprobante') == 'FACTURA' ? 'selected' : '' }}>FACTURA</option>
                                </select>
                                @error('tipo_comprobante')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="total">Total (S/)</label>
                        <input type="number" step="0.01" class="form-control @error('total') is-invalid @enderror" id="total" name="total" value="{{ old('total', 0) }}" required>
                        @error('total')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Esta es una versión simplificada del registro de ventas. El total debe calcularse manualmente por ahora.
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Registrar Venta
                    </button>
                    <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
