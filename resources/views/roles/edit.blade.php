@extends('layouts.admin')

@section('title', 'Editar Rol')
@section('page_title', 'Editar Rol')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header with-border">
                <h3 class="card-title">Editar Rol: {{ $role->nombre }}</h3>
            </div>
            <form action="{{ route('roles.update', $role->id_rol) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="nombre">Nombre del Rol</label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                               id="nombre" name="nombre" placeholder="Ej: Gerente" 
                               value="{{ old('nombre', $role->nombre) }}" required>
                        @error('nombre')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                  id="descripcion" name="descripcion" rows="4" 
                                  placeholder="Describe el propósito de este rol...">{{ old('descripcion', $role->descripcion) }}</textarea>
                        @error('descripcion')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
