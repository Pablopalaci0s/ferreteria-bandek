@component('admin.layout')

    @slot('header')
        Editar producto
    @endslot

    <a href="{{ route('admin.productos.index') }}"
       class="text-sm text-gray-500 hover:text-red-800 mb-4 inline-block">
        &larr; Volver a productos
    </a>

    @php $solicitudPendiente = $producto->solicitudPrecioPendiente(); @endphp

    @if ($solicitudPendiente)
        <div class="bg-yellow-50 text-yellow-800 border border-yellow-200 px-4 py-3 rounded-md mb-4 text-sm max-w-2xl">
            Hay un cambio de precio pendiente de aprobación para este producto:
            precio actual <strong>${{ number_format($solicitudPendiente->precio_actual, 2) }}</strong>
            &rarr; propuesto <strong>${{ number_format($solicitudPendiente->precio_nuevo, 2) }}</strong>.
            @if (auth()->user()->rol === 'admin')
                Podés revisarlo en
                <a href="{{ route('admin.solicitudes-precio.index') }}" class="underline font-medium">Solicitudes de precio</a>.
            @else
                Todavía no fue revisado por un administrador. Si volvés a cambiar el precio, se actualizará esta misma solicitud.
            @endif
        </div>
    @endif

    {{-- Formulario principal del producto --}}
    <form
        action="{{ route('admin.productos.update', $producto) }}"
        method="POST"
        enctype="multipart/form-data"
        class="bg-white p-6 rounded-lg border max-w-2xl"
    >
        @csrf
        @method('PUT')

        @include('admin.productos._form')

        <button
            type="submit"
            class="w-full bg-red-800 hover:bg-red-900 text-white font-medium py-2.5 rounded-md"
        >
            Actualizar producto
        </button>
    </form>

    {{-- Galería de imágenes --}}
    <div class="bg-white p-6 rounded-lg border max-w-2xl mt-6">

        <h2 class="text-sm font-semibold text-gray-700 mb-4">
            Galería de imágenes
        </h2>

        @if (session('status'))
            <div class="bg-green-50 text-green-800 border border-green-200 px-4 py-2 rounded-md mb-4 text-sm">
                {{ session('status') }}
            </div>
        @endif

        @error('imagenes')
            <p class="text-red-600 text-xs mb-4">
                {{ $message }}
            </p>
        @enderror

        @error('imagenes.0')
            <p class="text-red-600 text-xs mb-4">
                {{ $message }}
            </p>
        @enderror

        {{-- Imágenes existentes --}}
        @if ($producto->imagenes->isNotEmpty())

            <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 mb-6">

                @foreach ($producto->imagenes as $imagen)

                    <div class="relative group">

                        <img
                            src="{{ asset('storage/' . $imagen->ruta) }}"
                            class="w-full h-24 object-cover rounded-md border"
                            alt="Imagen del producto"
                        >

                        {{-- Formulario para eliminar --}}
                        <form
                            action="{{ route('admin.productos.imagenes.destroy', [$producto, $imagen]) }}"
                            method="POST"
                            class="absolute top-1 right-1"
                            onsubmit="return confirm('¿Eliminar esta imagen?');"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="bg-white/90 hover:bg-red-800 hover:text-white text-gray-600 text-xs w-6 h-6 rounded-full border shadow-sm"
                            >
                                &times;
                            </button>
                        </form>

                    </div>

                @endforeach

            </div>

        @else

            <p class="text-sm text-gray-400 mb-6">
                Todavía no hay imágenes en la galería.
            </p>

        @endif

        {{-- Formulario para subir imágenes --}}
        <form
            action="{{ route('admin.productos.imagenes.store', $producto) }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf

            <label class="block text-sm font-medium text-gray-700 mb-1">
                Agregar imágenes
            </label>

            <input
                type="file"
                name="imagenes[]"
                multiple
                accept="image/*"
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm mb-3"
            >

            <p class="text-xs text-gray-400 mb-4">
                Podés seleccionar varias imágenes a la vez (máx. 2MB cada una).
            </p>

            <button
                type="submit"
                class="bg-red-800 hover:bg-red-900 text-white text-sm font-medium px-4 py-2 rounded-md"
            >
                Subir imágenes
            </button>

        </form>

    </div>

@endcomponent