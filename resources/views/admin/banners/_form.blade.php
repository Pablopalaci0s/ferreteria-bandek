@php
$b = $banner ?? null;
$zonaActual = old('zona', $b->zona ?? $zonaSeleccionada ?? array_key_first(\App\Models\Banner::ZONAS));
@endphp

<div
    x-data="{ zona: @js($zonaActual), zonas: @js(\App\Models\Banner::ZONAS) }"
>

<div class="mb-5">

<label class="block text-sm font-medium text-gray-700 mb-1">
    Dónde se muestra
</label>

<select
    name="zona"
    x-model="zona"
    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-red-700 focus:border-red-700"
>
    @foreach (\App\Models\Banner::ZONAS as $clave => $datos)
        <option value="{{ $clave }}" @selected($zonaActual === $clave)>{{ $datos['etiqueta'] }}</option>
    @endforeach
</select>

@error('zona')
    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
@enderror

</div>

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
    Imagen
</label>

<input
    type="file"
    name="imagen"
    accept="image/*"
    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
    {{ $b ? '' : 'required' }}
>

<p class="text-xs font-medium text-red-800 mt-1.5" x-text="'Tamaño recomendado: ' + zonas[zona].ancho + '×' + zonas[zona].alto + 'px'"></p>
<p class="text-xs text-gray-400 mt-0.5" x-text="zonas[zona].ayuda"></p>
<p class="text-xs text-gray-400 mt-0.5">La imagen no se recorta: si subís otra proporción, se ajusta completa dentro de ese tamaño. Máx. 5 MB.</p>

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
    Entre varios banners de la misma zona, se muestran de menor a mayor orden.
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
    Activo (visible en el sitio)
</label>

</div>
