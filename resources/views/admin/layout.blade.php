<!DOCTYPE html>

<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>{{ $title ?? 'Panel BANDEK' }}</title>
<link rel="icon" type="image/png" href="{{ asset('img/favicon-32.png') }}">

@vite(['resources/css/app.css', 'resources/js/app.js'])

<style>
    .admin-sidebar {
        width: 240px;
    }

    .admin-menu-toggle,
    .admin-sidebar-close {
        display: none;
    }

    @media (max-width: 767px) {

        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 50;
            transform: translateX(-100%);
            transition: transform 0.25s ease;
            overflow-y: auto;
        }

        .admin-sidebar.admin-sidebar-open {
            transform: translateX(0);
        }

        .admin-menu-toggle {
            display: inline-flex;
        }

        .admin-sidebar-close {
            display: inline-flex;
        }

        .admin-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: 40;
        }

        .admin-main-header {
            padding-left: 1.25rem !important;
            padding-right: 1.25rem !important;
        }

        .admin-main-content {
            padding: 1.25rem !important;
        }
    }
</style>

</head>

<body class="bg-gray-100 font-sans">

<div class="flex min-h-screen" x-data="{ menuAbierto: false }">

<div
    class="admin-backdrop"
    x-show="menuAbierto"
    x-cloak
    @click="menuAbierto = false"
></div>

<aside class="admin-sidebar bg-gray-900 text-gray-300 flex flex-col shrink-0" :class="menuAbierto ? 'admin-sidebar-open' : ''">

    <div class="px-5 py-5 border-b border-gray-800 flex items-center justify-between">

        <div class="flex items-center" style="gap: 0.6rem;">
            <img src="{{ asset('img/logo-icono.png') }}" alt="Ferretería BANDEK" style="width:32px; height:32px; object-fit:contain; flex-shrink:0;">
            <div>
                <span class="text-white font-bold text-lg block leading-tight">BANDEK</span>
                <span class="block text-xs text-gray-500">Panel administrativo</span>
            </div>
        </div>

        <button
            type="button"
            @click="menuAbierto = false"
            class="admin-sidebar-close text-gray-400 hover:text-white"
            aria-label="Cerrar menú"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

    </div>

    <nav class="flex-1 py-4 space-y-1">

        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-3 px-5 py-2.5 text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-red-800 text-white' : 'hover:bg-gray-800' }}">
            Dashboard
        </a>

        <a href="{{ route('admin.categorias.index') }}"
           class="flex items-center gap-3 px-5 py-2.5 text-sm {{ request()->routeIs('admin.categorias.*') ? 'bg-red-800 text-white' : 'hover:bg-gray-800' }}">
            Categorías
        </a>

        <a href="{{ route('admin.marcas.index') }}" class="flex items-center gap-3 px-5 py-2.5 text-sm {{ request()->routeIs('admin.marcas.*') ? 'bg-red-800 text-white' : 'hover:bg-gray-800' }}">
        Marcas
        </a>

        <a href="{{ route('admin.proveedores.index') }}" class="flex items-center gap-3 px-5 py-2.5 text-sm {{ request()->routeIs('admin.proveedores.*') ? 'bg-red-800 text-white' : 'hover:bg-gray-800' }}">
        Proveedores
        </a>

        <a href="{{ route('admin.productos.index') }}"
        class="flex items-center gap-3 px-5 py-2.5 text-sm {{ request()->routeIs('admin.productos.*') ? 'bg-red-800 text-white' : 'hover:bg-gray-800' }}">
        Productos
        </a>

        <a href="{{ route('admin.inventario.index') }}"
        class="flex items-center gap-3 px-5 py-2.5 text-sm {{ request()->routeIs('admin.inventario.*') ? 'bg-red-800 text-white' : 'hover:bg-gray-800' }}">
        Inventario
        </a>

        <a href="{{ route('admin.banners.index') }}"
        class="flex items-center gap-3 px-5 py-2.5 text-sm {{ request()->routeIs('admin.banners.*') ? 'bg-red-800 text-white' : 'hover:bg-gray-800' }}">
        Banner principal
        </a>

        @if (auth()->user()->rol === 'admin')
    <a href="{{ route('admin.usuarios.index') }}" class="flex items-center gap-3 px-5 py-2.5 text-sm {{ request()->routeIs('admin.usuarios.*') ? 'bg-red-800 text-white' : 'hover:bg-gray-800' }}">
        Usuarios
    </a>
@endif

        {{--Usuarios a medida que los construyamos --}}

    </nav>

    <div class="px-5 py-4 border-t border-gray-800">

        <p class="text-xs text-gray-500 mb-2">
            {{ auth()->user()->name }}
        </p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button class="text-xs text-gray-400 hover:text-white">
                Cerrar sesión
            </button>
        </form>

    </div>

</aside>

<div class="flex-1 flex flex-col" style="min-width: 0;">

    <header class="admin-main-header bg-white border-b px-8 py-4 flex items-center gap-3">

        <button
            type="button"
            @click="menuAbierto = true"
            class="admin-menu-toggle text-gray-600 hover:text-red-800"
            aria-label="Abrir menú"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <h1 class="text-lg font-semibold text-gray-800">
            {{ $header ?? '' }}
        </h1>

    </header>

    <main class="admin-main-content flex-1 p-8" style="overflow-x: auto;">
        {{ $slot }}
    </main>

</div>


</div>

</body>
</html>
