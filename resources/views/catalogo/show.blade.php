@php
$imagenPrincipal = $producto->imagen_principal
? asset('storage/' . $producto->imagen_principal)
: asset('img/logo-completo.png');

$ogDescripcion = $producto->descripcion
? \Illuminate\Support\Str::limit(strip_tags($producto->descripcion), 160)
: 'Consultá precio y disponibilidad de ' . $producto->nombre . ' en Ferretería BANDEK.';

$galeriaImagenes = collect([$imagenPrincipal])
    ->merge($producto->imagenes->map(fn ($img) => asset('storage/' . $img->ruta)))
    ->values();

$waNumero = \App\Models\Configuracion::where('clave', 'whatsapp_numero')->value('valor');

$unidadAbrev = $producto->unidadMedida->abreviatura ?? null;
@endphp

<x-layouts.tienda
    :title="$producto->nombre . ' — Ferretería BANDEK'"
    :description="$ogDescripcion"
    :image="$imagenPrincipal"
    :type="'product'"
>


<div class="max-w-6xl mx-auto px-4 py-10">

    <a href="{{ route('catalogo.index') }}"
       class="text-sm text-gray-500 hover:text-red-800 mb-6 inline-block">
        &larr; Volver al catálogo
    </a>

    <div class="grid md:grid-cols-2 gap-12">

        {{-- GALERÍA --}}
        <div
            class="flex gap-3"
            x-data="{
                imagenes: @js($galeriaImagenes),
                indice: 0,
            }"
        >

            {{-- MINIATURAS VERTICALES --}}
            <div class="flex flex-col items-center gap-2" x-show="imagenes.length > 1" x-cloak>

                <button
                    type="button"
                    @click="indice = (indice - 1 + imagenes.length) % imagenes.length"
                    class="text-gray-400 hover:text-red-800 transition"
                    aria-label="Imagen anterior"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                    </svg>
                </button>

                <div class="flex flex-col gap-2 overflow-y-auto" style="max-height: 340px;">
                    <template x-for="(img, i) in imagenes" :key="i">
                        <button
                            type="button"
                            @click="indice = i"
                            class="border-2 rounded-md p-0.5 shrink-0"
                            :class="indice === i ? 'border-red-700' : 'border-gray-200'"
                        >
                            <img :src="img" class="w-14 h-14 object-cover rounded" alt="Miniatura">
                        </button>
                    </template>
                </div>

                <button
                    type="button"
                    @click="indice = (indice + 1) % imagenes.length"
                    class="text-gray-400 hover:text-red-800 transition"
                    aria-label="Imagen siguiente"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

            </div>

            {{-- IMAGEN PRINCIPAL --}}
            <div class="flex-1 h-[420px] rounded-lg border bg-gray-50 flex items-center justify-center overflow-hidden">
                <img
                    :src="imagenes[indice]"
                    alt="{{ $producto->nombre }}"
                    class="max-w-full max-h-full w-auto h-auto object-contain"
                >
            </div>

        </div>


        {{-- INFORMACIÓN DEL PRODUCTO --}}
        <div>

            <div class="flex items-start justify-between gap-3 mb-1">

                <p class="text-sm text-red-800 font-medium">
                    {{ $producto->categoria->nombre }}
                </p>

                {{-- FAVORITO (guardado en este dispositivo) --}}
                <button
                    type="button"
                    x-data="{ favorito: false }"
                    x-init="favorito = (JSON.parse(localStorage.getItem('bandek-favoritos') || '[]')).includes({{ $producto->id }})"
                    @click="
                        favorito = !favorito;
                        let favs = JSON.parse(localStorage.getItem('bandek-favoritos') || '[]');
                        favs = favorito ? [...favs, {{ $producto->id }}] : favs.filter(id => id !== {{ $producto->id }});
                        localStorage.setItem('bandek-favoritos', JSON.stringify(favs));
                    "
                    class="border rounded-full w-10 h-10 flex items-center justify-center text-gray-400 hover:text-red-700 transition shrink-0"
                    aria-label="Guardar en favoritos"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5"
                        :fill="favorito ? 'currentColor' : 'none'"
                        :class="favorito ? 'text-red-700' : ''"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>

            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-2">
                {{ $producto->nombre }}
            </h1>

            @if ($producto->sku || $producto->modelo)

                <p class="text-xs text-gray-400 mb-3">

                    @if ($producto->sku)
                        SKU: {{ $producto->sku }}
                    @endif

                    @if ($producto->sku && $producto->modelo)
                        &nbsp;·&nbsp;
                    @endif

                    @if ($producto->modelo)
                        Modelo: {{ $producto->modelo }}
                    @endif

                </p>

            @endif

            @if ($producto->descripcion)

                <p class="text-gray-600 mb-4">
                    {{ $producto->descripcion }}
                </p>

            @endif

            {{-- DISPONIBILIDAD --}}
            @if ($producto->stock <= 0)
                <span class="inline-block bg-red-50 text-red-700 text-xs font-semibold px-3 py-1 rounded-full mb-4">
                    Agotado
                </span>
            @elseif ($producto->stock >= 10)
                <span class="inline-block bg-green-50 text-green-700 text-xs font-semibold px-3 py-1 rounded-full mb-4">
                    10+ Disponible(s)
                </span>
            @else
                <span class="inline-block bg-amber-50 text-amber-700 text-xs font-semibold px-3 py-1 rounded-full mb-4">
                    {{ $producto->stock }} Disponible(s) — ¡últimas unidades!
                </span>
            @endif

            @if ($producto->en_oferta)

                <span class="inline-block bg-red-700 text-white text-xs font-bold uppercase px-2 py-1 rounded mb-2">
                    Oferta
                </span>

                <p class="mb-6">
                    <span class="line-through text-gray-400 text-lg block">${{ number_format($producto->precio, 2) }}</span>
                    <span class="text-3xl font-bold text-red-700">
                        ${{ number_format($producto->precio_oferta, 2) }}
                        @if ($unidadAbrev)
                            <span class="text-sm font-normal text-gray-500">x {{ $unidadAbrev }}</span>
                        @endif
                    </span>
                </p>

            @else

                <p class="text-3xl font-bold text-gray-900 mb-6">
                    ${{ number_format($producto->precio, 2) }}
                    @if ($unidadAbrev)
                        <span class="text-sm font-normal text-gray-500">x {{ $unidadAbrev }}</span>
                    @endif
                </p>

            @endif

            @if ($producto->stock > 0)

                <div
                    class="flex items-center gap-3 flex-wrap"
                    x-data="{
                        cantidad: 1,
                        stockMax: {{ max((int) $producto->stock, 1) }},
                        precio: {{ (float) $producto->precio_final }},
                        nombre: @js($producto->nombre),
                        numero: @js($waNumero),
                        mas() { if (this.cantidad < this.stockMax) this.cantidad++ },
                        menos() { if (this.cantidad > 1) this.cantidad-- },
                        get enlaceWhatsapp() {
                            const total = (this.precio * this.cantidad).toFixed(2);
                            const mensaje = 'Hola, quiero pedir: ' + this.cantidad + 'x ' + this.nombre + ' - $' + total;
                            return 'https://wa.me/' + this.numero + '?text=' + encodeURIComponent(mensaje);
                        },
                        agregarAlCarrito() {
                            $store.carrito.agregar({
                                id: {{ $producto->id }},
                                nombre: this.nombre,
                                precio: this.precio,
                                imagen: @js($imagenPrincipal),
                                url: @js(route('catalogo.show', $producto)),
                            }, this.cantidad);
                        },
                    }"
                >

                    <div class="flex items-center border rounded-md">
                        <button type="button" @click="menos()" class="w-9 h-9 flex items-center justify-center text-gray-600 hover:text-red-800 transition" aria-label="Restar">
                            &minus;
                        </button>
                        <span class="w-8 text-center text-sm font-medium" x-text="cantidad"></span>
                        <button type="button" @click="mas()" class="w-9 h-9 flex items-center justify-center text-gray-600 hover:text-red-800 transition" aria-label="Sumar">
                            &plus;
                        </button>
                    </div>

                    <button
                        type="button"
                        @click="agregarAlCarrito()"
                        class="bandek-btn-cta bg-white hover:bg-gray-50 text-gray-800 font-semibold text-center py-3 px-6 rounded-lg border border-gray-300 inline-flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.836l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.907-4.706 2.325-7.183a1.125 1.125 0 00-1.11-1.317H5.106M7.5 14.25L5.106 5.653M7.5 14.25L5.526 5.653" />
                        </svg>
                        Agregar al carrito
                    </button>

                    <a
                        :href="enlaceWhatsapp"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="bandek-btn-cta bg-green-500 hover:bg-green-600 text-white font-semibold text-center py-3 px-6 rounded-lg inline-flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.87 9.87 0 004.74 1.21h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2z" />
                        </svg>
                        Comprar por WhatsApp
                    </a>

                </div>

            @else

                <p class="text-red-700 font-medium">
                    Agotado por el momento.
                </p>

            @endif

        </div>

    </div>


    {{-- DESCRIPCIÓN LARGA --}}
    @if ($producto->descripcion_larga)

        <div class="mt-12 border-t pt-8">

            <h2 class="text-lg font-bold text-gray-900 mb-4">
                Descripción
            </h2>

            <div class="text-gray-600 whitespace-pre-line leading-relaxed">
                {{ $producto->descripcion_larga }}
            </div>

        </div>

    @endif


    {{-- PRODUCTOS RELACIONADOS --}}
    @if ($relacionados->isNotEmpty())

        <div class="mt-12 border-t pt-8">

            <h2 class="text-lg font-bold text-gray-900 mb-6">
                Productos similares
            </h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach ($relacionados as $relacionado)
                    @include('catalogo._tarjeta-producto', ['producto' => $relacionado])
                @endforeach
            </div>

        </div>

    @endif

</div>


</x-layouts.tienda>
