<x-layouts.tienda
    :title="'Ferretería BANDEK — Inicio'"
    :description="'Herramientas, materiales de construcción y soluciones eléctricas. Asesoría técnica y atención por WhatsApp.'"
>

    @php
        $waNumero = \App\Models\Configuracion::where('clave', 'whatsapp_numero')->value('valor');
    @endphp

    <h1 class="sr-only">Ferretería BANDEK — Herramientas, materiales de construcción y soluciones eléctricas</h1>

    @if ($bannersPrincipal->isNotEmpty())

        <section class="bg-white">
            <div class="max-w-7xl mx-auto px-4 py-4 sm:py-6">

                <div class="grid {{ $bannersSecundario->isNotEmpty() ? 'lg:grid-cols-3' : '' }} gap-3 sm:gap-4">

                    {{-- BANNER PRINCIPAL --}}
                    <div class="{{ $bannersSecundario->isNotEmpty() ? 'lg:col-span-2' : '' }} relative"
                         x-data="bannerCarrusel({{ $bannersPrincipal->count() }})"
                         x-init="iniciar()"
                    >
                        <div class="bandek-banner aspect-[5/2]">
                            @foreach ($bannersPrincipal as $i => $banner)
                                <div
                                    x-show="actual === {{ $i }}"
                                    x-transition:enter="transition ease-out duration-500"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-300"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="bandek-banner-slide"
                                >
                                    @if ($banner->link)<a href="{{ $banner->link }}" class="block w-full h-full">@endif
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($banner->imagen) }}" alt="{{ $banner->titulo ?? 'Banner' }}" class="w-full h-full object-contain">
                                        @if ($banner->titulo)
                                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent flex items-end pointer-events-none">
                                                <p class="text-white text-base sm:text-xl font-bold px-4 sm:px-6 pb-3 sm:pb-4 max-w-lg leading-tight">{{ $banner->titulo }}</p>
                                            </div>
                                        @endif
                                    @if ($banner->link)</a>@endif
                                </div>
                            @endforeach
                        </div>

                        @if ($bannersPrincipal->count() > 1)
                            <button type="button" @click="anterior()" aria-label="Anterior" class="absolute top-1/2 -translate-y-1/2 left-2 bg-white/80 hover:bg-white text-gray-800 rounded-full w-8 h-8 flex items-center justify-center shadow-soft transition duration-200 ease-emil active:scale-90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                            </button>
                            <button type="button" @click="siguiente()" aria-label="Siguiente" class="absolute top-1/2 -translate-y-1/2 right-2 bg-white/80 hover:bg-white text-gray-800 rounded-full w-8 h-8 flex items-center justify-center shadow-soft transition duration-200 ease-emil active:scale-90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                            </button>
                            <div class="absolute bottom-2 inset-x-0 flex justify-center gap-1.5">
                                <template x-for="i in total" :key="i">
                                    <button type="button" @click="irA(i - 1)" class="w-1.5 h-1.5 rounded-full transition" :class="actual === i - 1 ? 'bg-white' : 'bg-white/50'"></button>
                                </template>
                            </div>
                        @endif
                    </div>

                    {{-- BANNER SECUNDARIO --}}
                    @if ($bannersSecundario->isNotEmpty())
                        <div class="relative"
                             x-data="bannerCarrusel({{ $bannersSecundario->count() }})"
                             x-init="iniciar()"
                        >
                            <div class="bandek-banner aspect-[5/2] lg:aspect-[5/4]">
                                @foreach ($bannersSecundario as $i => $banner)
                                    <div
                                        x-show="actual === {{ $i }}"
                                        x-transition:enter="transition ease-out duration-500"
                                        x-transition:enter-start="opacity-0"
                                        x-transition:enter-end="opacity-100"
                                        x-transition:leave="transition ease-in duration-300"
                                        x-transition:leave-start="opacity-100"
                                        x-transition:leave-end="opacity-0"
                                        class="bandek-banner-slide"
                                    >
                                        @if ($banner->link)<a href="{{ $banner->link }}" class="block w-full h-full">@endif
                                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($banner->imagen) }}" alt="{{ $banner->titulo ?? 'Banner' }}" class="w-full h-full object-contain">
                                            @if ($banner->titulo)
                                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent flex items-end pointer-events-none">
                                                    <p class="text-white text-sm sm:text-base font-bold px-4 pb-3 leading-tight">{{ $banner->titulo }}</p>
                                                </div>
                                            @endif
                                        @if ($banner->link)</a>@endif
                                    </div>
                                @endforeach
                            </div>

                            @if ($bannersSecundario->count() > 1)
                                <div class="absolute bottom-2 inset-x-0 flex justify-center gap-1.5">
                                    <template x-for="i in total" :key="i">
                                        <button type="button" @click="irA(i - 1)" class="w-1.5 h-1.5 rounded-full transition" :class="actual === i - 1 ? 'bg-white' : 'bg-white/50'"></button>
                                    </template>
                                </div>
                            @endif
                        </div>
                    @endif

                </div>

            </div>
        </section>

        <style>
            .bandek-banner { position: relative; width: 100%; background: #f3f4f6; border-radius: 8px; overflow: hidden; }
            .bandek-banner-slide { position: absolute; inset: 0; }
            .bandek-banner-slide img { display: block; width: 100%; height: 100%; }
        </style>

    @else

        <section class="relative bg-amber-50 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 py-16 grid md:grid-cols-2 gap-8 items-center">
                <div>
                    <p class="text-red-800 font-semibold text-sm mb-2">Especial de temporada</p>
                    <h1 class="text-4xl font-bold text-gray-900 mb-4 leading-tight">
                        Todo para construir<br>con confianza
                    </h1>
                    <p class="text-gray-600 mb-6">Herramientas, materiales y soluciones de calidad para tus proyectos.</p>
                    <a href="{{ route('catalogo.index') }}" class="bandek-btn-cta bg-red-800 hover:bg-red-900 text-white font-semibold px-6 py-3 rounded-md inline-block">
                        Conocer ofertas
                    </a>
                </div>
                <div class="hidden md:block">
                    <div class="bg-white border rounded-lg h-64 flex items-center justify-center p-6">
                        <img src="{{ asset('img/logo-completo.png') }}" alt="Ferretería BANDEK" class="h-full w-auto object-contain">
                    </div>
                </div>
            </div>
        </section>

    @endif

    {{-- =========================================================
         COMPRA POR CATEGORÍA
    ========================================================= --}}

    <section class="max-w-7xl mx-auto px-4 pt-12 sm:pt-16">

        <p class="flex items-center justify-center gap-2 text-red-800 font-bold text-xs uppercase tracking-widest mb-2">
            <span class="inline-block w-5 h-[3px] bg-red-800 -skew-x-12"></span>
            Catálogo
        </p>
        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-6 text-center">Compra por categoría</h2>

        @php
            $categoriasPorPagina = 8;
            $paginasCategorias = $categorias->chunk($categoriasPorPagina);
        @endphp

        <div x-data="{ pagina: 0 }">

            <div class="bandek-cat-carrusel">
                <div class="bandek-cat-carrusel-track" :style="`transform: translateX(-${pagina * 100}%)`">
                    @foreach ($paginasCategorias as $grupo)
                        <div class="bandek-cat-row">
                            @foreach ($grupo as $categoria)
                                <a href="{{ route('catalogo.index', ['categoria' => $categoria->slug]) }}" class="bandek-cat-item">

                                    <span class="bandek-cat-circle">
                                        @if ($categoria->imagen)
                                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($categoria->imagen) }}" alt="{{ $categoria->nombre }}">
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.775-3.775a6 6 0 01-7.936 7.936l-6.545 6.545a2.121 2.121 0 01-3-3l6.546-6.546a6 6 0 017.936-7.937l-3.767 3.768z" />
                                            </svg>
                                        @endif
                                    </span>

                                    <span class="bandek-cat-label">{{ $categoria->nombre }}</span>

                                </a>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            @if ($paginasCategorias->count() > 1)
                <div class="flex justify-center gap-2 mt-6">
                    @foreach ($paginasCategorias as $i => $grupo)
                        <button
                            type="button"
                            @click="pagina = {{ $i }}"
                            aria-label="Ir a la página {{ $i + 1 }} de categorías"
                            class="w-2 h-2 rounded-full transition"
                            :class="pagina === {{ $i }} ? 'bg-red-800' : 'bg-gray-300 hover:bg-gray-400'"
                        ></button>
                    @endforeach
                </div>
            @endif

        </div>

    </section>

    <style>
        .bandek-cat-carrusel {
            overflow: hidden;
        }
        .bandek-cat-carrusel-track {
            display: flex;
            transition: transform 0.4s cubic-bezier(0.32, 0.72, 0, 1);
        }
        .bandek-cat-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-content: flex-start;
            gap: 1.75rem 2rem;
            width: 100%;
            flex-shrink: 0;
        }
        .bandek-cat-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.6rem;
            width: 6rem;
            text-align: center;
            text-decoration: none;
        }
        .bandek-cat-circle {
            width: 5.5rem;
            height: 5.5rem;
            border-radius: 9999px;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: #9ca3af;
            transition: border-color 0.25s cubic-bezier(0.32, 0.72, 0, 1), transform 0.25s cubic-bezier(0.32, 0.72, 0, 1), box-shadow 0.25s cubic-bezier(0.32, 0.72, 0, 1);
        }
        .bandek-cat-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .bandek-cat-item:hover .bandek-cat-circle {
            border-color: #991b1b;
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.08);
        }
        .bandek-cat-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #374151;
            line-height: 1.2;
        }
        .bandek-cat-item:hover .bandek-cat-label {
            color: #991b1b;
        }
        @media (min-width: 768px) {
            .bandek-cat-item { width: 7rem; }
            .bandek-cat-circle { width: 6.5rem; height: 6.5rem; }
            .bandek-cat-label { font-size: 0.85rem; }
        }
    </style>

    {{-- =========================================================
         PROMOCIONES
    ========================================================= --}}

    @if ($bannersPromocion->isNotEmpty())

        <section class="max-w-7xl mx-auto px-4 pt-12 sm:pt-16">
            <div class="flex flex-wrap justify-center gap-4">
                @foreach ($bannersPromocion as $banner)
                    <a
                        href="{{ $banner->link ?: route('catalogo.index') }}"
                        class="relative block w-[calc(50%-0.5rem)] sm:w-[calc(33.333%-0.667rem)] lg:w-[calc(25%-0.75rem)] aspect-[5/4] rounded-lg overflow-hidden bg-gray-100 group"
                    >
                        <img
                            src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($banner->imagen) }}"
                            alt="{{ $banner->titulo ?? 'Promoción' }}"
                            class="w-full h-full object-contain transition-transform duration-300 group-hover:scale-105"
                        >
                        @if ($banner->titulo)
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent flex items-end pointer-events-none">
                                <p class="text-white text-sm sm:text-base font-bold px-3 sm:px-4 pb-3 sm:pb-4 leading-tight">{{ $banner->titulo }}</p>
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
        </section>

    @endif

    {{-- =========================================================
         VENTAJAS (franja compacta, no una seccion mas)
    ========================================================= --}}

    <section class="max-w-7xl mx-auto px-4 pt-8 sm:pt-10">

        <div class="bandek-card bg-white border border-gray-100 rounded-2xl shadow-soft px-5 sm:px-8 py-5 sm:py-6">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-y-5 gap-x-4">

                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-800" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.87 9.87 0 004.74 1.21h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2z" />
                        </svg>
                    </span>
                    <p class="text-sm font-semibold text-gray-900 leading-tight">Pedís por WhatsApp</p>
                </div>

                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                            <circle cx="12" cy="11" r="2.5" />
                        </svg>
                    </span>
                    <p class="text-sm font-semibold text-gray-900 leading-tight">Retiro o envío</p>
                </div>

                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.775-3.775a6 6 0 01-7.936 7.936l-6.545 6.545a2.121 2.121 0 01-3-3l6.546-6.546a6 6 0 017.936-7.937l-3.767 3.768z" />
                        </svg>
                    </span>
                    <p class="text-sm font-semibold text-gray-900 leading-tight">Te asesoramos</p>
                </div>

                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    <p class="text-sm font-semibold text-gray-900 leading-tight">Lun-sáb, 8am-5pm</p>
                </div>

            </div>
        </div>

    </section>

    {{-- =========================================================
         CÓMO COMPRAR
    ========================================================= --}}

    <section class="bg-gray-50 mt-14">
        <div class="max-w-7xl mx-auto px-4 py-12 sm:py-14">

            <div class="text-center mb-10">
                <p class="flex items-center justify-center gap-2 text-red-800 font-bold text-xs uppercase tracking-widest mb-2">
                    <span class="inline-block w-5 h-[3px] bg-red-800 -skew-x-12"></span>
                    Proceso simple
                </p>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">¿Cómo comprar en BANDEK?</h2>
                <p class="text-gray-600 max-w-xl mx-auto">Comprar es fácil, directo y sin vueltas: tres pasos y listo.</p>
            </div>

            <div class="grid sm:grid-cols-3 gap-4 sm:gap-6">

                <div class="bandek-card bg-white rounded-2xl shadow-soft p-6 text-center relative">
                    <span class="absolute -top-3 -left-3 w-8 h-8 rounded-full bg-red-800 text-white text-sm font-bold flex items-center justify-center shadow-md">1</span>
                    <div class="mx-auto mb-4 w-14 h-14 rounded-full bg-red-50 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5A6.5 6.5 0 114 10.5a6.5 6.5 0 0113 0z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1.5">Buscá tu producto</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Usa el buscador o navega por categoría hasta encontrar lo que necesitas.</p>
                </div>

                <div class="bandek-card bg-white rounded-2xl shadow-soft p-6 text-center relative">
                    <span class="absolute -top-3 -left-3 w-8 h-8 rounded-full bg-red-800 text-white text-sm font-bold flex items-center justify-center shadow-md">2</span>
                    <div class="mx-auto mb-4 w-14 h-14 rounded-full bg-green-50 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.87 9.87 0 004.74 1.21h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1.5">Escríbenos por WhatsApp</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Confirmamos precio, disponibilidad y resolvemos tus dudas al instante.</p>
                </div>

                <div class="bandek-card bg-white rounded-2xl shadow-soft p-6 text-center relative">
                    <span class="absolute -top-3 -left-3 w-8 h-8 rounded-full bg-red-800 text-white text-sm font-bold flex items-center justify-center shadow-md">3</span>
                    <div class="mx-auto mb-4 w-14 h-14 rounded-full bg-red-50 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h11v8H3zM14 10h4l3 3v2h-7z" />
                            <circle cx="6.5" cy="18.5" r="1.5" />
                            <circle cx="17.5" cy="18.5" r="1.5" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1.5">Recibí tu pedido</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">Coordinamos envío a domicilio o retiro en tienda, como te acomode.</p>
                </div>

            </div>

        </div>
    </section>

    {{-- =========================================================
         LOS MÁS BUSCADOS
    ========================================================= --}}

    <section class="max-w-7xl mx-auto px-4 py-12 sm:py-16">

        <p class="flex items-center gap-2 text-red-800 font-bold text-xs uppercase tracking-widest mb-2">
            <span class="inline-block w-5 h-[3px] bg-red-800 -skew-x-12"></span>
            Selección
        </p>
        <div class="flex items-end justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Los más buscados</h2>
                <p class="text-sm text-gray-500 mt-1">Los productos que más nuestros clientes están pidiendo</p>
            </div>
            <a href="{{ route('catalogo.index') }}" class="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold text-red-800 hover:text-red-900 shrink-0">
                Ver catálogo completo
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
            @forelse ($destacados as $producto)
                @include('catalogo._tarjeta-producto', ['producto' => $producto])
            @empty
                <p class="col-span-full text-gray-500">Todavía no hay productos destacados.</p>
            @endforelse
        </div>

        <a href="{{ route('catalogo.index') }}" class="sm:hidden mt-6 flex items-center justify-center gap-1.5 text-sm font-semibold text-red-800 border border-red-100 bg-red-50 rounded-md py-2.5">
            Ver catálogo completo
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </a>

    </section>

    {{-- =========================================================
         MARCAS
    ========================================================= --}}

    @if ($marcas->isNotEmpty())

        <section class="bg-gray-50 border-y overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 pt-12 sm:pt-14">
                <p class="flex items-center justify-center gap-2 text-red-800 font-bold text-xs uppercase tracking-widest mb-2">
                    <span class="inline-block w-5 h-[3px] bg-red-800 -skew-x-12"></span>
                    Confianza
                </p>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-8 text-center">Marcas con las que trabajamos</h2>
            </div>

            @php
                // Suficientes copias para cubrir pantallas anchas sin huecos,
                // sin pasarnos si ya hay muchas marcas (evita cientos de chips).
                $marqueeSets = max(2, min(10, (int) ceil(5000 / max(1, $marcas->count() * 160))));
                // Ritmo de lectura ~1.8s por marca, para que se alcancen a leer.
                $marqueeDuracion = max(20, $marcas->count() * 1.8);
            @endphp

            <div class="bandek-marquee pb-12 sm:pb-14" style="--bandek-marquee-duracion: {{ $marqueeDuracion }}s; --bandek-marquee-fin: -{{ round(100 / $marqueeSets, 4) }}%;">
                <div class="bandek-marquee-track">
                    @for ($set = 0; $set < $marqueeSets; $set++)
                        @foreach ($marcas as $marca)
                            <a
                                href="{{ route('catalogo.index', ['marca' => $marca->slug]) }}"
                                class="bandek-marquee-chip"
                                @if ($set > 0) aria-hidden="true" tabindex="-1" @endif
                            >
                                {{ $marca->nombre }}
                            </a>
                        @endforeach
                    @endfor
                </div>
            </div>
        </section>

        <style>
            .bandek-marquee {
                position: relative;
                overflow: hidden;
                -webkit-mask-image: linear-gradient(to right, transparent, black 8%, black 92%, transparent);
                mask-image: linear-gradient(to right, transparent, black 8%, black 92%, transparent);
            }
            .bandek-marquee-track {
                display: flex;
                align-items: center;
                gap: 0.85rem;
                width: max-content;
                animation: bandek-marquee-scroll var(--bandek-marquee-duracion, 40s) linear infinite;
            }
            .bandek-marquee:hover .bandek-marquee-track {
                animation-play-state: paused;
            }
            .bandek-marquee-chip {
                flex-shrink: 0;
                background: #fff;
                border: 1px solid #e5e7eb;
                color: #374151;
                font-weight: 700;
                font-size: 0.95rem;
                padding: 0.65rem 1.75rem;
                border-radius: 9999px;
                white-space: nowrap;
                text-decoration: none;
                transition: border-color 0.2s cubic-bezier(0.32, 0.72, 0, 1), color 0.2s cubic-bezier(0.32, 0.72, 0, 1);
            }
            .bandek-marquee-chip:hover {
                border-color: #991b1b;
                color: #991b1b;
            }
            @keyframes bandek-marquee-scroll {
                from { transform: translateX(0); }
                to { transform: translateX(var(--bandek-marquee-fin, -50%)); }
            }
        </style>

    @endif

    {{-- =========================================================
         FRANJA PUBLICITARIA ANCHA
    ========================================================= --}}

    @if ($bannersFranja->isNotEmpty())

        <section class="max-w-7xl mx-auto px-4 pt-12 sm:pt-16">
            <div
                x-data="bannerCarrusel({{ $bannersFranja->count() }})"
                x-init="iniciar()"
                class="relative"
            >
                <div class="bandek-banner aspect-[16/3]">
                    @foreach ($bannersFranja as $i => $banner)
                        <div
                            x-show="actual === {{ $i }}"
                            x-transition:enter="transition ease-out duration-500"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-300"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="bandek-banner-slide"
                        >
                            @if ($banner->link)<a href="{{ $banner->link }}" class="block w-full h-full">@endif
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($banner->imagen) }}" alt="{{ $banner->titulo ?? 'Banner' }}" class="w-full h-full object-contain">
                            @if ($banner->link)</a>@endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

    @endif

    {{-- =========================================================
         PROYECTOS Y EMPRESAS
    ========================================================= --}}

    <section class="bg-white">
        <div class="max-w-7xl mx-auto px-4 py-14 sm:py-16">

            <div class="grid md:grid-cols-2 gap-10 items-center">

                <div>
                    <p class="flex items-center gap-2 text-red-800 font-bold text-xs uppercase tracking-widest mb-2">
                        <span class="inline-block w-5 h-[3px] bg-red-800 -skew-x-12"></span>
                        Para contratistas y empresas
                    </p>
                    <h2 class="text-gray-900 text-2xl sm:text-3xl font-bold mb-4 leading-tight">
                        Cotizaciones para proyectos y compras por volumen
                    </h2>
                    <p class="text-gray-600 mb-6 max-w-md leading-relaxed">
                        ¿Eres contratista, constructora o necesitas abastecer un proyecto? Escríbenos los materiales que necesitas y armamos tu cotización.
                    </p>
                    <a
                        href="https://wa.me/{{ $waNumero }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="bandek-btn-cta inline-flex items-center gap-2 bg-red-800 hover:bg-red-900 text-white font-semibold px-6 py-3 rounded-md"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.87 9.87 0 004.74 1.21h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2z" />
                        </svg>
                        Solicitar cotización
                    </a>
                </div>

                <div class="bandek-checklist rounded-2xl p-6 sm:p-8">
                    <ul class="space-y-4 sm:space-y-5">
                        @foreach ([
                            'Cotización a medida',
                            'Entrega coordinada',
                            'Asesoría técnica',
                            'Precios por volumen',
                        ] as $item)
                            <li class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full bg-red-800 flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </span>
                                <span class="bandek-checklist-text font-medium">{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                </div>

            </div>

        </div>
    </section>

    <style>
        /* Definido a mano (no con bg-red-50/text-gray-900 de Tailwind) para
           que el fondo y el texto cambien juntos en modo oscuro — si el
           fondo se queda claro pero el texto se pone casi blanco (que es lo
           que pasa si se mezclan utilidades de Tailwind con las reglas
           globales de body.oscuro), el texto queda ilegible. */
        .bandek-checklist { background: #fef2f2; }
        .bandek-checklist-text { color: #111827; }
        body.oscuro .bandek-checklist { background: rgba(153, 27, 27, 0.18); }
        body.oscuro .bandek-checklist-text { color: #f8fafc; }
    </style>

    {{-- =========================================================
         CTA WHATSAPP
    ========================================================= --}}

    <section class="bg-red-800">
        <div class="max-w-7xl mx-auto px-4 py-12 sm:py-14">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6 text-center sm:text-left">
                <div>
                    <h2 class="text-white text-xl sm:text-2xl font-bold mb-1.5">¿No encuentras lo que buscas?</h2>
                    <p class="text-red-100">Escríbenos por WhatsApp y te ayudamos a encontrarlo al instante.</p>
                </div>
                <a
                    href="https://wa.me/{{ $waNumero }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="bandek-btn-cta shrink-0 inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-red-800 font-semibold px-6 py-3 rounded-md"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.87 9.87 0 004.74 1.21h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2z" />
                    </svg>
                    Contactar por WhatsApp
                </a>
            </div>
        </div>
    </section>

    @if ($bannersPrincipal->isNotEmpty() || $bannersSecundario->isNotEmpty() || $bannersFranja->isNotEmpty())
        <script>
            function bannerCarrusel(total) {
                return {
                    total: total,
                    actual: 0,
                    intervalo: null,

                    iniciar() {
                        if (this.total <= 1) return;

                        this.intervalo = setInterval(() => {
                            this.actual = (this.actual + 1) % this.total;
                        }, 5000);
                    },

                    irA(indice) {
                        this.actual = indice;

                        clearInterval(this.intervalo);
                        this.iniciar();
                    },

                    anterior() {
                        this.irA((this.actual - 1 + this.total) % this.total);
                    },

                    siguiente() {
                        this.irA((this.actual + 1) % this.total);
                    },
                };
            }
        </script>
    @endif

</x-layouts.tienda>
