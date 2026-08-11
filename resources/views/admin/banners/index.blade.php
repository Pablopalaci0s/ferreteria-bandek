@component('admin.layout')
    @slot('header') Banner principal @endslot

    @if (session('status'))
        <div class="bg-green-50 text-green-800 border border-green-200 px-4 py-2 rounded-md mb-4 text-sm">{{ session('status') }}</div>
    @endif

    @include('admin._buscador', ['placeholder' => 'Buscar banner por título...'])

    <div class="flex justify-between items-center mb-6">
        <p class="text-sm text-gray-500">
            {{ $banners->count() }} banners registrados
            &middot; se muestran en la página principal en orden rotativo
        </p>
        <a href="{{ route('admin.banners.create') }}" class="bg-red-800 hover:bg-red-900 text-white text-sm font-medium px-4 py-2 rounded-md">
            + Nuevo banner
        </a>
    </div>

    <div class="bg-white border rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500 uppercase text-xs">
                <tr>
                    <th class="p-3">Imagen</th>
                    <th class="p-3">Título</th>
                    <th class="p-3">Orden</th>
                    <th class="p-3">Estado</th>
                    <th class="p-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($banners as $banner)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3">
                            <img src="{{ asset('storage/' . $banner->imagen) }}" class="w-24 h-12 object-cover rounded border">
                        </td>
                        <td class="p-3 font-medium text-gray-800">{{ $banner->titulo ?: '—' }}</td>
                        <td class="p-3 text-gray-500">{{ $banner->orden }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $banner->activo ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $banner->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="p-3 text-right space-x-3">
                            <a href="{{ route('admin.banners.edit', $banner) }}" class="text-red-800 hover:underline">Editar</a>
                            <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" class="inline"
                                  onsubmit="return confirm('¿Eliminar este banner?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-700">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-gray-500">
                            Todavía no hay banners. Creá el primero para que aparezca en la página principal.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endcomponent
