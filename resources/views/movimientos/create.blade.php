@extends('layouts.admin')

@section('title', 'Registrar Movimiento de Stock')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h1>Registrar Movimiento de Stock</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('movimientos.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_producto">Producto <span class="text-danger">*</span></label>
                            <select name="id_producto" id="id_producto" class="form-control @error('id_producto') is-invalid @enderror" required>
                                <option value="">Seleccione un producto</option>
                                @foreach($productos as $producto)
                                    <option value="{{ $producto->id_producto }}" {{ old('id_producto') == $producto->id_producto ? 'selected' : '' }}>
                                        {{ $producto->nombre }} (Stock actual: {{ $producto->stock }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_producto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tipo_movimiento">Tipo de Movimiento <span class="text-danger">*</span></label>
                            <select name="tipo_movimiento" id="tipo_movimiento" class="form-control @error('tipo_movimiento') is-invalid @enderror" required>
                                <option value="">Seleccione tipo</option>
                                <option value="ENTRADA" {{ old('tipo_movimiento') == 'ENTRADA' ? 'selected' : '' }}>
                                    ENTRADA (Aumenta stock)
                                </option>
                                <option value="SALIDA" {{ old('tipo_movimiento') == 'SALIDA' ? 'selected' : '' }}>
                                    SALIDA (Reduce stock)
                                </option>
                            </select>
                            @error('tipo_movimiento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cantidad">Cantidad <span class="text-danger">*</span></label>
                            <input type="number" name="cantidad" id="cantidad" class="form-control @error('cantidad') is-invalid @enderror" value="{{ old('cantidad') }}" min="1" required>
                            @error('cantidad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="motivo">Motivo</label>
                            <input type="text" name="motivo" id="motivo" class="form-control @error('motivo') is-invalid @enderror" value="{{ old('motivo') }}" maxlength="150" placeholder="Ej: Compra a proveedor, Venta, Devolución, Merma">
                            @error('motivo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Importante:</strong> 
                    <ul class="mb-0 mt-2">
                        <li><strong>ENTRADA:</strong> Aumentará el stock del producto (compras, devoluciones de clientes)</li>
                        <li><strong>SALIDA:</strong> Reducirá el stock del producto (ventas, mermas, daños)</li>
                    </ul>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Registrar Movimiento
                    </button>
                    <a href="{{ route('movimientos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
