<div class="bg-white border rounded-lg overflow-hidden flex flex-col relative">

    @if ($producto->en_oferta)
        <span class="absolute top-2 left-2 z-10 bg-red-700 text-white text-[10px] font-bold uppercase px-2 py-1 rounded">
            Oferta
        </span>
    @endif

    <a href="{{ route('catalogo.show', $producto) }}">
        <img
            src="{{ $producto->imagen_principal
                ? asset('storage/' . $producto->imagen_principal)
                : asset('img/logo-completo.png') }}"
            alt="{{ $producto->nombre }}"
            class="w-full h-40 object-cover"
        >
    </a>

    <div class="p-3 flex flex-col flex-1">

        <h3 class="font-medium text-sm mb-1 text-gray-800">
            {{ $producto->nombre }}
        </h3>

        @if ($producto->en_oferta)
            <p class="mb-3">
                <span class="line-through text-gray-400 text-xs block">${{ number_format($producto->precio, 2) }}</span>
                <span class="text-red-700 font-bold">${{ number_format($producto->precio_oferta, 2) }}</span>
            </p>
        @else
            <p class="text-red-800 font-bold mb-3">
                ${{ number_format($producto->precio, 2) }}
            </p>
        @endif

        <a
            href="{{ route('catalogo.show', $producto) }}"
            class="mt-auto bg-red-700 hover:bg-red-800 text-white text-sm font-semibold text-center py-2 rounded-md"
        >
            Comprar ahora
        </a>

    </div>

</div>
