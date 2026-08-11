@php
$m = $marca ?? null;
@endphp

<div class="mb-5">

<label class="block text-sm font-medium text-gray-700 mb-1">
    Nombre
</label>

<input
    type="text"
    name="nombre"
    value="{{ old('nombre', $m->nombre ?? '') }}"
    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-red-700 focus:border-red-700"
    required
>

@error('nombre')
    <p class="text-red-600 text-xs mt-1">
        {{ $message }}
    </p>
@enderror


</div>

<label class="flex items-center gap-2 text-sm text-gray-700 mb-6">


<input type="hidden" name="activo" value="0">

<input
    type="checkbox"
    name="activo"
    value="1"
    class="rounded border-gray-300 text-red-800 focus:ring-red-700"
    @checked(old('activo', $m->activo ?? true))
>

Activa


</label>
