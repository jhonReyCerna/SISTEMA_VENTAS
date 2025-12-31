@extends('layouts.admin')

@section('title', 'Configurar 2FA')
@section('page_title', 'Autenticación de Dos Factores')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('security.dashboard') }}">Seguridad</a></li>
    <li class="breadcrumb-item active">Configurar 2FA</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        @if($user2fa && $user2fa->is_enabled)
            <!-- 2FA is enabled -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-check-circle mr-2"></i>2FA Habilitado
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <h5><i class="fas fa-shield-alt"></i> ¡Excelente!</h5>
                        Tu cuenta está protegida con autenticación de dos factores desde el {{ $user2fa->enabled_at->format('d/m/Y H:i') }}.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box bg-success">
                                <span class="info-box-icon">
                                    <i class="fas fa-mobile-alt"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Último uso</span>
                                    <span class="info-box-number">
                                        {{ $user2fa->last_used_at ? $user2fa->last_used_at->diffForHumans() : 'Nunca' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box bg-info">
                                <span class="info-box-icon">
                                    <i class="fas fa-key"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Códigos de respaldo</span>
                                    <span class="info-box-number">
                                        {{ $user2fa->backup_codes ? count($user2fa->backup_codes) : 0 }} disponibles
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Backup Codes -->
                    @if($user2fa->backup_codes && count($user2fa->backup_codes) > 0)
                    <div class="card card-warning collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-key mr-2"></i>Códigos de Respaldo
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body" style="display: none;">
                            <div class="alert alert-warning">
                                <strong>¡Importante!</strong> Guarda estos códigos en un lugar seguro. Puedes usarlos para acceder a tu cuenta si pierdes tu dispositivo 2FA.
                            </div>
                            <div class="row">
                                @foreach($user2fa->backup_codes as $index => $code)
                                <div class="col-md-3 mb-2">
                                    <code class="d-block p-2 bg-light border rounded">{{ $code }}</code>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Disable 2FA -->
                    <div class="card card-danger collapsed-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-times-circle mr-2"></i>Deshabilitar 2FA
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body" style="display: none;">
                            <div class="alert alert-danger">
                                <strong>¡Atención!</strong> Deshabilitar el 2FA reducirá significativamente la seguridad de tu cuenta.
                            </div>
                            <form method="POST" action="{{ route('security.2fa.disable') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="password">Confirmar contraseña:</label>
                                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                    @error('password')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="code">Código 2FA o código de respaldo:</label>
                                    <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror" placeholder="000000" required>
                                    @error('code')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-times"></i> Deshabilitar 2FA
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- 2FA setup -->
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cog mr-2"></i>Configurar Autenticación de Dos Factores
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> ¿Qué es 2FA?</h5>
                        La autenticación de dos factores (2FA) añade una capa extra de seguridad a tu cuenta. 
                        Además de tu contraseña, necesitarás un código temporal desde tu aplicación de autenticación.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h5>Paso 1: Instala una App de Autenticación</h5>
                            <p class="text-muted mb-3">Recomendamos una de estas aplicaciones:</p>
                            <ul class="list-unstyled">
                                <li><i class="fab fa-google text-primary mr-2"></i>Google Authenticator</li>
                                <li><i class="fas fa-mobile-alt text-success mr-2"></i>Microsoft Authenticator</li>
                                <li><i class="fas fa-shield-alt text-info mr-2"></i>Authy</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Paso 2: Escanea el Código QR</h5>
                            <div class="text-center mb-3">
                                <!-- For now, display the secret key since we don't have QR generation -->
                                <div class="alert alert-secondary">
                                    <strong>Clave secreta:</strong><br>
                                    <code>{{ $user2fa->secret_key }}</code>
                                </div>
                                <small class="text-muted">
                                    Ingresa esta clave manualmente en tu app de autenticación
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 offset-md-3">
                            <h5>Paso 3: Verifica el Código</h5>
                            <form method="POST" action="{{ route('security.2fa.enable') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="code">Ingresa el código de 6 dígitos:</label>
                                    <input type="text" id="code" name="code" class="form-control form-control-lg text-center @error('code') is-invalid @enderror" 
                                           placeholder="000000" maxlength="6" required>
                                    @error('code')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-success btn-block btn-lg">
                                    <i class="fas fa-check"></i> Habilitar 2FA
                                </button>
                            </form>
                        </div>
                    </div>

                    @if($user2fa && $user2fa->backup_codes)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-warning">
                                <h5><i class="fas fa-exclamation-triangle"></i> Códigos de Respaldo</h5>
                                <p>Una vez que habilites el 2FA, podrás ver tus códigos de respaldo. Guárdalos en un lugar seguro.</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        @endif

        <div class="text-center mt-3">
            <a href="{{ route('security.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Centro de Seguridad
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto-focus on code input
    $('#code').focus();
    
    // Only allow numbers in code input
    $('#code').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
});
</script>
@endsection