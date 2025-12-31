@extends('layouts.admin')

@section('title', 'Historial de Accesos')
@section('page_title', 'Historial de Inicios de Sesión')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('security.dashboard') }}">Seguridad</a></li>
    <li class="breadcrumb-item active">Historial</li>
@endsection

@section('content')
<!-- Statistics Cards -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['total_logins'] }}</h3>
                <p>Accesos Exitosos</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stats['failed_attempts'] }}</h3>
                <p>Intentos Fallidos</p>
            </div>
            <div class="icon">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['unique_ips'] }}</h3>
                <p>IPs Diferentes</p>
            </div>
            <div class="icon">
                <i class="fas fa-globe"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['devices'] }}</h3>
                <p>Dispositivos</p>
            </div>
            <div class="icon">
                <i class="fas fa-mobile-alt"></i>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Alert -->
@if($recentAttempts->where('login_status', 'failed')->count() > 0)
<div class="row">
    <div class="col-12">
        <div class="alert alert-warning">
            <h5><i class="fas fa-exclamation-triangle mr-2"></i>Actividad Sospechosa Detectada</h5>
            Se han detectado {{ $recentAttempts->where('login_status', 'failed')->count() }} intentos fallidos en las últimas 24 horas.
            Si no fuiste tú, considera cambiar tu contraseña y habilitar 2FA.
        </div>
    </div>
</div>
@endif

<!-- Login History Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history mr-2"></i>Historial Completo
                </h3>
                <div class="card-tools">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" id="search" class="form-control" placeholder="Buscar por IP, dispositivo...">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                @if($loginHistory->count() > 0)
                <table class="table table-striped table-hover" id="loginHistoryTable">
                    <thead>
                        <tr>
                            <th>Fecha y Hora</th>
                            <th>Estado</th>
                            <th>IP / Ubicación</th>
                            <th>Dispositivo</th>
                            <th>Navegador</th>
                            <th>2FA</th>
                            <th>Duración</th>
                            <th>Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loginHistory as $login)
                        <tr class="{{ $login->login_status === 'failed' ? 'table-danger' : '' }}">
                            <td>
                                <strong>{{ $login->login_at->format('d/m/Y') }}</strong><br>
                                <small class="text-muted">{{ $login->login_at->format('H:i:s') }}</small>
                            </td>
                            <td>
                                @if($login->login_status === 'success')
                                    <span class="badge badge-success">
                                        <i class="fas fa-check"></i> Exitoso
                                    </span>
                                @elseif($login->login_status === 'failed')
                                    <span class="badge badge-danger">
                                        <i class="fas fa-times"></i> Fallido
                                    </span>
                                @elseif($login->login_status === 'blocked')
                                    <span class="badge badge-warning">
                                        <i class="fas fa-ban"></i> Bloqueado
                                    </span>
                                @endif
                            </td>
                            <td>
                                <code>{{ $login->ip_address }}</code><br>
                                @if($login->country || $login->city)
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        {{ $login->city }}{{ $login->city && $login->country ? ', ' : '' }}{{ $login->country }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <i class="fas fa-{{ $login->device_type === 'mobile' ? 'mobile-alt' : ($login->device_type === 'tablet' ? 'tablet-alt' : 'desktop') }} mr-1"></i>
                                {{ $login->device_name ?: 'Desconocido' }}<br>
                                <small class="text-muted">{{ $login->platform ?: 'N/A' }}</small>
                            </td>
                            <td>
                                {{ $login->browser ?: 'Desconocido' }}
                            </td>
                            <td class="text-center">
                                @if($login->is_2fa_used)
                                    <i class="fas fa-check-circle text-success" title="2FA utilizado"></i>
                                @else
                                    <i class="fas fa-times-circle text-muted" title="Sin 2FA"></i>
                                @endif
                            </td>
                            <td>
                                @if($login->session_duration)
                                    {{ $login->session_duration }} min
                                @elseif($login->logout_at)
                                    <span class="text-muted">Terminada</span>
                                @elseif($login->login_status === 'success')
                                    <span class="text-success">Activa</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($login->failure_reason)
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            title="{{ $login->failure_reason }}">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                @endif
                                @if($login->login_status === 'success')
                                    @if($login->logout_at)
                                        <small class="text-muted">
                                            Cerró: {{ $login->logout_at->format('H:i') }}
                                        </small>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No hay historial de accesos</h4>
                    <p class="text-muted">Los registros de acceso aparecerán aquí</p>
                </div>
                @endif
            </div>
            @if($loginHistory->hasPages())
            <div class="card-footer">
                {{ $loginHistory->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Security Recommendations -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card card-info collapsed-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-shield-alt mr-2"></i>Recomendaciones de Seguridad
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
                        <h5>🔍 Qué revisar:</h5>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success mr-2"></i>Accesos desde IPs desconocidas</li>
                            <li><i class="fas fa-check text-success mr-2"></i>Múltiples intentos fallidos</li>
                            <li><i class="fas fa-check text-success mr-2"></i>Accesos en horarios inusuales</li>
                            <li><i class="fas fa-check text-success mr-2"></i>Dispositivos no reconocidos</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>🛡️ Acciones recomendadas:</h5>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success mr-2"></i>Cambiar contraseña si hay actividad sospechosa</li>
                            <li><i class="fas fa-check text-success mr-2"></i>Habilitar autenticación de dos factores</li>
                            <li><i class="fas fa-check text-success mr-2"></i>Revisar y cerrar sesiones inactivas</li>
                            <li><i class="fas fa-check text-success mr-2"></i>Reportar accesos no autorizados</li>
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
    // Search functionality
    $('#search').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('#loginHistoryTable tbody tr').each(function() {
            const row = $(this);
            const text = row.text().toLowerCase();
            if (text.indexOf(searchTerm) > -1) {
                row.show();
            } else {
                row.hide();
            }
        });
    });

    // Tooltip for failure reasons
    $('[title]').tooltip();
});
</script>
@endsection