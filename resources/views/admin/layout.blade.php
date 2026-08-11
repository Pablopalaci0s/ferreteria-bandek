<!DOCTYPE html>

<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>{{ $title ?? 'Panel BANDEK' }}</title>
<link rel="icon" type="image/png" href="{{ asset('img/favicon-32.png') }}">

@vite(['resources/css/app.css', 'resources/js/app.js'])


</head>

<body class="bg-gray-100 font-sans">

<div class="flex min-h-screen">

<aside class="w-60 bg-gray-900 text-gray-300 flex flex-col shrink-0">

    <div class="px-5 py-5 border-b border-gray-800 flex items-center" style="gap: 0.6rem;">
        <img src="{{ asset('img/logo-icono.png') }}" alt="Ferretería BANDEK" style="width:32px; height:32px; object-fit:contain; flex-shrink:0;">
        <div>
            <span class="text-white font-bold text-lg block leading-tight">BANDEK</span>
            <span class="block text-xs text-gray-500">Panel administrativo</span>
        </div>
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

<div class="flex-1 flex flex-col">

    <header class="bg-white border-b px-8 py-4">
        <h1 class="text-lg font-semibold text-gray-800">
            {{ $header ?? '' }}
        </h1>
    </header>

    <main class="flex-1 p-8">
        {{ $slot }}
    </main>

</div>


</div>

</body>
</html>
