<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('title', 'Sistema de Ventas') - Admin</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/2.3.0/overlayscrollbars.min.css">

    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <link rel="stylesheet" href="{{ asset('css/pagination-custom.css') }}">
    @yield('extra_css')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <script>
        // Verificar sesión al cargar la página
        (function() {
            // Detectar si el navegador se cerró y abrió de nuevo
            var wasReloaded = sessionStorage.getItem('wasReloaded');
            
            if (!wasReloaded) {
                // Primera carga después de abrir el navegador
                // Verificar autenticación con el servidor
                fetch('{{ route("dashboard") }}', {
                    method: 'HEAD',
                    credentials: 'same-origin'
                }).then(response => {
                    if (response.redirected || response.status === 401) {
                        window.location.replace('{{ route("login") }}');
                    } else {
                        sessionStorage.setItem('wasReloaded', 'true');
                    }
                }).catch(() => {
                    sessionStorage.setItem('wasReloaded', 'true');
                });
            }
        })();
        
        // Prevenir navegación hacia atrás
        (function() {
            // Si venimos del login, reemplazar esa entrada del historial
            if (document.referrer && (document.referrer.includes('/login') || document.referrer.includes('/register'))) {
                window.history.replaceState(null, null, window.location.href);
            } else {
                // Para otras navegaciones, agregar entrada
                window.history.pushState(null, null, window.location.href);
            }
            
            // Interceptar el botón atrás
            window.addEventListener('popstate', function(event) {
                window.history.pushState(null, null, window.location.href);
            });
        })();
    </script>
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('dashboard') }}" class="nav-link">Home</a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Notifications Dropdown Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#" id="notifications-link">
                        <i class="fas fa-bell"></i>
                        <span class="badge badge-warning navbar-badge" id="notifications-count" style="display: none;">0</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" id="notifications-dropdown">
                        <span class="dropdown-item-text">Notificaciones</span>
                        <div class="dropdown-divider"></div>
                        <div id="notifications-list">
                            <a href="#" class="dropdown-item text-center">Cargando...</a>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('notificaciones.index') }}" class="dropdown-item dropdown-footer">Ver todas las notificaciones</a>
                    </div>
                </li>
                
                <!-- User Account Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fas fa-user"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="{{ route('profile.edit') }}" class="dropdown-item">
                            <i class="fas fa-user mr-2"></i> Perfil
                        </a>
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ route('dashboard') }}" class="brand-link">
                <img src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">Sistema Ventas</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        @if(Auth::user()->foto)
                            <img src="{{ asset('storage/fotos/' . Auth::user()->foto) }}" class="img-circle elevation-2" alt="User Image">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nombre) }}&background=667eea&color=fff&size=160" class="img-circle elevation-2" alt="User Image">
                        @endif
                    </div>
                    <div class="info">
                        <a href="{{ route('profile.edit') }}" class="d-block">{{ Auth::user()->nombre ?? 'Usuario' }}</a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <!-- Dashboard - Todos los roles -->
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Panel principal con estadísticas">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <!-- Roles - Solo Admin -->
                        @if(Auth::user()->role->nombre === 'Admin')
                        <li class="nav-item">
                            <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Gestionar roles del sistema">
                                <i class="nav-icon fas fa-shield-alt"></i>
                                <p>Roles</p>
                            </a>
                        </li>
                        @endif

                        <!-- Usuarios - Solo Admin -->
                        @if(Auth::user()->role->nombre === 'Admin')
                        <li class="nav-item">
                            <a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Administrar usuarios del sistema">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Usuarios</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('logs.index') }}" class="nav-link {{ request()->routeIs('logs.*') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Auditoría completa del sistema">
                                <i class="nav-icon fas fa-clipboard-list"></i>
                                <p>Logs de Actividad</p>
                            </a>
                        </li>
                        @endif

                        <!-- Clientes - Admin, Supervisor, Vendedor -->
                        @if(in_array(Auth::user()->role->nombre, ['Admin', 'Supervisor', 'Vendedor']))
                        <li class="nav-item">
                            <a href="{{ route('clientes.index') }}" class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Gestionar información de clientes">
                                <i class="nav-icon fas fa-user-tie"></i>
                                <p>Clientes</p>
                            </a>
                        </li>
                        @endif

                        <!-- Categorías - Admin, Supervisor -->
                        @if(in_array(Auth::user()->role->nombre, ['Admin', 'Supervisor']))
                        <li class="nav-item">
                            <a href="{{ route('categorias.index') }}" class="nav-link {{ request()->routeIs('categorias.*') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Organizar productos por categorías">
                                <i class="nav-icon fas fa-th-list"></i>
                                <p>Categorías</p>
                            </a>
                        </li>
                        @endif

                        <!-- Productos - Admin, Supervisor -->
                        @if(in_array(Auth::user()->role->nombre, ['Admin', 'Supervisor']))
                        <li class="nav-item">
                            <a href="{{ route('productos.index') }}" class="nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Administrar catálogo de productos">
                                <i class="nav-icon fas fa-cube"></i>
                                <p>Productos</p>
                            </a>
                        </li>
                        @endif

                        <!-- Ventas - Admin, Supervisor, Vendedor -->
                        @if(in_array(Auth::user()->role->nombre, ['Admin', 'Supervisor', 'Vendedor']))
                        <li class="nav-item">
                            <a href="{{ route('ventas.index') }}" class="nav-link {{ request()->routeIs('ventas.*') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Registrar y consultar ventas">
                                <i class="nav-icon fas fa-shopping-cart"></i>
                                <p>Ventas</p>
                            </a>
                        </li>
                        @endif

                        <!-- Movimientos Stock - Admin, Supervisor -->
                        @if(in_array(Auth::user()->role->nombre, ['Admin', 'Supervisor']))
                        <li class="nav-item">
                            <a href="{{ route('movimientos.index') }}" class="nav-link {{ request()->routeIs('movimientos.*') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Control de entradas y salidas de inventario">
                                <i class="nav-icon fas fa-exchange-alt"></i>
                                <p>Movimientos Stock</p>
                            </a>
                        </li>
                        @endif

                        <!-- Reportes - Admin, Supervisor -->
                        @if(in_array(Auth::user()->role->nombre, ['Admin', 'Supervisor']))
                        <li class="nav-item">
                            <a href="{{ route('reportes.index') }}" class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Analizar datos y generar reportes">
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <p>Reportes</p>
                            </a>
                        </li>
                        @endif

                        <!-- Notificaciones - Todos los roles -->
                        <li class="nav-item">
                            <a href="{{ route('notificaciones.index') }}" class="nav-link {{ request()->routeIs('notificaciones.*') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Centro de notificaciones y alertas">
                                <i class="nav-icon fas fa-bell"></i>
                                <p>Notificaciones</p>
                                <span class="badge badge-warning right" id="sidebar-notifications-count" style="display: none;">0</span>
                            </a>
                        </li>

                        <!-- Seguridad - Todos los roles -->
                        <li class="nav-item {{ request()->routeIs('security.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->routeIs('security.*') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Centro de seguridad y configuración avanzada">
                                <i class="nav-icon fas fa-shield-alt"></i>
                                <p>
                                    Seguridad
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('security.dashboard') }}" class="nav-link {{ request()->routeIs('security.dashboard') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Centro de Seguridad</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('security.2fa.setup') }}" class="nav-link {{ request()->routeIs('security.2fa.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Autenticación 2FA</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('security.sessions') }}" class="nav-link {{ request()->routeIs('security.sessions') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Sesiones Activas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('security.login-history') }}" class="nav-link {{ request()->routeIs('security.login-history') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Historial de Accesos</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('page_title', 'Dashboard')</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                {!! App\Helpers\BreadcrumbHelper::render() !!}
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h4 class="alert-heading">¡Error!</h4>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <strong>Copyright &copy; 2025 <a href="#">Sistema de Ventas</a>.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 1.0.0
            </div>
        </footer>
    </div>

    <!-- Modal de Confirmación para Eliminar -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Confirmar Eliminación
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="mb-0" id="confirmDeleteMessage">¿Estás seguro de que deseas eliminar este elemento? Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="fas fa-trash mr-1"></i>Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>

    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- overlayScrollbars -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/2.3.0/overlayscrollbars.browser.es6.min.js"></script>

    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    <script>
        $(function () {
            OverlayScrollbars(document.body, {
                autoHide: 'leave',
                sizeAutoCapable: true,
                className: 'os-theme-light'
            });

            // Inicializar tooltips de Bootstrap
            $('[data-toggle="tooltip"]').tooltip();

            // Cargar notificaciones al iniciar
            cargarNotificaciones();

            // Actualizar notificaciones cada 30 segundos
            setInterval(cargarNotificaciones, 30000);
        });

        // Función para cargar notificaciones
        function cargarNotificaciones() {
            fetch('/notificaciones/api')
                .then(response => response.json())
                .then(data => {
                    const count = data.no_leidas || 0;
                    const badge = document.getElementById('notifications-count');
                    const sidebarBadge = document.getElementById('sidebar-notifications-count');
                    
                    if (count > 0) {
                        badge.textContent = count > 9 ? '9+' : count;
                        badge.style.display = 'inline-block';
                        sidebarBadge.textContent = count > 9 ? '9+' : count;
                        sidebarBadge.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                        sidebarBadge.style.display = 'none';
                    }

                    // Actualizar dropdown de notificaciones
                    const notificationsList = document.getElementById('notifications-list');
                    if (data.notificaciones && data.notificaciones.length > 0) {
                        notificationsList.innerHTML = data.notificaciones.slice(0, 5).map(n => `
                            <a href="${n.url || '#'}" class="dropdown-item ${!n.leida ? 'bg-light' : ''}" 
                               onclick="marcarComoLeida(${n.id_notificacion})">
                                <i class="${n.icono} text-${n.color}"></i> ${n.titulo}
                                <span class="float-right text-muted text-sm">${formatearFecha(n.fecha_creacion)}</span>
                            </a>
                        `).join('');
                    } else {
                        notificationsList.innerHTML = '<a href="#" class="dropdown-item text-center">No hay notificaciones</a>';
                    }
                })
                .catch(error => console.error('Error cargando notificaciones:', error));
        }

        // Marcar notificación como leída
        function marcarComoLeida(id) {
            fetch(`/notificaciones/${id}/leida`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(() => cargarNotificaciones())
            .catch(error => console.error('Error:', error));
        }

        // Formatear fecha para mostrar
        function formatearFecha(fecha) {
            const date = new Date(fecha);
            const now = new Date();
            const diff = now - date;
            const minutes = Math.floor(diff / 60000);
            
            if (minutes < 1) return 'Ahora';
            if (minutes < 60) return `${minutes}m`;
            if (minutes < 1440) return `${Math.floor(minutes / 60)}h`;
            return `${Math.floor(minutes / 1440)}d`;
        }

        // Sistema de confirmación para eliminar
        let deleteForm = null;

        function confirmDelete(element, message = null) {
            deleteForm = $(element).closest('form');
            const customMessage = message || '¿Estás seguro de que deseas eliminar este elemento? Esta acción no se puede deshacer.';
            $('#confirmDeleteMessage').text(customMessage);
            $('#confirmDeleteModal').modal('show');
        }

        $('#confirmDeleteBtn').on('click', function() {
            if (deleteForm) {
                deleteForm.submit();
            }
        });

        // Prevenir cache del navegador
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });

        // Deshabilitar Backspace para navegar atrás
        document.addEventListener('keydown', function(e) {
            if (e.keyCode === 8) { // Backspace
                var element = e.target.nodeName.toLowerCase();
                if (element !== 'input' && element !== 'textarea') {
                    e.preventDefault();
                }
            }
            // Bloquear Alt + Flecha Izquierda
            if (e.altKey && e.keyCode === 37) {
                e.preventDefault();
            }
        });
    </script>

    @yield('extra_js')
</body>
</html>
