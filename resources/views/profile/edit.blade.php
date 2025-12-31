@extends('layouts.admin')

@section('title', 'Mi Perfil')
@section('page_title', 'Mi Perfil')
@section('breadcrumb')
    <li class="breadcrumb-item active">Perfil</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Profile Image -->
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <div class="profile-user-img-wrapper mb-3">
                        <div class="profile-user-img-circle" id="previewContainer">
                            @if($user->foto)
                                <img src="{{ asset('storage/fotos/' . $user->foto) }}" alt="Foto de perfil" class="profile-img" id="profilePreview">
                            @else
                                <i class="fas fa-user fa-4x text-primary" id="defaultIcon"></i>
                            @endif
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalFoto">
                        <i class="fas fa-camera mr-1"></i>Cambiar foto
                    </button>
                </div>

                <h3 class="profile-username text-center">{{ $user->nombre }}</h3>

                <p class="text-muted text-center">{{ $user->role->nombre ?? 'Usuario' }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b><i class="fas fa-envelope mr-2"></i>Correo</b>
                        <span class="float-right text-muted">{{ $user->email }}</span>
                    </li>
                    <li class="list-group-item">
                        <b><i class="fas fa-calendar mr-2"></i>Miembro desde</b>
                        <span class="float-right text-muted">{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}</span>
                    </li>
                    <li class="list-group-item">
                        <b><i class="fas fa-clock mr-2"></i>Última actualización</b>
                        <span class="float-right text-muted">{{ $user->updated_at ? $user->updated_at->diffForHumans() : 'N/A' }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Account Status -->
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-shield-alt mr-2"></i>Seguridad de la Cuenta</h3>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span><i class="fas fa-user-check mr-2"></i>Estado de la cuenta:</span>
                    @if($user->estado == 'ACTIVO')
                        <span class="badge badge-success"><i class="fas fa-check-circle"></i> Activa</span>
                    @else
                        <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Inactiva</span>
                    @endif
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-user-shield mr-2"></i>Rol:</span>
                    <span class="badge badge-primary">{{ $user->role->nombre ?? 'Usuario' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Alerta de foto actualizada -->
        @if (session('status') === 'photo-updated')
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i>Foto de perfil actualizada correctamente.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        
        <!-- Update Profile Information -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-edit mr-2"></i>Información del perfil</h3>
            </div>
            <form method="post" action="{{ route('profile.update') }}">
                @csrf
                @method('patch')
                <div class="card-body">
                    <p class="text-muted mb-4">Actualice la información del perfil y la dirección de correo electrónico de su cuenta.</p>
                    
                    <div class="form-group">
                        <label for="name"><i class="fas fa-user mr-2"></i>Nombre</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->nombre) }}" required autofocus>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope mr-2"></i>Correo electrónico</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="off">
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        
                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <small class="form-text text-warning">
                                <i class="fas fa-exclamation-triangle"></i> Su dirección de correo electrónico no está verificada.
                                <form method="post" action="{{ route('verification.send') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-link p-0 m-0 align-baseline text-primary">
                                        Haga clic aquí para reenviar el correo de verificación.
                                    </button>
                                </form>
                            </small>
                            
                            @if (session('status') === 'verification-link-sent')
                                <small class="form-text text-success">
                                    <i class="fas fa-check-circle"></i> Se ha enviado un nuevo enlace de verificación a su dirección de correo electrónico.
                                </small>
                            @endif
                        @endif
                    </div>

                    @if (session('status') === 'profile-updated')
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>Perfil actualizado correctamente.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>Guardar cambios
                    </button>
                </div>
            </form>
        </div>

        <!-- Update Password -->
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-key mr-2"></i>Actualizar contraseña</h3>
            </div>
            <form method="post" action="{{ route('password.update') }}">
                @csrf
                @method('put')
                <div class="card-body">
                    <p class="text-muted mb-4">Asegúrese de que su cuenta utilice una contraseña larga y aleatoria para mantener su seguridad.</p>
                    
                    <div class="form-group">
                        <label for="current_password"><i class="fas fa-lock mr-2"></i>Contraseña actual</label>
                        <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" id="current_password" name="current_password" autocomplete="current-password">
                        @error('current_password', 'updatePassword')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password"><i class="fas fa-key mr-2"></i>Nueva contraseña</label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" id="password" name="password" autocomplete="new-password">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                    <i class="fas fa-eye" id="password-icon"></i>
                                </button>
                            </div>
                            @error('password', 'updatePassword')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                        <small class="form-text text-muted">Mínimo 8 caracteres</small>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation"><i class="fas fa-check-double mr-2"></i>Confirmar nueva contraseña</label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                    <i class="fas fa-eye" id="password_confirmation-icon"></i>
                                </button>
                            </div>
                            @error('password_confirmation', 'updatePassword')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    @if (session('status') === 'password-updated')
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <h5><i class="fas fa-check-circle mr-2"></i>¡Contraseña actualizada correctamente!</h5>
                            <p class="mb-0">
                                <small><i class="fas fa-clock mr-1"></i>Última actualización: {{ session('password_updated_at') ?? now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY [a las] h:mm A') }}</small>
                            </p>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save mr-2"></i>Actualizar contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para cambiar foto -->
<div class="modal fade" id="modalFoto" tabindex="-1" role="dialog" aria-labelledby="modalFotoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('profile.update.photo') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="modalFotoLabel">
                        <i class="fas fa-camera mr-2"></i>Cambiar foto de perfil
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <div class="preview-wrapper">
                            <img id="modalPreview" src="{{ $user->foto ? asset('storage/fotos/' . $user->foto) : '' }}" 
                                 class="img-thumbnail {{ $user->foto ? '' : 'd-none' }}" 
                                 style="max-width: 200px; max-height: 200px; border-radius: 50%;">
                            <div id="modalDefaultIcon" class="{{ $user->foto ? 'd-none' : '' }}">
                                <i class="fas fa-user fa-5x text-muted"></i>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="foto">
                            <i class="fas fa-upload mr-2"></i>Seleccionar foto
                        </label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="foto" name="foto" accept="image/*" onchange="previewImage(this)">
                            <label class="custom-file-label" for="foto">Elegir archivo...</label>
                        </div>
                        <small class="form-text text-muted">
                            Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 2MB
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>Guardar foto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('extra_css')
<style>
    .profile-user-img-wrapper {
        display: flex;
        justify-content: center;
    }
    
    .profile-user-img-circle {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }
    
    .profile-user-img-circle i {
        color: white;
    }
    
    /* Evitar que el navegador cambie estilos en autocomplete */
    input:-webkit-autofill,
    input:-webkit-autofill:hover,
    input:-webkit-autofill:focus,
    input:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 30px white inset !important;
        -webkit-text-fill-color: #495057 !important;
        background-color: white !important;
        color: #495057 !important;
    }
    
    /* Asegurar que los inputs mantengan el color negro */
    input.form-control {
        color: #495057 !important;
        background-color: white !important;
    }
    
    input.form-control:disabled,
    input.form-control[readonly] {
        background-color: #e9ecef !important;
        opacity: 1 !important;
    }
    
    .profile-img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .preview-wrapper {
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .profile-username {
        font-size: 21px;
        margin-top: 10px;
    }
    
    .box-profile {
        padding: 20px;
    }
    
    .alert {
        animation: fadeIn 0.5s;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('extra_js')
<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '-icon');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Preview de imagen en el modal
    function previewImage(input) {
        const preview = document.getElementById('modalPreview');
        const defaultIcon = document.getElementById('modalDefaultIcon');
        const fileLabel = document.querySelector('.custom-file-label');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                defaultIcon.classList.add('d-none');
            };
            
            reader.readAsDataURL(input.files[0]);
            fileLabel.textContent = input.files[0].name;
        }
    }
</script>
@endsection
