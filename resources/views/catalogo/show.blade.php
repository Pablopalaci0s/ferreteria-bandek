@php
$imagenPrincipal = $producto->imagen_principal
? asset('storage/' . $producto->imagen_principal)
: asset('img/logo-completo.png');

$ogDescripcion = $producto->descripcion
? \Illuminate\Support\Str::limit(strip_tags($producto->descripcion), 160)
: 'Consultá precio y disponibilidad de ' . $producto->nombre . ' en Ferretería BANDEK.';
@endphp

<x-layouts.tienda
    :title="$producto->nombre . ' — Ferretería BANDEK'"
    :description="$ogDescripcion"
    :image="$imagenPrincipal"
    :type="'product'"
>


<div class="max-w-5xl mx-auto px-4 py-10">

    <a href="{{ route('catalogo.index') }}"
       class="text-sm text-gray-500 hover:text-red-800 mb-6 inline-block">
        &larr; Volver al catálogo
    </a>

    <div class="grid md:grid-cols-2 gap-10">

        {{-- GALERÍA --}}
        <div x-data="{ activa: @js($imagenPrincipal) }">

            {{-- IMAGEN PRINCIPAL --}}
            <div class="w-full h-[450px] rounded-lg border bg-gray-50 flex items-center justify-center overflow-hidden mb-3">

                <img
                    :src="activa"
                    alt="{{ $producto->nombre }}"
                    class="max-w-full max-h-full w-auto h-auto object-contain"
                >

            </div>

            {{-- MINIATURAS --}}
            <div class="flex gap-2 overflow-x-auto pb-2">

                {{-- Imagen principal --}}
                <button
                    type="button"
                    @click="activa = @js($imagenPrincipal)"
                    class="border rounded-md p-0.5 shrink-0"
                >
                    <img
                        src="{{ $imagenPrincipal }}"
                        alt="Imagen principal"
                        class="w-16 h-16 object-cover rounded"
                    >
                </button>

                {{-- Imágenes de galería --}}
                @foreach ($producto->imagenes as $imagen)

                    @php
                        $urlImagen = asset('storage/' . $imagen->ruta);
                    @endphp

                    <button
                        type="button"
                        @click="activa = @js($urlImagen)"
                        class="border rounded-md p-0.5 shrink-0"
                    >
                        <img
                            src="{{ $urlImagen }}"
                            alt="{{ $producto->nombre }}"
                            class="w-16 h-16 object-cover rounded"
                        >
                    </button>

                @endforeach

            </div>

        </div>


        {{-- INFORMACIÓN DEL PRODUCTO --}}
        <div>

            <p class="text-sm text-red-800 font-medium mb-1">
                {{ $producto->categoria->nombre }}
            </p>

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

            <p class="text-3xl font-bold text-gray-900 mb-4">
                ${{ number_format($producto->precio, 2) }}
            </p>

            @if ($producto->descripcion)

                <p class="text-gray-600 mb-6">
                    {{ $producto->descripcion }}
                </p>

            @endif

            @if ($producto->stock > 0)

                <a
                    href="{{ $producto->whatsapp_link }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="bg-green-500 hover:bg-green-600 text-white font-semibold text-center py-3 px-6 rounded-lg inline-block"
                >
                    Comprar por WhatsApp
                </a>

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

</div>


</x-layouts.tienda>
