<div class="bandek-card group bg-white border border-gray-200 rounded-lg overflow-hidden flex flex-col relative">

    <div class="absolute top-2 left-2 right-2 z-10 flex items-start justify-between gap-2 pointer-events-none">

        @if ($producto->en_oferta)
            <span class="bg-red-700 text-white text-[10px] font-bold uppercase px-2 py-1 rounded shadow-sm">
                Oferta
            </span>
        @else
            <span></span>
        @endif

        @if ($producto->stock <= 0)
            <span class="bg-gray-800 text-white text-[10px] font-bold uppercase px-2 py-1 rounded shadow-sm">
                Agotado
            </span>
        @endif

    </div>

    <a href="{{ route('catalogo.show', $producto) }}" class="block bg-gray-50 aspect-square overflow-hidden">
        <img
            src="{{ $producto->imagen_principal
                ? asset('storage/' . $producto->imagen_thumb)
                : asset('img/logo-completo.png') }}"
            alt="{{ $producto->nombre }}"
            loading="lazy"
            class="w-full h-full object-contain p-4 transition-transform duration-500 ease-emil group-hover:scale-105 {{ $producto->stock <= 0 ? 'opacity-50' : '' }}"
        >
    </a>

    <div class="p-3 sm:p-4 flex flex-col flex-1">

        @if ($producto->categoria)
            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-1 truncate">
                {{ $producto->categoria->nombre }}
            </p>
        @endif

        <h3 class="font-medium text-sm text-gray-800 leading-snug mb-2 line-clamp-2 min-h-[2.5rem]">
            {{ $producto->nombre }}
        </h3>

        @if ($producto->en_oferta)
            <p class="mb-3">
                <span class="line-through text-gray-400 text-xs block">${{ number_format($producto->precio, 2) }}</span>
                <span class="text-red-700 font-bold text-lg">${{ number_format($producto->precio_oferta, 2) }}</span>
            </p>
        @else
            <p class="text-red-800 font-bold text-lg mb-3">
                ${{ number_format($producto->precio, 2) }}
            </p>
        @endif

        <div class="mt-auto flex gap-2">

            <a
                href="{{ route('catalogo.show', $producto) }}"
                class="bandek-btn-cta flex-1 bg-red-700 hover:bg-red-800 text-white text-sm font-semibold text-center py-2 rounded-md inline-flex items-center justify-center gap-1.5"
            >
                Ver producto
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            @if ($producto->stock > 0)
                <button
                    type="button"
                    @click="$store.carrito.agregar({
                        id: {{ $producto->id }},
                        nombre: @js($producto->nombre),
                        precio: {{ (float) $producto->precio_final }},
                        imagen: @js($producto->imagen_principal ? asset('storage/' . $producto->imagen_thumb) : asset('img/logo-completo.png')),
                        url: @js(route('catalogo.show', $producto)),
                    })"
                    class="shrink-0 w-10 flex items-center justify-center border border-gray-300 hover:border-red-800 hover:text-red-800 text-gray-600 rounded-md transition duration-200 ease-emil active:scale-90"
                    aria-label="Agregar al carrito"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.836l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.907-4.706 2.325-7.183a1.125 1.125 0 00-1.11-1.317H5.106M7.5 14.25L5.106 5.653M7.5 14.25L5.526 5.653M9 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm9 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v4.5m2.25-2.25h-4.5" />
                    </svg>
                </button>
            @endif

        </div>

    </div>

</div>
