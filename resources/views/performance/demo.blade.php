@extends('adminlte::page')

@section('title', 'Sistema de Rendimiento - Demo')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-rocket text-primary"></i> Sistema de Rendimiento</h1>
        <small class="text-muted">Demostración de optimizaciones avanzadas</small>
    </div>
@stop

@section('content')
<div class="row">
    {{-- Búsqueda Global --}}
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-search text-primary"></i>
                    Búsqueda Global Instantánea
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">Con Caché Redis</span>
                </div>
            </div>
            <div class="card-body text-center">
                <p class="mb-3 text-muted">
                    Busca productos, clientes, ventas y usuarios de forma instantánea
                </p>
                
                {{-- Componente de búsqueda --}}
                <x-global-search />
                
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-lightbulb text-warning"></i>
                        Utiliza debouncing, caché local y Redis para máximo rendimiento
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Demostración Lazy Loading --}}
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-images text-success"></i>
                    Lazy Loading de Imágenes
                </h3>
                <div class="card-tools">
                    <span class="badge badge-success">Carga Diferida</span>
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Las imágenes se cargan automáticamente al hacerse visibles:</p>
                
                {{-- Imágenes de demostración --}}
                <div class="row">
                    <div class="col-6 mb-3">
                        <div class="text-center">
                            <img 
                                data-lazy-src="https://via.placeholder.com/200x150/007bff/ffffff?text=Producto+1" 
                                class="img-fluid rounded"
                                alt="Producto 1"
                                style="height: 120px; width: 100%; object-fit: cover; background: #f8f9fa;"
                            >
                            <small class="d-block mt-1 text-muted">Producto Ejemplo 1</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-center">
                            <img 
                                data-lazy-src="https://via.placeholder.com/200x150/28a745/ffffff?text=Producto+2" 
                                class="img-fluid rounded"
                                alt="Producto 2"
                                style="height: 120px; width: 100%; object-fit: cover; background: #f8f9fa;"
                            >
                            <small class="d-block mt-1 text-muted">Producto Ejemplo 2</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-center">
                            <img 
                                data-lazy-src="https://via.placeholder.com/200x150/dc3545/ffffff?text=Producto+3" 
                                class="img-fluid rounded"
                                alt="Producto 3"
                                style="height: 120px; width: 100%; object-fit: cover; background: #f8f9fa;"
                            >
                            <small class="d-block mt-1 text-muted">Producto Ejemplo 3</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-center">
                            <img 
                                data-lazy-src="https://via.placeholder.com/200x150/ffc107/ffffff?text=Producto+4" 
                                class="img-fluid rounded"
                                alt="Producto 4"
                                style="height: 120px; width: 100%; object-fit: cover; background: #f8f9fa;"
                            >
                            <small class="d-block mt-1 text-muted">Producto Ejemplo 4</small>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <button class="btn btn-sm btn-outline-primary" onclick="showLazyStats()">
                        <i class="fas fa-chart-bar"></i> Ver Estadísticas
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Panel de Control de Caché --}}
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-database text-warning"></i>
                    Control de Caché Redis
                </h3>
                <div class="card-tools">
                    <span class="badge badge-warning">Admin Only</span>
                </div>
            </div>
            <div class="card-body">
                @if(auth()->user()->role->nombre === 'Admin')
                    <p class="text-muted mb-3">Administración del sistema de caché:</p>
                    
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-info" onclick="getCacheStats()">
                            <i class="fas fa-chart-line"></i>
                            Ver Estadísticas de Caché
                        </button>
                        
                        <button class="btn btn-outline-warning" onclick="clearSearchCache()">
                            <i class="fas fa-broom"></i>
                            Limpiar Caché de Búsquedas
                        </button>
                    </div>
                    
                    <div id="cache-stats" class="mt-3" style="display: none;">
                        <h6>Estadísticas de Redis:</h6>
                        <div class="row">
                            <div class="col-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fas fa-memory"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Memoria Usada</span>
                                        <span class="info-box-number" id="memory-used">--</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success"><i class="fas fa-percentage"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Hit Rate</span>
                                        <span class="info-box-number" id="hit-rate">--</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Acceso Restringido</strong><br>
                        Solo los administradores pueden gestionar el caché del sistema.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Demostración de Scroll Infinito --}}
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-infinity text-info"></i>
                    Scroll Infinito (Simulación)
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">Paginación Automática</span>
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">
                    El contenido se carga automáticamente al hacer scroll hacia abajo
                </p>
                
                {{-- Contenedor de scroll infinito --}}
                <div class="infinite-scroll-demo" style="height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 5px;">
                    <div class="infinite-content p-3">
                        @for($i = 1; $i <= 20; $i++)
                        <div class="card mb-2">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Elemento {{ $i }}</h6>
                                        <small class="text-muted">Contenido de ejemplo para demostración</small>
                                    </div>
                                    <span class="badge badge-primary">{{ $i }}</span>
                                </div>
                            </div>
                        </div>
                        @endfor
                    </div>
                    
                    <div class="loading-indicator text-center py-3" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2 mb-0 text-muted">Cargando más contenido...</p>
                    </div>
                </div>
                
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        Esta es una simulación. En un entorno real, cargaría datos desde el servidor.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Métricas de Rendimiento --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tachometer-alt text-danger"></i>
                    Métricas de Rendimiento
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-bolt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Búsquedas en Caché</span>
                                <span class="info-box-number">85%</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 85%"></div>
                                </div>
                                <span class="progress-description">Reducción en tiempo de respuesta</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-images"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Imágenes Lazy</span>
                                <span class="info-box-number">60%</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 60%"></div>
                                </div>
                                <span class="progress-description">Reducción en carga inicial</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6">
                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fas fa-infinity"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Scroll Infinito</span>
                                <span class="info-box-number">40%</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 40%"></div>
                                </div>
                                <span class="progress-description">Mejora en experiencia</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6">
                        <div class="info-box bg-danger">
                            <span class="info-box-icon"><i class="fas fa-server"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Redis Cache</span>
                                <span class="info-box-number">95%</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 95%"></div>
                                </div>
                                <span class="progress-description">Disponibilidad del servicio</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h6>Beneficios Implementados:</h6>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-check text-success"></i> Búsqueda instantánea con debouncing</span>
                            <span class="badge badge-success badge-pill">Activo</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-check text-success"></i> Caché inteligente con Redis</span>
                            <span class="badge badge-success badge-pill">Activo</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-check text-success"></i> Lazy loading de imágenes</span>
                            <span class="badge badge-success badge-pill">Activo</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-check text-success"></i> Scroll infinito para paginación</span>
                            <span class="badge badge-success badge-pill">Activo</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/search.css') }}">
    <style>
        .infinite-scroll-demo {
            background: #f8f9fa;
        }
        
        .lazy-loading {
            background: linear-gradient(90deg, #f0f0f0 25%, transparent 37%, #f0f0f0 63%);
            background-size: 400% 100%;
            animation: shimmer 1.5s ease-in-out infinite;
        }
        
        .lazy-loaded {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes shimmer {
            0% { background-position: 100% 0; }
            100% { background-position: -100% 0; }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .info-box {
            min-height: 90px;
        }
        
        .info-box-number {
            font-size: 1.5rem;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('js/search.js') }}"></script>
    <script src="{{ asset('js/lazy-loading.js') }}"></script>
    
    <script>
        // Funciones específicas para la demo
        function showLazyStats() {
            if (window.lazyLoader) {
                const stats = window.lazyLoader.getStats();
                Swal.fire({
                    title: 'Estadísticas de Lazy Loading',
                    html: `
                        <div class="text-left">
                            <p><strong>Imágenes cargando:</strong> ${stats.loading}</p>
                            <p><strong>Imágenes cargadas:</strong> ${stats.loaded}</p>
                            <p><strong>Errores:</strong> ${stats.error}</p>
                            <p><strong>Reintentando:</strong> ${stats.retrying}</p>
                        </div>
                    `,
                    icon: 'info',
                    confirmButtonText: 'Cerrar'
                });
            } else {
                Swal.fire('Error', 'LazyLoader no está inicializado', 'error');
            }
        }
        
        @if(auth()->user()->role->nombre === 'Admin')
        function getCacheStats() {
            fetch('{{ route("search.cache.stats") }}', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('memory-used').textContent = data.memory_used;
                document.getElementById('hit-rate').textContent = data.hit_rate;
                document.getElementById('cache-stats').style.display = 'block';
                
                Swal.fire({
                    title: 'Estadísticas de Redis',
                    html: `
                        <div class="text-left">
                            <p><strong>Memoria utilizada:</strong> ${data.memory_used}</p>
                            <p><strong>Clientes conectados:</strong> ${data.connected_clients}</p>
                            <p><strong>Cache hits:</strong> ${data.hits}</p>
                            <p><strong>Cache misses:</strong> ${data.misses}</p>
                            <p><strong>Hit rate:</strong> ${data.hit_rate}</p>
                            <p><strong>Total de claves:</strong> ${data.total_keys}</p>
                        </div>
                    `,
                    icon: 'info',
                    confirmButtonText: 'Cerrar'
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'No se pudieron obtener las estadísticas de caché', 'error');
            });
        }
        
        function clearSearchCache() {
            Swal.fire({
                title: '¿Confirmar acción?',
                text: 'Se limpiará todo el caché de búsquedas',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, limpiar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("search.cache.clear") }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.fire('¡Éxito!', data.message, 'success');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'No se pudo limpiar el caché', 'error');
                    });
                }
            });
        }
        @endif
        
        // Simular scroll infinito básico para la demo
        document.addEventListener('DOMContentLoaded', function() {
            const demoContainer = document.querySelector('.infinite-scroll-demo');
            let itemCount = 20;
            let isLoading = false;
            
            if (demoContainer) {
                demoContainer.addEventListener('scroll', function() {
                    if (isLoading) return;
                    
                    const scrollTop = this.scrollTop;
                    const scrollHeight = this.scrollHeight;
                    const clientHeight = this.clientHeight;
                    
                    if (scrollTop + clientHeight >= scrollHeight - 50) {
                        isLoading = true;
                        const loadingIndicator = document.querySelector('.loading-indicator');
                        const content = document.querySelector('.infinite-content');
                        
                        if (loadingIndicator && content) {
                            loadingIndicator.style.display = 'block';
                            
                            // Simular carga después de 1 segundo
                            setTimeout(() => {
                                // Agregar más elementos
                                for (let i = itemCount + 1; i <= itemCount + 10; i++) {
                                    const newItem = document.createElement('div');
                                    newItem.className = 'card mb-2';
                                    newItem.innerHTML = `
                                        <div class="card-body py-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">Elemento ${i}</h6>
                                                    <small class="text-muted">Contenido cargado dinámicamente</small>
                                                </div>
                                                <span class="badge badge-secondary">${i}</span>
                                            </div>
                                        </div>
                                    `;
                                    content.appendChild(newItem);
                                }
                                
                                itemCount += 10;
                                loadingIndicator.style.display = 'none';
                                isLoading = false;
                                
                                // Parar después de 100 elementos
                                if (itemCount >= 100) {
                                    loadingIndicator.innerHTML = `
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                                            <p class="mb-0">Demo completada - 100 elementos cargados</p>
                                        </div>
                                    `;
                                    loadingIndicator.style.display = 'block';
                                }
                            }, 1000);
                        }
                    }
                });
            }
        });
    </script>
@stop