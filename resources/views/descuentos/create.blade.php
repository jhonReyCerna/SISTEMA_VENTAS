@extends('adminlte::page')

@section('title', 'Crear Descuento')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-percentage"></i>
                Crear Descuento
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('descuentos.index') }}">Descuentos</a></li>
                <li class="breadcrumb-item active">Crear</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <form action="{{ route('descuentos.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Información del Descuento</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nombre">Nombre del descuento *</label>
                            <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                                   value="{{ old('nombre') }}" required maxlength="100">
                            @error('nombre')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea name="descripcion" id="descripcion" class="form-control" rows="3" 
                                      maxlength="500">{{ old('descripcion') }}</textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tipo_descuento">Tipo de descuento *</label>
                                    <select name="tipo_descuento" id="tipo_descuento" class="form-control @error('tipo_descuento') is-invalid @enderror" required>
                                        <option value="">Seleccione un tipo</option>
                                        <option value="PORCENTAJE" {{ old('tipo_descuento') === 'PORCENTAJE' ? 'selected' : '' }}>
                                            Porcentaje (%)
                                        </option>
                                        <option value="MONTO_FIJO" {{ old('tipo_descuento') === 'MONTO_FIJO' ? 'selected' : '' }}>
                                            Monto fijo ($)
                                        </option>
                                    </select>
                                    @error('tipo_descuento')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="valor_descuento">Valor del descuento *</label>
                                    <div class="input-group">
                                        <input type="number" name="valor_descuento" id="valor_descuento" 
                                               class="form-control @error('valor_descuento') is-invalid @enderror" 
                                               value="{{ old('valor_descuento') }}" step="0.01" min="0" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="simbolo-descuento">-</span>
                                        </div>
                                    </div>
                                    @error('valor_descuento')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="aplica_a">¿A qué aplica el descuento? *</label>
                            <select name="aplica_a" id="aplica_a" class="form-control @error('aplica_a') is-invalid @enderror" required>
                                <option value="">Seleccione una opción</option>
                                <option value="GENERAL" {{ old('aplica_a') === 'GENERAL' ? 'selected' : '' }}>
                                    General (todas las ventas)
                                </option>
                                <option value="CLIENTE" {{ old('aplica_a') === 'CLIENTE' ? 'selected' : '' }}>
                                    Cliente específico
                                </option>
                                <option value="PRODUCTO" {{ old('aplica_a') === 'PRODUCTO' ? 'selected' : '' }}>
                                    Producto específico
                                </option>
                                <option value="CATEGORIA" {{ old('aplica_a') === 'CATEGORIA' ? 'selected' : '' }}>
                                    Categoría específica
                                </option>
                            </select>
                            @error('aplica_a')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <!-- Cliente específico -->
                        <div class="form-group" id="grupo-cliente" style="display: none;">
                            <label for="cliente_id">Cliente</label>
                            <select name="cliente_id" id="cliente_id" class="form-control @error('cliente_id') is-invalid @enderror">
                                <option value="">Seleccione un cliente</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id_cliente }}" {{ old('cliente_id') == $cliente->id_cliente ? 'selected' : '' }}>
                                        {{ $cliente->nombre }} ({{ $cliente->cedula }})
                                    </option>
                                @endforeach
                            </select>
                            @error('cliente_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <!-- Producto específico -->
                        <div class="form-group" id="grupo-producto" style="display: none;">
                            <label for="producto_id">Producto</label>
                            <select name="producto_id" id="producto_id" class="form-control @error('producto_id') is-invalid @enderror">
                                <option value="">Seleccione un producto</option>
                                @foreach($productos as $producto)
                                    <option value="{{ $producto->id_producto }}" {{ old('producto_id') == $producto->id_producto ? 'selected' : '' }}>
                                        {{ $producto->nombre }} - ${{ number_format($producto->precio_venta, 2) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('producto_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <!-- Categoría específica -->
                        <div class="form-group" id="grupo-categoria" style="display: none;">
                            <label for="categoria_id">Categoría</label>
                            <select name="categoria_id" id="categoria_id" class="form-control @error('categoria_id') is-invalid @enderror">
                                <option value="">Seleccione una categoría</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id_categoria }}" {{ old('categoria_id') == $categoria->id_categoria ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categoria_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-cog"></i> Configuración</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="fecha_inicio">Fecha de inicio *</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" 
                                   class="form-control @error('fecha_inicio') is-invalid @enderror" 
                                   value="{{ old('fecha_inicio', date('Y-m-d')) }}" required>
                            @error('fecha_inicio')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="fecha_fin">Fecha de fin</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" 
                                   class="form-control @error('fecha_fin') is-invalid @enderror" 
                                   value="{{ old('fecha_fin') }}">
                            <small class="form-text text-muted">Opcional. Dejar vacío para descuento sin fecha límite.</small>
                            @error('fecha_fin')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="cantidad_minima">Cantidad mínima</label>
                            <input type="number" name="cantidad_minima" id="cantidad_minima" 
                                   class="form-control @error('cantidad_minima') is-invalid @enderror" 
                                   value="{{ old('cantidad_minima') }}" min="1">
                            <small class="form-text text-muted">Cantidad mínima de productos para aplicar el descuento.</small>
                            @error('cantidad_minima')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="monto_minimo_compra">Monto mínimo de compra</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" name="monto_minimo_compra" id="monto_minimo_compra" 
                                       class="form-control @error('monto_minimo_compra') is-invalid @enderror" 
                                       value="{{ old('monto_minimo_compra') }}" step="0.01" min="0">
                            </div>
                            <small class="form-text text-muted">Monto mínimo de la venta para aplicar el descuento.</small>
                            @error('monto_minimo_compra')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="activo" name="activo" value="1" 
                                       {{ old('activo', '1') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="activo">Descuento activo</label>
                            </div>
                            <small class="form-text text-muted">Solo los descuentos activos se aplicarán en las ventas.</small>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save"></i> Guardar Descuento
                        </button>
                        <a href="{{ route('descuentos.index') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@section('css')
    <style>
        .card-body .form-group:last-child {
            margin-bottom: 0;
        }
    </style>
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipoDescuento = document.getElementById('tipo_descuento');
            const simboloDescuento = document.getElementById('simbolo-descuento');
            const valorDescuento = document.getElementById('valor_descuento');
            const aplicaA = document.getElementById('aplica_a');
            const grupoCliente = document.getElementById('grupo-cliente');
            const grupoProducto = document.getElementById('grupo-producto');
            const grupoCategoria = document.getElementById('grupo-categoria');
            
            // Cambiar símbolo según el tipo de descuento
            tipoDescuento.addEventListener('change', function() {
                if (this.value === 'PORCENTAJE') {
                    simboloDescuento.textContent = '%';
                    valorDescuento.max = '100';
                } else if (this.value === 'MONTO_FIJO') {
                    simboloDescuento.textContent = '$';
                    valorDescuento.removeAttribute('max');
                } else {
                    simboloDescuento.textContent = '-';
                    valorDescuento.removeAttribute('max');
                }
            });
            
            // Mostrar/ocultar campos según aplica a
            aplicaA.addEventListener('change', function() {
                // Ocultar todos los grupos
                grupoCliente.style.display = 'none';
                grupoProducto.style.display = 'none';
                grupoCategoria.style.display = 'none';
                
                // Mostrar el grupo correspondiente
                switch (this.value) {
                    case 'CLIENTE':
                        grupoCliente.style.display = 'block';
                        break;
                    case 'PRODUCTO':
                        grupoProducto.style.display = 'block';
                        break;
                    case 'CATEGORIA':
                        grupoCategoria.style.display = 'block';
                        break;
                }
            });
            
            // Ejecutar al cargar si hay valores previos
            tipoDescuento.dispatchEvent(new Event('change'));
            aplicaA.dispatchEvent(new Event('change'));
            
            // Validación de fechas
            const fechaInicio = document.getElementById('fecha_inicio');
            const fechaFin = document.getElementById('fecha_fin');
            
            fechaInicio.addEventListener('change', function() {
                fechaFin.min = this.value;
            });
            
            // Configurar min inicial
            fechaFin.min = fechaInicio.value;
        });
    </script>
@stop