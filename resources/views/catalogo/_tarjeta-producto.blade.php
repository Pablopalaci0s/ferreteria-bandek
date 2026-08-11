<div class="bg-white border rounded-lg overflow-hidden flex flex-col">

    <a href="{{ route('catalogo.show', $producto) }}">
        <img
            src="{{ $producto->imagen_principal
                ? asset('storage/' . $producto->imagen_principal)
                : 'https://placehold.co/300x300?text=BANDEK' }}"
            alt="{{ $producto->nombre }}"
            class="w-full h-40 object-cover"
        >
    </a>

    <div class="p-3 flex flex-col flex-1">

        <h3 class="font-medium text-sm mb-1 text-gray-800">
            {{ $producto->nombre }}
        </h3>

        <p class="text-red-800 font-bold mb-3">
            ${{ number_format($producto->precio, 2) }}
        </p>

        <a
            href="{{ $producto->whatsapp_link }}"
            target="_blank"
            class="mt-auto bg-green-500 hover:bg-green-600 text-white text-sm text-center py-2 rounded-md"
        >
            Comprar por WhatsApp
        </a>

    </div>

</div>