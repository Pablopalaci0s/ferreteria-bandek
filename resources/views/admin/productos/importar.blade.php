@component('admin.layout')

    @slot('header')
        Importar productos
    @endslot

    <a href="{{ route('admin.productos.index') }}"
       class="text-sm text-gray-500 hover:text-red-800 mb-4 inline-block">
        &larr; Volver a productos
    </a>

    @if (session('error'))
        <div class="bg-red-50 text-red-800 border border-red-200 px-4 py-2 rounded-md mb-4 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if (session('resultado_importacion'))

        @php $resultado = session('resultado_importacion'); @endphp

        <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6 text-sm">
            <p class="text-green-800 font-medium mb-1">
                Importación terminada: {{ $resultado['creados'] }} producto(s) creados,
                {{ $resultado['actualizados'] }} actualizados.
            </p>

            @if (count($resultado['errores']) > 0)
                <p class="text-red-700 font-medium mt-3 mb-1">
                    {{ count($resultado['errores']) }} fila(s) con problemas (no se importaron):
                </p>
                <ul class="list-disc list-inside text-red-700 space-y-0.5">
                    @foreach ($resultado['errores'] as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

    @endif

    <div class="bg-white p-6 rounded-lg border max-w-2xl mb-6">

        <h2 class="font-semibold text-gray-800 mb-3">Cómo funciona</h2>

        <ul class="text-sm text-gray-600 space-y-2 mb-5" style="list-style: disc; padding-left: 1.25rem;">
            <li>Descargá la plantilla, llenala en Excel o Google Sheets y guardala/exportala como <strong>CSV (UTF-8)</strong>.</li>
            <li>Las columnas <strong>sku</strong>, <strong>nombre</strong>, <strong>precio</strong>, <strong>categoria</strong> y <strong>unidad_medida</strong> son obligatorias.</li>
            <li>Si el SKU ya existe, el producto se <strong>actualiza</strong>; si no existe, se <strong>crea</strong>.</li>
            <li>La categoría y la marca se crean solas si escribís un nombre que todavía no existe. La unidad de medida tiene que existir de antes.</li>
            <li>El <strong>stock</strong> solo se usa al crear un producto nuevo. Para productos que ya existen, el stock no se toca acá — se maneja desde Inventario.</li>
            <li>Las fotos no se importan por CSV — se suben después, producto por producto.</li>
        </ul>

        <a href="{{ route('admin.productos.plantilla') }}"
           class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded-md">
            Descargar plantilla CSV
        </a>

    </div>

    <form
        action="{{ route('admin.productos.importar.procesar') }}"
        method="POST"
        enctype="multipart/form-data"
        class="bg-white p-6 rounded-lg border max-w-2xl"
    >
        @csrf

        <label class="block text-sm font-medium text-gray-700 mb-1">
            Archivo CSV
        </label>

        <input
            type="file"
            name="archivo"
            accept=".csv,text/csv,text/plain"
            required
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm mb-1"
        >

        @error('archivo')
            <p class="text-red-600 text-xs mt-1 mb-3">{{ $message }}</p>
        @enderror

        <p class="text-xs text-gray-400 mb-5">Máximo 5 MB.</p>

        <button
            type="submit"
            class="w-full bg-red-800 hover:bg-red-900 text-white font-medium py-2.5 rounded-md"
        >
            Importar
        </button>
    </form>

@endcomponent
