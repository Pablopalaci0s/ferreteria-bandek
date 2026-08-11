@php
$p = $producto ?? null;
@endphp

<div class="grid grid-cols-2 gap-4 mb-5">


<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        SKU
    </label>

    <input
        type="text"
        name="sku"
        value="{{ old('sku', $p->sku ?? '') }}"
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-red-700 focus:border-red-700"
        required
    >

    @error('sku')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Modelo
    </label>

    <input
        type="text"
        name="modelo"
        value="{{ old('modelo', $p->modelo ?? '') }}"
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-red-700 focus:border-red-700"
        placeholder="Ej. Taladro Bosch GSB 13 RE"
    >

    @error('modelo')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Nombre
    </label>

    <input
        type="text"
        name="nombre"
        value="{{ old('nombre', $p->nombre ?? '') }}"
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-red-700 focus:border-red-700"
        required
    >

    @error('nombre')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Descripción larga
    </label>

    <textarea
        name="descripcion_larga"
        rows="6"
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-red-700 focus:border-red-700"
        placeholder="Escribí las características y detalles del producto..."
    >{{ old('descripcion_larga', $p->descripcion_larga ?? '') }}</textarea>

    @error('descripcion_larga')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

</div>

<div class="mb-5">


<label class="block text-sm font-medium text-gray-700 mb-1">
    Descripción
</label>

<textarea
    name="descripcion"
    rows="3"
    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-red-700 focus:border-red-700"
>{{ old('descripcion', $p->descripcion ?? '') }}</textarea>


</div>

<div class="grid grid-cols-2 gap-4 mb-5">


<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Precio de venta
    </label>

    <input
        type="number"
        step="0.01"
        name="precio"
        value="{{ old('precio', $p->precio ?? '') }}"
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-red-700 focus:border-red-700"
        required
    >

    @error('precio')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Costo (opcional)
    </label>

    <input
        type="number"
        step="0.01"
        name="costo"
        value="{{ old('costo', $p->costo ?? '') }}"
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-red-700 focus:border-red-700"
    >
</div>


</div>

<div class="mb-5">


<label class="block text-sm font-medium text-gray-700 mb-1">
    Stock mínimo (para alertas)
</label>

<input
    type="number"
    name="stock_minimo"
    value="{{ old('stock_minimo', $p->stock_minimo ?? 0) }}"
    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-red-700 focus:border-red-700"
    required
>

@if ($p)
    <p class="text-xs text-gray-400 mt-1">
        Stock actual: {{ $p->stock }} (se modifica desde Inventario, no acá)
    </p>
@endif


</div>

<div class="grid grid-cols-2 gap-4 mb-5">


<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Categoría
    </label>

    <select
        name="categoria_id"
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
        required
    >
        <option value="">Seleccionar...</option>

        @foreach ($categorias as $categoria)
            <option
                value="{{ $categoria->id }}"
                @selected(old('categoria_id', $p->categoria_id ?? null) == $categoria->id)
            >
                {{ $categoria->nombre }}
            </option>
        @endforeach
    </select>

    @error('categoria_id')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Marca (opcional)
    </label>

    <select
        name="marca_id"
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
    >
        <option value="">Sin marca</option>

        @foreach ($marcas as $marca)
            <option
                value="{{ $marca->id }}"
                @selected(old('marca_id', $p->marca_id ?? null) == $marca->id)
            >
                {{ $marca->nombre }}
            </option>
        @endforeach
    </select>
</div>


</div>

<div class="grid grid-cols-2 gap-4 mb-5">


<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Proveedor (opcional)
    </label>

    <select
        name="proveedor_id"
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
    >
        <option value="">Sin proveedor</option>

        @foreach ($proveedores as $proveedor)
            <option
                value="{{ $proveedor->id }}"
                @selected(old('proveedor_id', $p->proveedor_id ?? null) == $proveedor->id)
            >
                {{ $proveedor->nombre }}
            </option>
        @endforeach
    </select>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Unidad de medida
    </label>

    <select
        name="unidad_medida_id"
        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
        required
    >
        <option value="">Seleccionar...</option>

        @foreach ($unidadesMedida as $unidad)
            <option
                value="{{ $unidad->id }}"
                @selected(old('unidad_medida_id', $p->unidad_medida_id ?? null) == $unidad->id)
            >
                {{ $unidad->nombre }} ({{ $unidad->abreviatura }})
            </option>
        @endforeach
    </select>

    @error('unidad_medida_id')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>


</div>

<div class="mb-5">

<label class="block text-sm font-medium text-gray-700 mb-1">
    Imagen principal
</label>

<input
    type="file"
    name="imagen_principal"
    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
>

@error('imagen_principal')
    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
@enderror

@if ($p && $p->imagen_principal)
    <img
        src="{{ asset('storage/' . $p->imagen_principal) }}"
        class="w-20 h-20 object-cover mt-2 rounded border"
    >
@endif


</div>

<div class="flex gap-6 mb-6">


<label class="flex items-center gap-2 text-sm text-gray-700">

    <input type="hidden" name="activo" value="0">

    <input
        type="checkbox"
        name="activo"
        value="1"
        class="rounded border-gray-300 text-red-800 focus:ring-red-700"
        @checked(old('activo', $p->activo ?? true))
    >

    Activo (visible en el catálogo)

</label>

<label class="flex items-center gap-2 text-sm text-gray-700">

    <input type="hidden" name="destacado" value="0">

    <input
        type="checkbox"
        name="destacado"
        value="1"
        class="rounded border-gray-300 text-red-800 focus:ring-red-700"
        @checked(old('destacado', $p->destacado ?? false))
    >

    Destacado (aparece en inicio)

</label>


</div>
