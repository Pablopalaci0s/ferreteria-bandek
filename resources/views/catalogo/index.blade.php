<x-layouts.tienda
    :title="'Catálogo — Ferretería BANDEK'"
    :description="'Explora todo el catálogo de Ferretería BANDEK: herramientas, materiales eléctricos y más.'"
>

<div class="max-w-7xl mx-auto px-4 py-10">

    <h1 class="text-2xl font-bold text-gray-900 mb-6">
        Catálogo
    </h1>

    {{-- FILTROS: navegación normal (GET), asi la paginación del servidor
         siempre funciona. Antes esto disparaba una búsqueda "en vivo" por
         AJAX contra el mismo endpoint del buscador chico del header, que
         está limitado a 12 resultados sin paginación — por eso cualquier
         filtro (categoría, marca, orden...) dejaba ver como mucho 12
         productos sin forma de pasar de página. --}}
    <form method="GET" action="{{ route('catalogo.index') }}" class="flex flex-col md:flex-row mb-8" style="gap: 0.75rem; flex-wrap: wrap;">

        {{-- BUSCADOR --}}
        <div class="relative flex-1">
            <input
                type="text"
                name="buscar"
                value="{{ request('buscar') }}"
                placeholder="Buscar producto..."
                class="border rounded-md px-4 py-2 pr-10 w-full text-sm
                       focus:outline-none focus:ring-1
                       focus:ring-red-700"
            >
        </div>

        {{-- CATEGORÍA --}}
        <select name="categoria" onchange="this.form.submit()" class="border rounded-md px-4 py-2 text-sm">

            <option value="">Todas las categorías</option>

            @foreach ($categorias as $cat)
                <option value="{{ $cat->slug }}" @selected(request('categoria') === $cat->slug)>
                    {{ $cat->nombre }}
                </option>
            @endforeach

        </select>

        {{-- MARCA --}}
        <select name="marca" onchange="this.form.submit()" class="border rounded-md px-4 py-2 text-sm">

            <option value="">Todas las marcas</option>

            @foreach ($marcas as $m)
                <option value="{{ $m->slug }}" @selected(request('marca') === $m->slug)>
                    {{ $m->nombre }}
                </option>
            @endforeach

        </select>

        {{-- ORDEN --}}
        <select name="orden" onchange="this.form.submit()" class="border rounded-md px-4 py-2 text-sm">
            <option value="" @selected(! request('orden'))>Más recientes</option>
            <option value="precio_asc" @selected(request('orden') === 'precio_asc')>Precio: menor a mayor</option>
            <option value="precio_desc" @selected(request('orden') === 'precio_desc')>Precio: mayor a menor</option>
            <option value="nombre_asc" @selected(request('orden') === 'nombre_asc')>Nombre: A-Z</option>
        </select>

        {{-- DISPONIBILIDAD --}}
        <label class="flex items-center gap-2 text-sm text-gray-600 border rounded-md px-4 py-2 whitespace-nowrap cursor-pointer">
            <input
                type="checkbox"
                name="disponible"
                value="1"
                onchange="this.form.submit()"
                @checked(request()->boolean('disponible'))
                class="rounded border-gray-300 text-red-800 focus:ring-red-700"
            >
            Solo en stock
        </label>

        {{-- OFERTA --}}
        <label class="flex items-center gap-2 text-sm text-gray-600 border rounded-md px-4 py-2 whitespace-nowrap cursor-pointer">
            <input
                type="checkbox"
                name="oferta"
                value="1"
                onchange="this.form.submit()"
                @checked(request()->boolean('oferta'))
                class="rounded border-gray-300 text-red-800 focus:ring-red-700"
            >
            Solo ofertas
        </label>

        {{-- LIMPIAR --}}
        @if (request()->anyFilled(['buscar', 'categoria', 'marca', 'orden', 'disponible', 'oferta']))
            <a
                href="{{ route('catalogo.index') }}"
                class="text-sm text-gray-500 hover:text-red-800 px-2 self-center"
            >
                Limpiar filtros
            </a>
        @endif

    </form>

    {{-- RESULTADOS --}}
    <div class="mb-4">
        <p class="text-sm text-gray-500">
            {{ $productos->total() }} resultado(s) encontrado(s)
        </p>

        @if ($sugerencia)
            <p class="text-sm text-gray-600 mt-1">
                ¿Quisiste decir
                <a
                    href="{{ route('catalogo.index', array_merge(request()->except('buscar'), ['buscar' => $sugerencia])) }}"
                    class="text-red-800 font-semibold hover:underline"
                >
                    {{ $sugerencia }}
                </a>?
            </p>
        @endif
    </div>

    {{-- PRODUCTOS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">

        @forelse ($productos as $producto)

            @include('catalogo._tarjeta-producto', ['producto' => $producto])

        @empty

            <p class="col-span-full text-gray-500 text-center py-10">
                No se encontraron productos.
            </p>

        @endforelse

    </div>

    {{-- PAGINACIÓN --}}
    <div class="mt-10">
        {{ $productos->links() }}
    </div>

</div>

</x-layouts.tienda>
