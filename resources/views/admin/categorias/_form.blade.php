@php $c = $categoria ?? null; @endphp

<div class="mb-5">
    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
    <input type="text" name="nombre" value="{{ old('nombre', $c->nombre ?? '') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-red-700 focus:border-red-700" required>
    @error('nombre')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-5">
    <label class="block text-sm font-medium text-gray-700 mb-1">Categoría padre (opcional)</label>
    <select name="categoria_padre_id" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-red-700 focus:border-red-700">
        <option value="">Ninguna (categoría principal)</option>
        @foreach ($categoriasPadre as $padre)
            <option value="{{ $padre->id }}" @selected(old('categoria_padre_id', $c->categoria_padre_id ?? null) == $padre->id)>
                {{ $padre->nombre }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-5">
    <label class="block text-sm font-medium text-gray-700 mb-1">Ícono / imagen (opcional)</label>

    <input type="file" name="imagen" accept="image/*" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">

    <p class="text-xs text-gray-400 mt-1">
        Se muestra en vez del ícono genérico en el menú y en la página principal. Recomendado: imagen cuadrada, fondo transparente o simple, máx. 2 MB.
    </p>

    @error('imagen')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror

    @if ($c && $c->imagen)
        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($c->imagen) }}" class="w-16 h-16 object-cover mt-2 rounded border">
        <p class="text-xs text-gray-400 mt-1">Imagen actual. Subí una nueva solo si querés reemplazarla.</p>
    @endif
</div>

<label class="flex items-center gap-2 text-sm text-gray-700 mb-6">
    <input type="hidden" name="activo" value="0">
    <input type="checkbox" name="activo" value="1" class="rounded border-gray-300 text-red-800 focus:ring-red-700" @checked(old('activo', $c->activo ?? true))>
    Activa (visible en el catálogo)
</label>
