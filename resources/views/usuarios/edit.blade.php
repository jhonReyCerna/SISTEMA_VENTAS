@extends('layouts.admin')

@section('title', 'Editar Usuario')
@section('page_title', 'Editar Usuario')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('usuarios.index') }}">Usuarios</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Editar Usuario</h3>
            </div>
            <form action="{{ route('usuarios.update', $usuario->id_usuario) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $usuario->nombre) }}" required>
                        @error('nombre')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $usuario->email) }}" required>
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="id_rol">Rol</label>
                        <select class="form-control @error('id_rol') is-invalid @enderror" id="id_rol" name="id_rol" required>
                            <option value="">Selecciona un rol</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id_rol }}" {{ old('id_rol', $usuario->id_rol) == $role->id_rol ? 'selected' : '' }}>
                                    {{ $role->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_rol')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select class="form-control @error('estado') is-invalid @enderror" id="estado" name="estado" required>
                            <option value="ACTIVO" {{ old('estado', $usuario->estado) == 'ACTIVO' ? 'selected' : '' }}>ACTIVO</option>
                            <option value="INACTIVO" {{ old('estado', $usuario->estado) == 'INACTIVO' ? 'selected' : '' }}>INACTIVO</option>
                        </select>
                        @error('estado')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
