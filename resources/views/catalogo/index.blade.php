<x-layouts.tienda
    :title="'Catálogo — Ferretería BANDEK'"
    :description="'Explorá todo el catálogo de Ferretería BANDEK: herramientas, materiales eléctricos y más.'"
>

<div
    class="max-w-7xl mx-auto px-4 py-10"
    x-data="buscadorCatalogo()"
    x-init="init()"
>

    <h1 class="text-2xl font-bold text-gray-900 mb-6">
        Catálogo
    </h1>

    {{-- FILTROS --}}
    <div class="flex flex-col md:flex-row mb-8" style="gap: 0.75rem; flex-wrap: wrap;">

        {{-- BUSCADOR --}}
        <div class="relative flex-1">

            <input
                type="text"
                x-model="buscar"
                @input.debounce.350ms="buscarProductos()"
                placeholder="Buscar producto..."
                class="border rounded-md px-4 py-2 pr-10 w-full text-sm
                       focus:outline-none focus:ring-1
                       focus:ring-red-700"
            >

            {{-- Spinner --}}
            <div
                x-show="cargando"
                x-cloak
                class="absolute right-3 top-1/2
                       -translate-y-1/2"
            >
                <svg
                    class="animate-spin h-4 w-4 text-red-800"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    ></circle>

                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                    ></path>
                </svg>
            </div>

        </div>


        {{-- CATEGORÍA --}}
        <select
            x-model="categoria"
            @change="buscarProductos()"
            class="border rounded-md px-4 py-2 text-sm"
        >

            <option value="">
                Todas las categorías
            </option>

            @foreach ($categorias as $categoria)

                <option value="{{ $categoria->slug }}">
                    {{ $categoria->nombre }}
                </option>

            @endforeach

        </select>


        {{-- MARCA --}}
        <select
            x-model="marca"
            @change="buscarProductos()"
            class="border rounded-md px-4 py-2 text-sm"
        >

            <option value="">
                Todas las marcas
            </option>

            @foreach ($marcas as $marca)

                <option value="{{ $marca->slug }}">
                    {{ $marca->nombre }}
                </option>

            @endforeach

        </select>


        {{-- ORDEN --}}
        <select
            x-model="orden"
            @change="buscarProductos()"
            class="border rounded-md px-4 py-2 text-sm"
        >
            <option value="">Más recientes</option>
            <option value="precio_asc">Precio: menor a mayor</option>
            <option value="precio_desc">Precio: mayor a menor</option>
            <option value="nombre_asc">Nombre: A-Z</option>
        </select>


        {{-- DISPONIBILIDAD --}}
        <label class="flex items-center gap-2 text-sm text-gray-600 border rounded-md px-4 py-2 whitespace-nowrap cursor-pointer">
            <input
                type="checkbox"
                x-model="disponible"
                @change="buscarProductos()"
                class="rounded border-gray-300 text-red-800 focus:ring-red-700"
            >
            Solo en stock
        </label>


        {{-- OFERTA --}}
        <label class="flex items-center gap-2 text-sm text-gray-600 border rounded-md px-4 py-2 whitespace-nowrap cursor-pointer">
            <input
                type="checkbox"
                x-model="oferta"
                @change="buscarProductos()"
                class="rounded border-gray-300 text-red-800 focus:ring-red-700"
            >
            Solo ofertas
        </label>


        {{-- LIMPIAR --}}
        <button
            type="button"
            @click="limpiarFiltros()"
            x-show="buscar || categoria || marca || orden || disponible || oferta"
            x-cloak
            class="text-sm text-gray-500
                   hover:text-red-800 px-2"
        >
            Limpiar filtros
        </button>

    </div>


    {{-- RESULTADOS --}}
    <div class="mb-4">

        <p
            x-show="buscando"
            x-cloak
            class="text-sm text-gray-500"
        >
            <span x-text="productos.length"></span>
            resultado(s) encontrado(s)
        </p>

    </div>


    {{-- PRODUCTOS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">

        {{-- RESULTADOS EN TIEMPO REAL --}}
        <template
            x-if="buscando"
        >

            <template
                x-for="producto in productos"
                :key="producto.id"
            >

                <div
                    class="bg-white rounded-lg border
                           overflow-hidden flex flex-col relative"
                >

                    {{-- Badge de oferta --}}
                    <span
                        x-show="producto.en_oferta"
                        x-cloak
                        class="absolute top-2 left-2 z-10 bg-red-700 text-white text-[10px] font-bold uppercase px-2 py-1 rounded"
                    >
                        Oferta
                    </span>

                    {{-- Imagen --}}
                    <a :href="producto.url">

                        <div
                            class="w-full h-40 bg-gray-50
                                   flex items-center justify-center
                                   overflow-hidden"
                        >

                            <img
                                :src="producto.imagen"
                                :alt="producto.nombre"
                                class="w-full h-full object-contain"
                            >

                        </div>

                    </a>


                    {{-- Información --}}
                    <div class="p-3 flex flex-col flex-1">

                        <p
                            x-show="producto.marca"
                            x-text="producto.marca"
                            class="text-xs text-gray-400 mb-1"
                        ></p>

                        <h3
                            class="font-medium text-sm
                                   mb-1 text-gray-800"
                            x-text="producto.nombre"
                        ></h3>

                        <p
                            x-show="producto.sku"
                            x-text="'SKU: ' + producto.sku"
                            class="text-xs text-gray-400 mb-2"
                        ></p>

                        <p x-show="!producto.en_oferta" class="text-red-800 font-bold mb-3" x-text="'$' + producto.precio"></p>

                        <p x-show="producto.en_oferta" x-cloak class="mb-3">
                            <span class="line-through text-gray-400 text-xs block" x-text="'$' + producto.precio"></span>
                            <span class="text-red-700 font-bold" x-text="'$' + producto.precio_oferta"></span>
                        </p>

                        <a
                            :href="producto.whatsapp"
                            target="_blank"
                            class="mt-auto bg-green-500
                                   hover:bg-green-600
                                   text-white text-sm
                                   text-center py-2 rounded-md"
                        >
                            Comprar por WhatsApp
                        </a>

                    </div>

                </div>

            </template>

        </template>


        {{-- PRODUCTOS NORMALES --}}
        <template x-if="!buscando">

            <template>

                @forelse ($productos as $producto)

                    <div
                        class="bg-white rounded-lg border
                               overflow-hidden flex flex-col"
                    >

                        @include(
                            'catalogo._tarjeta-producto',
                            ['producto' => $producto]
                        )

                    </div>

                @empty

                    <p class="col-span-full text-gray-500">
                        No se encontraron productos.
                    </p>

                @endforelse

            </template>

        </template>


        {{-- SIN RESULTADOS --}}
        <div
            x-show="buscando &&
                    !cargando &&
                    productos.length === 0"
            x-cloak
            class="col-span-full text-center
                   text-gray-500 py-10"
        >
            No se encontraron productos.
        </div>

    </div>


    {{-- PAGINACIÓN --}}
    <div
        x-show="!buscando"
        x-cloak
        class="mt-10"
    >
        {{ $productos->links() }}
    </div>

</div>


<script>

function buscadorCatalogo()
{
    return {

        buscar: '',
        categoria: '',
        marca: '',
        orden: '',
        disponible: false,
        oferta: false,

        productos: [],

        cargando: false,
        buscando: false,

        init()
        {
            const params =
                new URLSearchParams(
                    window.location.search
                );

            this.buscar =
                params.get('buscar') || '';

            this.categoria =
                params.get('categoria') || '';

            this.marca =
                params.get('marca') || '';

            this.orden =
                params.get('orden') || '';

            this.disponible =
                params.get('disponible') === '1';

            this.oferta =
                params.get('oferta') === '1';

            if (
                this.buscar ||
                this.categoria ||
                this.marca ||
                this.orden ||
                this.disponible ||
                this.oferta
            ) {
                this.buscarProductos();
            }
        },


        async buscarProductos()
        {
            this.cargando = true;
            this.buscando = true;

            try {

                const params =
                    new URLSearchParams();

                params.append(
                    'q',
                    this.buscar
                );

                if (this.categoria) {

                    params.append(
                        'categoria',
                        this.categoria
                    );
                }

                if (this.marca) {

                    params.append(
                        'marca',
                        this.marca
                    );
                }

                if (this.orden) {

                    params.append(
                        'orden',
                        this.orden
                    );
                }

                if (this.disponible) {

                    params.append(
                        'disponible',
                        '1'
                    );
                }

                if (this.oferta) {

                    params.append(
                        'oferta',
                        '1'
                    );
                }


                const response =
                    await fetch(
                        '{{ route('catalogo.buscar') }}?' +
                        params.toString(),
                        {
                            headers: {
                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest'
                            }
                        }
                    );


                if (!response.ok) {
                    throw new Error(
                        'Error en la búsqueda'
                    );
                }


                const data =
                    await response.json();

                this.productos =
                    data.productos;


                // Actualizar URL sin recargar
                const url =
                    new URL(
                        window.location.href
                    );

                if (this.buscar) {
                    url.searchParams.set(
                        'buscar',
                        this.buscar
                    );
                } else {
                    url.searchParams.delete(
                        'buscar'
                    );
                }

                if (this.categoria) {
                    url.searchParams.set(
                        'categoria',
                        this.categoria
                    );
                } else {
                    url.searchParams.delete(
                        'categoria'
                    );
                }

                if (this.marca) {
                    url.searchParams.set(
                        'marca',
                        this.marca
                    );
                } else {
                    url.searchParams.delete(
                        'marca'
                    );
                }

                if (this.orden) {
                    url.searchParams.set(
                        'orden',
                        this.orden
                    );
                } else {
                    url.searchParams.delete(
                        'orden'
                    );
                }

                if (this.disponible) {
                    url.searchParams.set(
                        'disponible',
                        '1'
                    );
                } else {
                    url.searchParams.delete(
                        'disponible'
                    );
                }

                if (this.oferta) {
                    url.searchParams.set(
                        'oferta',
                        '1'
                    );
                } else {
                    url.searchParams.delete(
                        'oferta'
                    );
                }

                window.history.replaceState(
                    {},
                    '',
                    url
                );

            } catch (error) {

                console.error(error);

                this.productos = [];

            } finally {

                this.cargando = false;
            }
        },


        limpiarFiltros()
        {
            this.buscar = '';
            this.categoria = '';
            this.marca = '';
            this.orden = '';
            this.disponible = false;
            this.oferta = false;

            this.productos = [];
            this.buscando = false;

            window.history.replaceState(
                {},
                '',
                '{{ route('catalogo.index') }}'
            );
        }

    }
}

</script>

</x-layouts.tienda>

