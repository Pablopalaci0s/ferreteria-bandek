@component('admin.layout')
    @slot('header') Editar usuario @endslot

    <a href="{{ route('admin.usuarios.index') }}" class="text-sm text-gray-500 hover:text-red-800 mb-4 inline-block">&larr; Volver a usuarios</a>

    <form action="{{ route('admin.usuarios.update', $usuario) }}" method="POST" class="bg-white p-6 rounded-lg border max-w-lg">
        @csrf
        @method('PUT')

        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
            <input type="text" name="name" value="{{ old('name', $usuario->name) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" required>
            @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $usuario->email) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" required>
            @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nueva contraseña</label>
            <input type="password" name="password" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" placeholder="Dejar en blanco para no cambiarla">
            @error('password') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
            <select name="rol" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" required @disabled($usuario->id === auth()->id())>
                <option value="vendedor" @selected(old('rol', $usuario->rol) == 'vendedor')>Vendedor</option>
                <option value="admin" @selected(old('rol', $usuario->rol) == 'admin')>Admin</option>
            </select>
            @if ($usuario->id === auth()->id())
                <input type="hidden" name="rol" value="{{ $usuario->rol }}">
                <p class="text-xs text-gray-400 mt-1">No podés cambiar tu propio rol.</p>
            @endif
            @error('rol') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <button class="w-full bg-red-800 hover:bg-red-900 text-white font-medium py-2.5 rounded-md">Guardar cambios</button>
    </form>
@endcomponent