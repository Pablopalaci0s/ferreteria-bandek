<x-layouts.tienda :title="'Ferretería BANDEK — Inicio'">

    @if ($banners->isNotEmpty())

        <style>
            .bandek-banner { position: relative; width: 100%; height: 260px; background: #f3f4f6; border-radius: 8px; overflow: hidden; }
            @media (min-width: 640px) { .bandek-banner { height: 340px; } }
            @media (min-width: 768px) { .bandek-banner { height: 420px; } }
            .bandek-banner-slide { position: absolute; inset: 0; }
            .bandek-banner-slide img { display: block; width: 100%; height: 100%; object-fit: contain; }
        </style>

        <section
            class="relative bg-white"
            x-data="bannerCarrusel({{ $banners->count() }})"
            x-init="iniciar()"
        >

            <div class="max-w-7xl mx-auto px-4 sm:px-10 py-6 sm:py-8">

                <div class="relative">

                    <div class="bandek-banner">

                        @foreach ($banners as $i => $banner)

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
                                @if ($banner->link)
                                    <a href="{{ $banner->link }}" class="block w-full h-full">
                                @endif

                                <img
                                    src="{{ asset('storage/' . $banner->imagen) }}"
                                    alt="{{ $banner->titulo ?? 'Banner' }}"
                                    class="w-full h-full object-contain"
                                >

                                @if ($banner->titulo)
                                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent flex items-end pointer-events-none">
                                        <h1 class="text-white text-lg sm:text-2xl md:text-3xl font-bold px-4 sm:px-8 pb-4 sm:pb-6 max-w-2xl leading-tight">
                                            {{ $banner->titulo }}
                                        </h1>
                                    </div>
                                @endif

                                @if ($banner->link)
                                    </a>
                                @endif
                            </div>

                        @endforeach

                    </div>

                    @if ($banners->count() > 1)

                        <button
                            type="button"
                            @click="anterior()"
                            aria-label="Anterior"
                            class="absolute top-1/2 -translate-y-1/2 -left-2 sm:-left-10 text-gray-800 hover:text-red-800 transition"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 sm:w-9 sm:h-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>

                        <button
                            type="button"
                            @click="siguiente()"
                            aria-label="Siguiente"
                            class="absolute top-1/2 -translate-y-1/2 -right-2 sm:-right-10 text-gray-800 hover:text-red-800 transition"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 sm:w-9 sm:h-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                    @endif

                </div>

                @if ($banners->count() > 1)

                    <div class="flex justify-center gap-2 mt-4">
                        <template x-for="i in total" :key="i">
                            <button
                                type="button"
                                @click="irA(i - 1)"
                                class="w-2.5 h-2.5 rounded-full transition"
                                :class="actual === i - 1 ? 'bg-red-800' : 'bg-gray-300 hover:bg-gray-400'"
                            ></button>
                        </template>
                    </div>

                @endif

            </div>

        </section>

    @else

        <section class="relative bg-amber-50 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 py-16 grid md:grid-cols-2 gap-8 items-center">
                <div>
                    <p class="text-red-800 font-semibold text-sm mb-2">Especial de temporada</p>
                    <h1 class="text-4xl font-bold text-gray-900 mb-4 leading-tight">
                        Todo para construir<br>con confianza
                    </h1>
                    <p class="text-gray-600 mb-6">Herramientas, materiales y soluciones de calidad para tus proyectos.</p>
                    <a href="{{ route('catalogo.index') }}" class="bg-red-800 hover:bg-red-900 text-white font-semibold px-6 py-3 rounded-md inline-block">
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

    <section class="max-w-7xl mx-auto px-4 py-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Los más buscados</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @forelse ($destacados as $producto)
                @include('catalogo._tarjeta-producto', ['producto' => $producto])
            @empty
                <p class="col-span-full text-gray-500">Todavía no hay productos destacados.</p>
            @endforelse
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 pb-16">

        <h2 class="text-2xl font-bold text-gray-900 mb-6">Comprá por categoría</h2>

        <div class="bandek-cat-row">
            @foreach ($categorias as $categoria)
                <a href="{{ route('catalogo.index', ['categoria' => $categoria->slug]) }}" class="bandek-cat-item">

                    <span class="bandek-cat-circle">
                        @if ($categoria->imagen)
                            <img src="{{ asset('storage/' . $categoria->imagen) }}" alt="{{ $categoria->nombre }}">
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

    </section>

    <style>
        .bandek-cat-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 2rem 2.5rem;
        }
        .bandek-cat-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            width: 7.5rem;
            text-align: center;
            text-decoration: none;
        }
        .bandek-cat-circle {
            width: 6.5rem;
            height: 6.5rem;
            border-radius: 9999px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: #991b1b;
            transition: border-color 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
        }
        .bandek-cat-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .bandek-cat-item:hover .bandek-cat-circle {
            border-color: #991b1b;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        }
        .bandek-cat-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #1f2937;
        }
        .bandek-cat-item:hover .bandek-cat-label {
            color: #991b1b;
        }
        @media (min-width: 768px) {
            .bandek-cat-item { width: 8.5rem; }
            .bandek-cat-circle { width: 7.5rem; height: 7.5rem; }
        }
    </style>

    @if ($banners->isNotEmpty())
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