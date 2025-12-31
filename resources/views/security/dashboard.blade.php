@extends('layouts.admin')

@section('title', 'Centro de Seguridad')
@section('page_title', 'Centro de Seguridad')
@section('breadcrumb')
    <li class="breadcrumb-item active">Seguridad</li>
@endsection

@section('content')
<div class="row">
    <!-- Security Score Card -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-shield-alt mr-2"></i>Puntuación de Seguridad
                </h3>
            </div>
            <div class="card-body text-center">
                <div class="progress progress-xl mb-3">
                    <div class="progress-bar bg-{{ $securityScore['color'] }}" role="progressbar" 
                         style="width: {{ $securityScore['score'] }}%">
                        {{ $securityScore['score'] }}%
                    </div>
                </div>
                <h2 class="text-{{ $securityScore['color'] }}">{{ $securityScore['level'] }}</h2>
                <p class="text-muted">{{ $securityScore['score'] }}/100 puntos</p>
                
                @if(count($securityScore['recommendations']) > 0)
                <div class="mt-3">
                    <h5>Recomendaciones:</h5>
                    <ul class="list-unstyled text-left">
                        @foreach($securityScore['recommendations'] as $recommendation)
                        <li><i class="fas fa-info-circle text-info mr-1"></i> {{ $recommendation }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cogs mr-2"></i>Configuración de Seguridad
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- 2FA Status -->
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-{{ $user2fa && $user2fa->is_enabled ? 'success' : 'warning' }}">
                                <i class="fas fa-mobile-alt"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Autenticación 2FA</span>
                                <span class="info-box-number">
                                    {{ $user2fa && $user2fa->is_enabled ? 'Habilitado' : 'Deshabilitado' }}
                                </span>
                                <div class="mt-2">
                                    @if($user2fa && $user2fa->is_enabled)
                                        <a href="{{ route('security.2fa.setup') }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-cog"></i> Administrar
                                        </a>
                                    @else
                                        <a href="{{ route('security.2fa.setup') }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-plus"></i> Configurar
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Sessions -->
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-{{ $activeSessions <= 3 ? 'info' : 'warning' }}">
                                <i class="fas fa-desktop"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Sesiones Activas</span>
                                <span class="info-box-number">{{ $activeSessions }}</span>
                                <div class="mt-2">
                                    <a href="{{ route('security.sessions') }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> Ver Todas
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <!-- Login History -->
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-secondary">
                                <i class="fas fa-history"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Historial de Accesos</span>
                                <span class="info-box-number">{{ $recentLogins->count() }} (24h)</span>
                                <div class="mt-2">
                                    <a href="{{ route('security.login-history') }}" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-list"></i> Ver Historial
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Actions -->
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-danger">
                                <i class="fas fa-key"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Cambiar Contraseña</span>
                                <span class="info-box-number">Actualizar</span>
                                <div class="mt-2">
                                    <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Cambiar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clock mr-2"></i>Actividad Reciente (Últimas 24 horas)
                </h3>
            </div>
            <div class="card-body">
                @if($recentLogins->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>IP</th>
                                <th>Dispositivo</th>
                                <th>Estado</th>
                                <th>2FA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentLogins->take(10) as $login)
                            <tr>
                                <td>{{ $login->login_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <code>{{ $login->ip_address }}</code>
                                    @if($login->country)
                                        <small class="text-muted">({{ $login->country }})</small>
                                    @endif
                                </td>
                                <td>
                                    <i class="fas fa-{{ $login->device_type === 'mobile' ? 'mobile' : ($login->device_type === 'tablet' ? 'tablet' : 'desktop') }}"></i>
                                    {{ $login->device_name }}
                                </td>
                                <td>
                                    <span class="badge badge-{{ $login->login_status === 'success' ? 'success' : 'danger' }}">
                                        {{ ucfirst($login->login_status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($login->is_2fa_used)
                                        <i class="fas fa-check-circle text-success" title="2FA utilizado"></i>
                                    @else
                                        <i class="fas fa-times-circle text-muted" title="Sin 2FA"></i>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                    <p class="text-muted">No hay actividad reciente</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Security Tips -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card card-primary collapsed-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-lightbulb mr-2"></i>Consejos de Seguridad
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" style="display: none;">
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success mr-2"></i>Usa contraseñas únicas y complejas</li>
                            <li><i class="fas fa-check text-success mr-2"></i>Habilita la autenticación de dos factores</li>
                            <li><i class="fas fa-check text-success mr-2"></i>Revisa regularmente tus sesiones activas</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success mr-2"></i>Cierra sesión cuando uses dispositivos compartidos</li>
                            <li><i class="fas fa-check text-success mr-2"></i>Mantén actualizado tu navegador</li>
                            <li><i class="fas fa-check text-success mr-2"></i>Reporta cualquier actividad sospechosa</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto refresh security score every 5 minutes
    setInterval(function() {
        location.reload();
    }, 300000);
});
</script>
@endsection