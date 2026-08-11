@php
$p = $proveedor ?? null;
@endphp

<div class="mb-5">


<label class="block text-sm font-medium text-gray-700 mb-1">
    Nombre del proveedor
</label>

<input
    type="text"
    name="nombre"
    value="{{ old('nombre', $p->nombre ?? '') }}"
    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-red-700 focus:border-red-700"
    required
>

@error('nombre')
    <p class="text-red-600 text-xs mt-1">
        {{ $message }}
    </p>
@enderror


</div>

<div class="mb-5">


<label class="block text-sm font-medium text-gray-700 mb-1">
    Nombre de contacto
</label>

<input
    type="text"
    name="contacto_nombre"
    value="{{ old('contacto_nombre', $p->contacto_nombre ?? '') }}"
    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-red-700 focus:border-red-700"
>


</div>

<div class="grid grid-cols-2 gap-4 mb-5">


<div>

    <label class="block text-sm font-medium text-gray-700 mb-1">
        Teléfono
    </label>

    <input
        type="text"
        name="telefono"
        value="{{ old('telefono', $p->telefono ?? '') }}"
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-red-700 focus:border-red-700"
    >

</div>

<div>

    <label class="block text-sm font-medium text-gray-700 mb-1">
        Email
    </label>

    <input
        type="email"
        name="email"
        value="{{ old('email', $p->email ?? '') }}"
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-red-700 focus:border-red-700"
    >

    @error('email')
        <p class="text-red-600 text-xs mt-1">
            {{ $message }}
        </p>
    @enderror

</div>


</div>

<div class="mb-5">


<label class="block text-sm font-medium text-gray-700 mb-1">
    Dirección
</label>

<input
    type="text"
    name="direccion"
    value="{{ old('direccion', $p->direccion ?? '') }}"
    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-red-700 focus:border-red-700"
>


</div>

<label class="flex items-center gap-2 text-sm text-gray-700 mb-6">


<input type="hidden" name="activo" value="0">

<input
    type="checkbox"
    name="activo"
    value="1"
    class="rounded border-gray-300 text-red-800 focus:ring-red-700"
    @checked(old('activo', $p->activo ?? true))
>

Activo


</label>
