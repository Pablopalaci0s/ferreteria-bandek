@component('admin.layout')
    @slot('header') Nuevo usuario @endslot

    <a href="{{ route('admin.usuarios.index') }}" class="text-sm text-gray-500 hover:text-red-800 mb-4 inline-block">&larr; Volver a usuarios</a>

    <form action="{{ route('admin.usuarios.store') }}" method="POST" class="bg-white p-6 rounded-lg border max-w-lg">
        @csrf

        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" required>
            @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" required>
            @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
            <input type="password" name="password" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" required>
            @error('password') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
            <select name="rol" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" required>
                <option value="vendedor" @selected(old('rol') == 'vendedor')>Vendedor</option>
                <option value="admin" @selected(old('rol') == 'admin')>Admin</option>
            </select>
            @error('rol') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <button class="w-full bg-red-800 hover:bg-red-900 text-white font-medium py-2.5 rounded-md">Crear usuario</button>
    </form>
@endcomponent