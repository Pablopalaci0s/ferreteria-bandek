@php
$b = $banner ?? null;
@endphp

<div class="mb-5">

<label class="block text-sm font-medium text-gray-700 mb-1">
    Título (opcional)
</label>

<input
    type="text"
    name="titulo"
    value="{{ old('titulo', $b->titulo ?? '') }}"
    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-red-700 focus:border-red-700"
    placeholder="Ej. Descuentos de temporada"
>

@error('titulo')
    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
@enderror

</div>

<div class="mb-5">

<label class="block text-sm font-medium text-gray-700 mb-1">
    Enlace al hacer clic (opcional)
</label>

<input
    type="text"
    name="link"
    value="{{ old('link', $b->link ?? '') }}"
    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-red-700 focus:border-red-700"
    placeholder="Ej. /catalogo?categoria=herramientas"
>

<p class="text-xs text-gray-400 mt-1">
    Puede ser una URL completa o una ruta interna del sitio.
</p>

@error('link')
    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
@enderror

</div>

<div class="mb-5">

<label class="block text-sm font-medium text-gray-700 mb-1">
    Imagen {{ $b ? '' : '' }}
</label>

<input
    type="file"
    name="imagen"
    accept="image/*"
    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
    {{ $b ? '' : 'required' }}
>

<p class="text-xs text-gray-400 mt-1">
    Recomendado: imagen panorámica de al menos 1600×600px (relación aproximada 21:7). La imagen se muestra completa, sin recortar, así que entre más se acerque a esa proporción, mejor se va a ver. Máx. 4 MB.
</p>

@error('imagen')
    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
@enderror

@if ($b && $b->imagen)
    <img
        src="{{ asset('storage/' . $b->imagen) }}"
        class="w-full max-w-md h-32 object-cover mt-2 rounded border"
    >
    <p class="text-xs text-gray-400 mt-1">
        Imagen actual. Subí una nueva solo si querés reemplazarla.
    </p>
@endif

</div>

<div class="mb-6">

<label class="block text-sm font-medium text-gray-700 mb-1">
    Orden
</label>

<input
    type="number"
    name="orden"
    value="{{ old('orden', $b->orden ?? 0) }}"
    min="0"
    class="w-32 border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-red-700 focus:border-red-700"
>

<p class="text-xs text-gray-400 mt-1">
    Los banners se muestran de menor a mayor orden.
</p>

</div>

<label class="flex items-center gap-2 text-sm text-gray-700 mb-6">
    <input type="hidden" name="activo" value="0">
    <input
        type="checkbox"
        name="activo"
        value="1"
        class="rounded border-gray-300 text-red-800 focus:ring-red-700"
        @checked(old('activo', $b->activo ?? true))
    >
    Activo (visible en la página principal)
</label>
