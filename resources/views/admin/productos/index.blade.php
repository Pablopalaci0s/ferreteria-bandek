@component('admin.layout')

@slot('header')
    Productos
@endslot

@if (session('status'))
    <div class="bg-green-50 text-green-800 border border-green-200 px-4 py-2 rounded-md mb-4 text-sm">
        {{ session('status') }}
    </div>
@endif

@include('admin._buscador', ['placeholder' => 'Buscar producto por nombre, SKU o modelo...'])

<div class="flex justify-between items-center mb-6">

    <p class="text-sm text-gray-500">
        {{ $productos->total() }} productos registrados
    </p>

    <a
        href="{{ route('admin.productos.create') }}"
        class="bg-red-800 hover:bg-red-900 text-white text-sm font-medium px-4 py-2 rounded-md"
    >
        + Nuevo producto
    </a>

</div>

<div class="bg-white border rounded-lg overflow-hidden">

    <table class="w-full text-sm">

        <thead class="bg-gray-50 text-left text-gray-500 uppercase text-xs">
            <tr>
                <th class="p-3">SKU</th>
                <th class="p-3">Nombre</th>
                <th class="p-3">Categoría</th>
                <th class="p-3">Precio</th>
                <th class="p-3">Stock</th>
                <th class="p-3">Estado</th>
                <th class="p-3 text-right">Acciones</th>
            </tr>
        </thead>

        <tbody class="divide-y">

            @foreach ($productos as $producto)

                <tr class="hover:bg-gray-50">

                    <td class="p-3 text-gray-500">
                        {{ $producto->sku }}
                    </td>

                    <td class="p-3 font-medium text-gray-800">
                        {{ $producto->nombre }}
                    </td>

                    <td class="p-3 text-gray-500">
                        {{ $producto->categoria->nombre }}
                    </td>

                    <td class="p-3 text-gray-500">
                        @if ($producto->en_oferta)
                            <span class="line-through text-gray-400 text-xs block">${{ number_format($producto->precio, 2) }}</span>
                            <span class="text-red-700 font-semibold">${{ number_format($producto->precio_oferta, 2) }}</span>
                        @else
                            ${{ number_format($producto->precio, 2) }}
                        @endif
                    </td>

                    <td class="p-3">

                        <span class="{{ $producto->stock <= $producto->stock_minimo ? 'text-red-700 font-semibold' : 'text-gray-500' }}">
                            {{ $producto->stock }}
                        </span>

                    </td>

                    <td class="p-3">

                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $producto->activo ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $producto->activo ? 'Activo' : 'Inactivo' }}
                        </span>

                    </td>

                    <td class="p-3 text-right space-x-3">

                        <a
                            href="{{ route('admin.productos.edit', $producto) }}"
                            class="text-red-800 hover:underline"
                        >
                            Editar
                        </a>

                        <form
                            action="{{ route('admin.productos.destroy', $producto) }}"
                            method="POST"
                            class="inline"
                            onsubmit="return confirm('¿Eliminar este producto?')"
                        >

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="text-gray-400 hover:text-red-700"
                            >
                                Eliminar
                            </button>

                        </form>

                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

</div>

<div class="mt-6">
    {{ $productos->links() }}
</div>


@endcomponent
