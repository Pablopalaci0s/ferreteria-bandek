@component('admin.layout')
    @slot('header') Categorías @endslot

    @if (session('status'))
        <div class="bg-green-50 text-green-800 border border-green-200 px-4 py-2 rounded-md mb-4 text-sm">{{ session('status') }}</div>
    @endif

    @include('admin._buscador', ['placeholder' => 'Buscar categoría por nombre...'])

    <div class="flex justify-between items-center mb-6">
        <p class="text-sm text-gray-500">{{ $categorias->total() }} categorías registradas</p>
        <a href="{{ route('admin.categorias.create') }}" class="bg-red-800 hover:bg-red-900 text-white text-sm font-medium px-4 py-2 rounded-md">
            + Nueva categoría
        </a>
    </div>

    <div class="bg-white border rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500 uppercase text-xs">
                <tr>
                    <th class="p-3">Imagen</th>
                    <th class="p-3">Nombre</th>
                    <th class="p-3">Productos</th>
                    <th class="p-3">Estado</th>
                    <th class="p-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach ($categorias as $categoria)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3">
                            @if ($categoria->imagen)
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($categoria->imagen) }}" class="w-10 h-10 object-cover rounded border">
                            @else
                                <span class="text-xs text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="p-3 font-medium text-gray-800">{{ $categoria->nombre }}</td>
                        <td class="p-3 text-gray-500">{{ $categoria->productos_count }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $categoria->activo ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $categoria->activo ? 'Activa' : 'Inactiva' }}
                            </span>
                        </td>
                        <td class="p-3 text-right space-x-3">
                            <a href="{{ route('admin.categorias.edit', $categoria) }}" class="text-red-800 hover:underline">Editar</a>
                            <form action="{{ route('admin.categorias.destroy', $categoria) }}" method="POST" class="inline"
                                  onsubmit="return confirm('¿Eliminar esta categoría?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-700">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $categorias->links() }}</div>

@endcomponent