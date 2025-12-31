@extends('layouts.admin')

@section('title', 'Sesiones Activas')
@section('page_title', 'Gestión de Sesiones')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('security.dashboard') }}">Seguridad</a></li>
    <li class="breadcrumb-item active">Sesiones</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-desktop mr-2"></i>Sesiones Activas ({{ $activeSessions->count() }})
                </h3>
                <div class="card-tools">
                    @if($activeSessions->count() > 1)
                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#terminateAllModal">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Todas
                    </button>
                    @endif
                </div>
            </div>
            <div class="card-body">
                @if($activeSessions->count() > 0)
                <div class="row">
                    @foreach($activeSessions as $session)
                    <div class="col-md-6 mb-4">
                        <div class="card {{ $session->id === $currentSessionId ? 'card-primary' : 'card-secondary' }}">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-{{ $session->device_type === 'mobile' ? 'mobile-alt' : ($session->device_type === 'tablet' ? 'tablet-alt' : 'desktop') }}"></i>
                                    {{ $session->device_name ?: 'Dispositivo Desconocido' }}
                                    @if($session->id === $currentSessionId)
                                        <span class="badge badge-success ml-2">Sesión Actual</span>
                                    @endif
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <strong>IP:</strong><br>
                                        <code>{{ $session->ip_address }}</code>
                                    </div>
                                    <div class="col-6">
                                        <strong>Plataforma:</strong><br>
                                        {{ $session->platform ?: 'Desconocida' }}
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-6">
                                        <strong>Navegador:</strong><br>
                                        {{ $session->browser ?: 'Desconocido' }}
                                    </div>
                                    <div class="col-6">
                                        <strong>Última Actividad:</strong><br>
                                        {{ $session->last_activity_formatted }}
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <strong>Estado:</strong><br>
                                        @if($session->isExpired())
                                            <span class="badge badge-warning">Expirada</span>
                                        @elseif($session->is_current)
                                            <span class="badge badge-success">Activa (Actual)</span>
                                        @else
                                            <span class="badge badge-info">Activa</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if($session->id !== $currentSessionId)
                            <div class="card-footer">
                                <button type="button" class="btn btn-danger btn-sm terminate-session" 
                                        data-session-id="{{ $session->id }}"
                                        data-device-name="{{ $session->device_name }}">
                                    <i class="fas fa-times"></i> Cerrar Sesión
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-desktop fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No hay sesiones activas</h4>
                    <p class="text-muted">Todas las sesiones han expirado o han sido cerradas</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Security Tips -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-2"></i>Consejos de Seguridad
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success mr-2"></i>Revisa regularmente tus sesiones activas</li>
                            <li><i class="fas fa-check text-success mr-2"></i>Cierra sesiones en dispositivos que no uses</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success mr-2"></i>Si ves actividad sospechosa, cierra todas las sesiones</li>
                            <li><i class="fas fa-check text-success mr-2"></i>Nunca dejes sesiones abiertas en dispositivos públicos</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Terminate All Sessions Modal -->
<div class="modal fade" id="terminateAllModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Todas las Sesiones
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('security.sessions.terminate-all') }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>¿Estás seguro?</strong> Esta acción cerrará todas tus otras sesiones activas. 
                        Tu sesión actual permanecerá abierta.
                    </div>
                    <div class="form-group">
                        <label for="password">Confirma tu contraseña:</label>
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Todas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Terminate individual session
    $('.terminate-session').click(function() {
        const sessionId = $(this).data('session-id');
        const deviceName = $(this).data('device-name');
        
        if (confirm(`¿Cerrar sesión en "${deviceName}"?`)) {
            $.post('{{ route("security.sessions.terminate") }}', {
                _token: '{{ csrf_token() }}',
                session_id: sessionId
            })
            .done(function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (response.error || 'No se pudo cerrar la sesión'));
                }
            })
            .fail(function() {
                alert('Error de conexión. Inténtalo de nuevo.');
            });
        }
    });

    // Auto refresh every 30 seconds
    setInterval(function() {
        location.reload();
    }, 30000);
});
</script>
@endsection