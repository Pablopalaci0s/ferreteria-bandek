<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $ogTitulo = $title ?? 'Ferretería BANDEK';
        $ogDescripcion = $description ?? 'Materiales de construcción, herramientas y soluciones eléctricas. Asesoría técnica y atención por WhatsApp en Ferretería BANDEK.';
        $ogImagen = $image ?? asset('img/logo-completo.png');
        $ogTipo = $type ?? 'website';

        $configSitio = \App\Models\Configuracion::pluck('valor', 'clave');
        $whatsappNumero = $configSitio['whatsapp_numero'] ?? '';
        $telefonoSitio = $configSitio['telefono'] ?? '';
        $direccionSitio = $configSitio['direccion'] ?? 'San Salvador, El Salvador';
    @endphp

    <title>{{ $ogTitulo }}</title>
    <meta name="description" content="{{ $ogDescripcion }}">
    <link rel="icon" type="image/png" href="{{ asset('img/favicon-32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/apple-touch-icon.png') }}">

    {{-- Open Graph / WhatsApp, Facebook --}}
    <meta property="og:type" content="{{ $ogTipo }}">
    <meta property="og:site_name" content="Ferretería BANDEK">
    <meta property="og:title" content="{{ $ogTitulo }}">
    <meta property="og:description" content="{{ $ogDescripcion }}">
    <meta property="og:image" content="{{ $ogImagen }}">
    <meta property="og:image:secure_url" content="{{ $ogImagen }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="es_SV">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $ogTitulo }}">
    <meta name="twitter:description" content="{{ $ogDescripcion }}">
    <meta name="twitter:image" content="{{ $ogImagen }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">

    <style>
        /* =========================================================
           TIPOGRAFÍA DE MARCA
        ========================================================= */
        h1, h2, .bandek-cat-label, .bandek-benefit-title {
            font-family: 'Oswald', sans-serif;
            letter-spacing: 0.01em;
        }

        /* =========================================================
           TARJETAS Y BOTONES CON RELIEVE
        ========================================================= */
        .bandek-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .bandek-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 28px rgba(17, 24, 39, 0.12);
        }

        .bandek-btn-cta {
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }
        .bandek-btn-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(0, 0, 0, 0.22);
        }

        .bandek-benefit-icon-wrap {
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 9999px;
            background: rgba(153, 27, 27, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* =========================================================
           MODO OSCURO
        ========================================================= */
        body.oscuro { background-color: #111827; color: #e5e7eb; }

        body.oscuro .bg-white { background-color: #1f2937 !important; }
        body.oscuro .bg-gray-50 { background-color: #111827 !important; }
        body.oscuro .bg-gray-100 { background-color: #1f2937 !important; }
        body.oscuro .bg-amber-50 { background-color: #1f2937 !important; }

        body.oscuro .text-gray-900 { color: #f8fafc !important; }
        body.oscuro .text-gray-800 { color: #e2e8f0 !important; }
        body.oscuro .text-gray-700 { color: #cbd5e1 !important; }
        body.oscuro .text-gray-600 { color: #b0bac6 !important; }
        body.oscuro .text-gray-500 { color: #9aa5b1 !important; }
        body.oscuro .text-gray-400 { color: #94a3b8 !important; }

        body.oscuro .text-red-800,
        body.oscuro .text-red-700,
        body.oscuro .hover\:text-red-800:hover,
        body.oscuro .hover\:text-red-700:hover { color: #f87171 !important; }

        body.oscuro .border,
        body.oscuro .border-t,
        body.oscuro .border-b,
        body.oscuro .border-y { border-color: #374151 !important; }

        body.oscuro input,
        body.oscuro select,
        body.oscuro textarea {
            background-color: #1f2937 !important;
            color: #e5e7eb !important;
            border-color: #374151 !important;
        }
        body.oscuro input::placeholder { color: #94a3b8 !important; }

        body.oscuro .hover\:bg-gray-50:hover { background-color: #374151 !important; }

        body.oscuro .bandek-benefit { background: #1f2937 !important; border-color: #374151 !important; }
        body.oscuro .bandek-cat-circle { background: #1f2937 !important; border-color: #374151 !important; }
        body.oscuro .bandek-cat-label { color: #e5e7eb !important; }
        body.oscuro .bandek-benefit-icon-wrap { background: rgba(248, 113, 113, 0.15) !important; }

        body.oscuro .bandek-card:hover { box-shadow: 0 16px 28px rgba(0, 0, 0, 0.55) !important; }
    </style>
</head>
<body
    class="bg-gray-50 text-gray-800 font-sans"
    x-data="{ oscuro: localStorage.getItem('bandek-modo-oscuro') === '1' }"
    x-init="$watch('oscuro', v => localStorage.setItem('bandek-modo-oscuro', v ? '1' : '0'))"
    :class="{ 'oscuro': oscuro }"
>

{{-- =========================================================
BARRA SUPERIOR
========================================================= --}}

<div class="bg-red-800 text-white text-sm">


<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">

    <div class="flex items-center justify-between">

        <span class="hidden sm:inline">
            Asesoría técnica y materiales eléctricos
        </span>

        <span class="sm:hidden">
            Asesoría técnica y materiales eléctricos
        </span>

        <div class="flex items-center gap-4 shrink-0">

            <a href="{{ route('nosotros') }}"
               class="hover:text-red-100 hidden sm:inline">
                Nosotros
            </a>

            <a href="{{ route('catalogo.index', ['oferta' => 1]) }}"
               class="underline font-medium hover:text-red-100">
                Ver promociones ›
            </a>

        </div>

    </div>

</div>


</div>

{{-- =========================================================
HEADER
========================================================= --}}

<header class="bg-white border-b">


<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">

    <div class="flex flex-wrap items-center gap-3 md:gap-6">


        {{-- =================================================
             LOGO
        ================================================== --}}

        <a href="{{ route('inicio') }}"
           class="flex items-center gap-2 shrink-0">

            <img src="{{ asset('img/logo-icono.png') }}" alt="Ferretería BANDEK" class="w-9 h-9 object-contain">

            <span class="text-lg sm:text-xl font-bold text-red-800 tracking-tight">
                BANDEK
            </span>

        </a>


        {{-- =================================================
             BUSCADOR PRINCIPAL EN TIEMPO REAL
        ================================================== --}}

        <div
            class="order-3 md:order-none w-full md:flex-1 relative"
            x-data="buscadorPrincipal()"
            @click.outside="cerrar()"
        >

            {{-- Input + botón --}}

            <div class="flex">

                <input
                    type="text"
                    x-model="buscar"
                    @input.debounce.300ms="buscarProductos()"
                    @focus="abierto = true"
                    placeholder="Buscar en toda la tienda..."
                    autocomplete="off"
                    class="w-full min-w-0 border border-gray-300 rounded-l-md
                           px-3 sm:px-4 py-2.5 text-sm
                           focus:outline-none focus:ring-1
                           focus:ring-red-700
                           focus:border-red-700"
                >

                <button
                    type="button"
                    @click="irAlCatalogo()"
                    class="bg-red-700 hover:bg-red-800 text-white
                           px-4 sm:px-5 rounded-r-md shrink-0 transition"
                >

                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-4 h-4"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M21 21l-4.35-4.35M17 10.5A6.5 6.5 0 114 10.5a6.5 6.5 0 0113 0z"
                        />

                    </svg>

                </button>

            </div>


            {{-- =================================================
                 RESULTADOS DEL BUSCADOR
            ================================================== --}}

            <div
                x-show="abierto && buscar.trim().length > 0"
                x-cloak
                class="absolute z-50 left-0 right-0 mt-1
                       bg-white border border-gray-200
                       rounded-md shadow-xl overflow-hidden"
            >


                {{-- =================================================
                     CARGANDO
                ================================================== --}}

                <div
                    x-show="cargando"
                    class="px-4 py-4 text-sm text-gray-500 text-center"
                >

                    <div class="flex items-center justify-center gap-2">

                        <svg
                            class="animate-spin h-4 w-4 text-red-800"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                        >

                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            />

                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                            />

                        </svg>

                        Buscando productos...

                    </div>

                </div>


                {{-- =================================================
                     RESULTADOS
                ================================================== --}}

                <div x-show="!cargando">


                    {{-- Lista de productos --}}

                    <div class="max-h-[360px] overflow-y-auto">

                       <template
x-for="producto in productos"
:key="producto.id"

>


<a
    :href="producto.url"
    class="flex items-center gap-3 px-3 py-2
           hover:bg-gray-50
           border-b border-gray-100
           transition
           h-[72px]"
>

    {{-- Imagen --}}
    <div
        class="w-14 h-14 rounded-md border
               bg-gray-50 overflow-hidden
               flex items-center justify-center
               shrink-0"
    >

        <img
            :src="producto.imagen"
            :alt="producto.nombre"
            class="w-14 h-14 object-contain"
        >

    </div>


    {{-- Información --}}
    <div class="min-w-0 flex-1">

        <p
            class="text-sm font-medium text-gray-800 truncate"
            x-text="producto.nombre"
        ></p>

        <p
            x-show="producto.marca"
            class="text-xs text-gray-500 truncate"
            x-text="producto.marca"
        ></p>

        <p
            x-show="!producto.marca && producto.categoria"
            class="text-xs text-gray-500 truncate"
            x-text="producto.categoria"
        ></p>

        <p
            class="text-sm font-bold text-red-800"
            x-text="'$' + producto.precio"
        ></p>

    </div>


    {{-- Flecha --}}
    <svg
        xmlns="http://www.w3.org/2000/svg"
        class="w-4 h-4 text-gray-400 shrink-0"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        stroke-width="2"
    >

        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M9 5l7 7-7 7"
        />

    </svg>

</a>


</template>



                        {{-- =================================================
                             SIN RESULTADOS
                        ================================================== --}}

                        <div
                            x-show="!cargando && productos.length === 0"
                            class="px-4 py-6 text-center"
                        >

                            <p class="text-sm text-gray-500">
                                No se encontraron productos.
                            </p>

                            <p class="text-xs text-gray-400 mt-1">
                                Intenta con otro nombre, marca o modelo.
                            </p>

                        </div>

                    </div>


                    {{-- =================================================
                         VER TODOS
                    ================================================== --}}

                    <button
                        type="button"
                        x-show="productos.length > 0"
                        @click="irAlCatalogo()"
                        class="w-full px-4 py-3
                               text-sm font-medium
                               text-red-800
                               hover:bg-red-50
                               border-t border-gray-200
                               transition"
                    >

                        Ver todos los resultados →

                    </button>

                </div>

            </div>

        </div>


        {{-- =================================================
             ACCIONES
        ================================================== --}}

        <div class="ml-auto flex items-center gap-3 sm:gap-5 shrink-0">


            {{-- =================================================
                 MODO OSCURO
            ================================================== --}}

            <button
                type="button"
                @click="oscuro = !oscuro"
                class="text-gray-600 hover:text-red-800 transition"
                aria-label="Cambiar a modo oscuro"
            >

                <svg
                    x-show="!oscuro"
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.36 6.36l-.7-.7M6.34 6.34l-.7-.7m12.02 0l-.7.7M6.34 17.66l-.7.7M16 12a4 4 0 11-8 0 4 4 0 018 0z"
                    />
                </svg>

                <svg
                    x-show="oscuro"
                    x-cloak
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                </svg>

            </button>


            {{-- =================================================
                 UBICACIÓN
            ================================================== --}}

            <span
                class="hidden lg:flex items-center gap-1.5
                       text-sm text-gray-600"
            >

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"
                    />

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                    />

                </svg>

                San Salvador

            </span>


        </div>

    </div>

</div>


</header>

{{-- =========================================================
NAVEGACIÓN DE CATEGORÍAS
========================================================= --}}

<nav class="bandek-catnav relative">


<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="bandek-catnav-list">

        <a href="{{ route('catalogo.index') }}" class="bandek-catnav-lead">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            Categorías
        </a>

        @foreach (
            $categoriasNav ??
            \App\Models\Categoria::whereNull('categoria_padre_id')
                ->where('activo', true)
                ->with(['subcategorias' => function ($q) {
                    $q->where('activo', true);
                }])
                ->get()
            as $categoria
        )

            <div class="bandek-nav-item relative shrink-0">

                <a
                    href="{{ route('catalogo.index', ['categoria' => $categoria->slug]) }}"
                    class="bandek-catnav-link"
                >

                    {{ $categoria->nombre }}

                    @if ($categoria->subcategorias->isNotEmpty())
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    @endif

                </a>

                @if ($categoria->subcategorias->isNotEmpty())

                    <div class="bandek-nav-dropdown">
                        @foreach ($categoria->subcategorias as $sub)
                            <a
                                href="{{ route('catalogo.index', ['categoria' => $sub->slug]) }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-red-800 whitespace-nowrap"
                            >
                                {{ $sub->nombre }}
                            </a>
                        @endforeach
                    </div>

                @endif

            </div>

        @endforeach

    </div>

</div>


</nav>

<style>
    .bandek-catnav {
        background: #991b1b;
        border-top: 1px solid rgba(255,255,255,0.1);
    }
    .bandek-catnav-list {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
    }
    .bandek-catnav-lead {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #fff;
        font-weight: 700;
        font-size: 0.875rem;
        padding: 0.85rem 1.25rem;
        background: rgba(0,0,0,0.18);
        white-space: nowrap;
        margin-right: 0.25rem;
    }
    .bandek-catnav-lead:hover {
        background: rgba(0,0,0,0.3);
    }
    .bandek-catnav-link {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        color: #fff;
        font-weight: 600;
        font-size: 0.875rem;
        padding: 0.85rem 1rem;
        white-space: nowrap;
        transition: background 0.15s ease;
    }
    .bandek-catnav-link:hover {
        background: rgba(0,0,0,0.15);
    }
    .bandek-nav-dropdown {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        min-width: 200px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        padding: 0.5rem 0;
        z-index: 40;
    }
    .bandek-nav-item:hover .bandek-nav-dropdown {
        display: block;
    }
</style>

{{-- =========================================================
CONTENIDO
========================================================= --}}

<main>


{{ $slot }}


</main>

{{-- =========================================================
FOOTER
========================================================= --}}

<footer class="bg-gray-900">

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-12">

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4
                gap-8 sm:gap-10">


        {{-- Marca --}}

        <div>

            <div class="flex items-center gap-3 mb-5">

                <img src="{{ asset('img/logo-icono.png') }}" alt="Ferretería BANDEK" class="w-10 h-10 object-contain">

                <span
                    class="text-xl font-bold text-white tracking-tight"
                >
                    BANDEK
                </span>

            </div>

            <p class="text-sm text-gray-400 leading-6 max-w-sm">

                Soluciones y materiales para tus proyectos de construcción,
                mantenimiento y electricidad. Atención profesional y
                asesoría para ayudarte a encontrar el producto adecuado.

            </p>

        </div>


        {{-- Navegación --}}

        <div>

            <h3
                class="text-sm font-semibold text-white uppercase
                       tracking-wider mb-5"
            >
                Navegación
            </h3>

            <ul class="space-y-3 text-sm">

                <li>
                    <a
                        href="{{ route('inicio') }}"
                        class="text-gray-400 hover:text-white transition"
                    >
                        Inicio
                    </a>
                </li>

                <li>
                    <a
                        href="{{ route('catalogo.index') }}"
                        class="text-gray-400 hover:text-white transition"
                    >
                        Catálogo
                    </a>
                </li>

                <li>
                    <a
                        href="{{ route('catalogo.index') }}"
                        class="text-gray-400 hover:text-white transition"
                    >
                        Productos
                    </a>
                </li>

                <li>
                    <a
                        href="{{ route('nosotros') }}"
                        class="text-gray-400 hover:text-white transition"
                    >
                        Nosotros
                    </a>
                </li>

                <li>
                    <a
                        href="{{ route('login') }}"
                        class="text-gray-400 hover:text-white transition"
                    >
                        Iniciar sesión
                    </a>
                </li>

            </ul>

        </div>


        {{-- Información --}}

        <div>

            <h3
                class="text-sm font-semibold text-white uppercase
                       tracking-wider mb-5"
            >
                Información
            </h3>

            <ul class="space-y-4 text-sm">


                {{-- Dirección --}}

                <li class="flex items-start gap-3">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 text-red-500 shrink-0 mt-0.5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 21s7-4.35 7-10a7 7 0 10-14 0c0 5.65 7 10 7 10z"
                        />

                        <circle cx="12" cy="11" r="2.5"/>

                    </svg>

                    <span class="text-gray-400">
                        {{ $direccionSitio }}
                    </span>

                </li>


                {{-- Teléfono --}}

                <li class="flex items-start gap-3">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 text-red-500 shrink-0 mt-0.5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M22 16.92v3a2 2 0 01-2.18 2
                            19.79 19.79 0 01-8.63-3.07
                            19.5 19.5 0 01-6-6
                            19.79 19.79 0 01-3.07-8.67
                            A2 2 0 014.11 2h3a2 2 0 012
                            1.72c.12.9.33 1.78.62 2.63
                            a2 2 0 01-.45 2.11L8 9.73
                            a16 16 0 006 6l1.27-1.27
                            a2 2 0 012.11-.45
                            c.85.29 1.73.5 2.63.62
                            A2 2 0 0122 16.92z"
                        />

                    </svg>

                    @if ($telefonoSitio)
                        <a
                            href="tel:{{ preg_replace('/[^0-9+]/', '', $telefonoSitio) }}"
                            class="text-gray-400 hover:text-white transition"
                        >
                            {{ $telefonoSitio }}
                        </a>
                    @else
                        <span class="text-gray-500">
                            Próximamente
                        </span>
                    @endif

                </li>


                {{-- WhatsApp --}}

                <li class="flex items-start gap-3">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 text-green-500 shrink-0 mt-0.5"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                    >

                        <path
                            d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.87 9.87 0 004.74 1.21h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2z"
                        />

                    </svg>

                    <a
                        href="https://wa.me/{{ $whatsappNumero }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-gray-400 hover:text-white transition"
                    >
                        WhatsApp
                    </a>

                </li>

            </ul>

        </div>


        {{-- Horario --}}

        <div>

            <h3
                class="text-sm font-semibold text-white uppercase
                       tracking-wider mb-5"
            >
                Horario de atención
            </h3>

            <div class="flex items-start gap-3 mb-5">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5 text-red-500 shrink-0 mt-0.5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 8v4l3 2"
                    />

                    <circle cx="12" cy="12" r="9"/>

                </svg>

                <div class="text-sm">

                    <p class="text-gray-300 font-medium">
                        Lunes a sábado
                    </p>

                    <p class="text-gray-500 mt-1">
                        8:00 AM — 5:00 PM
                    </p>

                    <p class="text-gray-500 mt-1">
                        Domingo cerrado
                    </p>

                </div>

            </div>


            <a
                href="https://wa.me/{{ $whatsappNumero }}"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center justify-center
                       bg-red-700 hover:bg-red-800 text-white
                       text-sm font-medium px-5 py-2.5 rounded-md
                       transition w-full sm:w-auto"
            >

                Contactar por WhatsApp

            </a>

        </div>

    </div>


    {{-- Línea inferior --}}

    <div class="border-t border-gray-800 mt-10 pt-6">

        <div
            class="flex flex-col md:flex-row
                   justify-between items-center
                   text-center md:text-left
                   gap-3 text-xs text-gray-500"
        >

            <p>
                &copy; {{ date('Y') }} Ferretería BANDEK.
                Todos los derechos reservados.
            </p>

            <p>
                Materiales eléctricos y ferretería
            </p>

        </div>

    </div>

</div>


</footer>

{{-- =========================================================
BOTÓN FLOTANTE WHATSAPP
========================================================= --}}

<a
href="https://wa.me/{{ $whatsappNumero }}"
target="_blank"
rel="noopener noreferrer"
class="fixed bottom-5 right-5 z-40
bg-green-500 hover:bg-green-600
text-white rounded-full
w-12 h-12 sm:w-14 sm:h-14
flex items-center justify-center
shadow-lg transition"
aria-label="Contactar por WhatsApp"

>

<svg
    xmlns="http://www.w3.org/2000/svg"
    class="w-5 h-5 sm:w-6 sm:h-6"
    fill="currentColor"
    viewBox="0 0 24 24"
>

    <path
        d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.87 9.87 0 004.74 1.21h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2z"
    />

</svg>


</a>

{{-- =========================================================
ALPINE.JS
BUSCADOR PRINCIPAL
========================================================= --}}

<script>

    function buscadorPrincipal() {

        return {

            buscar: '',
            productos: [],
            cargando: false,
            abierto: false,


            async buscarProductos() {

                const texto = this.buscar.trim();


                // Si no hay texto, limpiar resultados

                if (texto.length === 0) {

                    this.productos = [];
                    this.cargando = false;

                    return;

                }


                this.abierto = true;
                this.cargando = true;


                try {

                    const params = new URLSearchParams();

                    params.append('q', texto);


                    const response = await fetch(
                        '{{ route('catalogo.buscar') }}?' + params.toString(),
                        {
                            method: 'GET',

                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }
                    );


                    if (!response.ok) {

                        throw new Error(
                            'Error HTTP: ' + response.status
                        );

                    }


                    const data = await response.json();


                    this.productos = data.productos || [];


                } catch (error) {

                    console.error(
                        'Error en búsqueda:',
                        error
                    );

                    this.productos = [];


                } finally {

                    this.cargando = false;

                }

            },


            irAlCatalogo() {

                const texto = this.buscar.trim();


                if (texto.length > 0) {

                    window.location.href =
                        '{{ route('catalogo.index') }}?buscar='
                        + encodeURIComponent(texto);

                } else {

                    window.location.href =
                        '{{ route('catalogo.index') }}';

                }

            },


            cerrar() {

                this.abierto = false;

            }

        };

    }

</script>
