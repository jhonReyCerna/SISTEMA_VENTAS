@extends('layouts.admin')

@section('title', 'Notificaciones')
@section('page_title', 'Centro de Notificaciones')
@section('breadcrumb')
    <li class="breadcrumb-item active">Notificaciones</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bell mr-2"></i>Mis Notificaciones
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-success btn-sm" onclick="marcarTodasLeidas()">
                        <i class="fas fa-check-double"></i> Marcar todas como leídas
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                @forelse($notificaciones as $notificacion)
                    <div class="notification-item {{ !$notificacion->leida ? 'unread' : '' }}" 
                         data-id="{{ $notificacion->id_notificacion }}">
                        <div class="d-flex align-items-center p-3 border-bottom">
                            <div class="notification-icon mr-3">
                                <span class="badge badge-{{ $notificacion->color }} p-2">
                                    <i class="{{ $notificacion->icono }}"></i>
                                </span>
                            </div>
                            <div class="notification-content flex-grow-1">
                                <h6 class="mb-1 {{ !$notificacion->leida ? 'font-weight-bold' : '' }}">
                                    {{ $notificacion->titulo }}
                                </h6>
                                <p class="mb-1 text-sm">{{ $notificacion->mensaje }}</p>
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i>
                                    {{ $notificacion->fecha_creacion->diffForHumans() }}
                                </small>
                            </div>
                            <div class="notification-actions">
                                @if($notificacion->url)
                                    <a href="{{ $notificacion->url }}" class="btn btn-sm btn-outline-primary mr-1">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                @endif
                                @if(!$notificacion->leida)
                                    <button class="btn btn-sm btn-outline-success mr-1" 
                                            onclick="marcarLeida({{ $notificacion->id_notificacion }})">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                                <button class="btn btn-sm btn-outline-danger" 
                                        onclick="eliminarNotificacion({{ $notificacion->id_notificacion }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No tienes notificaciones</h5>
                        <p class="text-muted">Las nuevas notificaciones aparecerán aquí</p>
                    </div>
                @endforelse
            </div>
            
            @if($notificaciones->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        {{ $notificaciones->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.notification-item.unread {
    background-color: #f8f9ff;
}

.notification-item:hover {
    background-color: #f5f5f5;
}

.notification-icon .badge {
    font-size: 14px;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.notification-actions .btn {
    padding: 0.25rem 0.5rem;
}
</style>
@endpush

@push('scripts')
<script>
function marcarLeida(id) {
    fetch(`/notificaciones/${id}/leida`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const item = document.querySelector(`[data-id="${id}"]`);
            item.classList.remove('unread');
            const titulo = item.querySelector('h6');
            titulo.classList.remove('font-weight-bold');
            const boton = item.querySelector('.btn-outline-success');
            if (boton) boton.remove();
        }
    })
    .catch(error => console.error('Error:', error));
}

function marcarTodasLeidas() {
    fetch('/notificaciones/marcar-todas-leidas', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

function eliminarNotificacion(id) {
    if (confirm('¿Estás seguro de eliminar esta notificación?')) {
        fetch(`/notificaciones/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = document.querySelector(`[data-id="${id}"]`);
                item.remove();
            }
        })
        .catch(error => console.error('Error:', error));
    }
}
</script>
@endpush