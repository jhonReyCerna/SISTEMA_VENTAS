<?php

namespace App\Helpers;

class BreadcrumbHelper
{
    /**
     * Generar breadcrumbs automáticamente basado en la ruta actual
     */
    public static function generate()
    {
        $routeName = request()->route()->getName();
        $breadcrumbs = [];

        // Siempre empezar con Home
        $breadcrumbs[] = [
            'name' => 'Inicio',
            'url' => route('dashboard'),
            'icon' => 'fas fa-home',
            'active' => false
        ];

        // Mapeo de rutas a breadcrumbs
        $routeMap = [
            // Roles
            'roles.index' => ['name' => 'Roles', 'icon' => 'fas fa-shield-alt'],
            'roles.create' => ['name' => 'Roles', 'url' => 'roles.index', 'icon' => 'fas fa-shield-alt', 'child' => ['name' => 'Crear', 'icon' => 'fas fa-plus']],
            'roles.edit' => ['name' => 'Roles', 'url' => 'roles.index', 'icon' => 'fas fa-shield-alt', 'child' => ['name' => 'Editar', 'icon' => 'fas fa-edit']],
            'roles.show' => ['name' => 'Roles', 'url' => 'roles.index', 'icon' => 'fas fa-shield-alt', 'child' => ['name' => 'Ver', 'icon' => 'fas fa-eye']],

            // Usuarios
            'usuarios.index' => ['name' => 'Usuarios', 'icon' => 'fas fa-users'],
            'usuarios.create' => ['name' => 'Usuarios', 'url' => 'usuarios.index', 'icon' => 'fas fa-users', 'child' => ['name' => 'Crear', 'icon' => 'fas fa-plus']],
            'usuarios.edit' => ['name' => 'Usuarios', 'url' => 'usuarios.index', 'icon' => 'fas fa-users', 'child' => ['name' => 'Editar', 'icon' => 'fas fa-edit']],
            'usuarios.show' => ['name' => 'Usuarios', 'url' => 'usuarios.index', 'icon' => 'fas fa-users', 'child' => ['name' => 'Ver', 'icon' => 'fas fa-eye']],

            // Clientes
            'clientes.index' => ['name' => 'Clientes', 'icon' => 'fas fa-user-tie'],
            'clientes.create' => ['name' => 'Clientes', 'url' => 'clientes.index', 'icon' => 'fas fa-user-tie', 'child' => ['name' => 'Crear', 'icon' => 'fas fa-plus']],
            'clientes.edit' => ['name' => 'Clientes', 'url' => 'clientes.index', 'icon' => 'fas fa-user-tie', 'child' => ['name' => 'Editar', 'icon' => 'fas fa-edit']],
            'clientes.show' => ['name' => 'Clientes', 'url' => 'clientes.index', 'icon' => 'fas fa-user-tie', 'child' => ['name' => 'Ver', 'icon' => 'fas fa-eye']],

            // Categorías
            'categorias.index' => ['name' => 'Categorías', 'icon' => 'fas fa-th-list'],
            'categorias.create' => ['name' => 'Categorías', 'url' => 'categorias.index', 'icon' => 'fas fa-th-list', 'child' => ['name' => 'Crear', 'icon' => 'fas fa-plus']],
            'categorias.edit' => ['name' => 'Categorías', 'url' => 'categorias.index', 'icon' => 'fas fa-th-list', 'child' => ['name' => 'Editar', 'icon' => 'fas fa-edit']],
            'categorias.show' => ['name' => 'Categorías', 'url' => 'categorias.index', 'icon' => 'fas fa-th-list', 'child' => ['name' => 'Ver', 'icon' => 'fas fa-eye']],

            // Productos
            'productos.index' => ['name' => 'Productos', 'icon' => 'fas fa-cube'],
            'productos.create' => ['name' => 'Productos', 'url' => 'productos.index', 'icon' => 'fas fa-cube', 'child' => ['name' => 'Crear', 'icon' => 'fas fa-plus']],
            'productos.edit' => ['name' => 'Productos', 'url' => 'productos.index', 'icon' => 'fas fa-cube', 'child' => ['name' => 'Editar', 'icon' => 'fas fa-edit']],
            'productos.show' => ['name' => 'Productos', 'url' => 'productos.index', 'icon' => 'fas fa-cube', 'child' => ['name' => 'Ver', 'icon' => 'fas fa-eye']],

            // Ventas
            'ventas.index' => ['name' => 'Ventas', 'icon' => 'fas fa-shopping-cart'],
            'ventas.create' => ['name' => 'Ventas', 'url' => 'ventas.index', 'icon' => 'fas fa-shopping-cart', 'child' => ['name' => 'Crear', 'icon' => 'fas fa-plus']],
            'ventas.edit' => ['name' => 'Ventas', 'url' => 'ventas.index', 'icon' => 'fas fa-shopping-cart', 'child' => ['name' => 'Editar', 'icon' => 'fas fa-edit']],
            'ventas.show' => ['name' => 'Ventas', 'url' => 'ventas.index', 'icon' => 'fas fa-shopping-cart', 'child' => ['name' => 'Ver', 'icon' => 'fas fa-eye']],

            // Movimientos
            'movimientos.index' => ['name' => 'Movimientos Stock', 'icon' => 'fas fa-exchange-alt'],
            'movimientos.create' => ['name' => 'Movimientos Stock', 'url' => 'movimientos.index', 'icon' => 'fas fa-exchange-alt', 'child' => ['name' => 'Crear', 'icon' => 'fas fa-plus']],
            'movimientos.show' => ['name' => 'Movimientos Stock', 'url' => 'movimientos.index', 'icon' => 'fas fa-exchange-alt', 'child' => ['name' => 'Ver', 'icon' => 'fas fa-eye']],

            // Reportes
            'reportes.index' => ['name' => 'Reportes', 'icon' => 'fas fa-chart-bar'],
            'reportes.ventas-por-fecha' => ['name' => 'Reportes', 'url' => 'reportes.index', 'icon' => 'fas fa-chart-bar', 'child' => ['name' => 'Ventas por Fecha', 'icon' => 'fas fa-calendar-alt']],
            'reportes.ventas-por-usuario' => ['name' => 'Reportes', 'url' => 'reportes.index', 'icon' => 'fas fa-chart-bar', 'child' => ['name' => 'Ventas por Usuario', 'icon' => 'fas fa-user']],
            'reportes.productos-mas-vendidos' => ['name' => 'Reportes', 'url' => 'reportes.index', 'icon' => 'fas fa-chart-bar', 'child' => ['name' => 'Productos Más Vendidos', 'icon' => 'fas fa-medal']],
            'reportes.stock-alertas' => ['name' => 'Reportes', 'url' => 'reportes.index', 'icon' => 'fas fa-chart-bar', 'child' => ['name' => 'Stock y Alertas', 'icon' => 'fas fa-exclamation-triangle']],
            'reportes.kardex' => ['name' => 'Reportes', 'url' => 'reportes.index', 'icon' => 'fas fa-chart-bar', 'child' => ['name' => 'Kardex', 'icon' => 'fas fa-file-invoice']],

            // Perfil
            'profile.edit' => ['name' => 'Mi Perfil', 'icon' => 'fas fa-user-circle'],
        ];

        if (isset($routeMap[$routeName])) {
            $route = $routeMap[$routeName];
            
            // Agregar breadcrumb padre
            $breadcrumbs[] = [
                'name' => $route['name'],
                'url' => isset($route['url']) ? route($route['url']) : null,
                'icon' => $route['icon'],
                'active' => !isset($route['child'])
            ];

            // Agregar breadcrumb hijo si existe
            if (isset($route['child'])) {
                $breadcrumbs[] = [
                    'name' => $route['child']['name'],
                    'url' => null,
                    'icon' => $route['child']['icon'],
                    'active' => true
                ];
            }
        }

        return $breadcrumbs;
    }

    /**
     * Renderizar breadcrumbs como HTML
     */
    public static function render()
    {
        $breadcrumbs = self::generate();
        $html = '';

        foreach ($breadcrumbs as $index => $breadcrumb) {
            $isLast = $index === count($breadcrumbs) - 1;
            
            if ($breadcrumb['url'] && !$isLast) {
                $html .= sprintf(
                    '<li class="breadcrumb-item"><a href="%s"><i class="%s"></i> %s</a></li>',
                    $breadcrumb['url'],
                    $breadcrumb['icon'],
                    $breadcrumb['name']
                );
            } else {
                $html .= sprintf(
                    '<li class="breadcrumb-item active"><i class="%s"></i> %s</li>',
                    $breadcrumb['icon'],
                    $breadcrumb['name']
                );
            }
        }

        return $html;
    }
}
