@component('admin.layout')
    @slot('header') Usuarios @endslot

    @if (session('status'))
        <div class="bg-green-50 text-green-800 border border-green-200 px-4 py-2 rounded-md mb-4 text-sm">{{ session('status') }}</div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 text-red-800 border border-red-200 px-4 py-2 rounded-md mb-4 text-sm">{{ session('error') }}</div>
    @endif

    @include('admin._buscador', ['placeholder' => 'Buscar usuario por nombre o email...'])

    <div class="flex justify-between items-center mb-6">
        <p class="text-sm text-gray-500">Administradores y vendedores con acceso al panel</p>
        <a href="{{ route('admin.usuarios.create') }}" class="bg-red-800 hover:bg-red-900 text-white text-sm font-medium px-4 py-2 rounded-md">
            + Nuevo usuario
        </a>
    </div>

    <div class="bg-white border rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500 uppercase text-xs">
                <tr>
                    <th class="p-3">Nombre</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Rol</th>
                    <th class="p-3">Creado</th>
                    <th class="p-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($usuarios as $usuario)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-medium text-gray-800">
                            {{ $usuario->name }}
                            @if ($usuario->id === auth()->id())
                                <span class="text-xs text-gray-400">(vos)</span>
                            @endif
                        </td>
                        <td class="p-3 text-gray-500">{{ $usuario->email }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $usuario->rol === 'admin' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ ucfirst($usuario->rol) }}
                            </span>
                        </td>
                        <td class="p-3 text-gray-500">{{ $usuario->created_at->format('d/m/Y') }}</td>
                        <td class="p-3 text-right space-x-2">
                            <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="text-red-800 hover:underline">Editar</a>
                            @if ($usuario->id !== auth()->id())
                                <form action="{{ route('admin.usuarios.destroy', $usuario) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar a {{ $usuario->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-500 hover:text-red-800 hover:underline">Eliminar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-gray-400">No hay usuarios registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $usuarios->links() }}</div>
@endcomponent