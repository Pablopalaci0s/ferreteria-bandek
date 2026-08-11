@component('admin.layout')

@slot('header')
    Configuración del sitio
@endslot

@if (session('status'))
    <div class="bg-green-50 text-green-800 border border-green-200 px-4 py-2 rounded-md mb-4 text-sm">
        {{ session('status') }}
    </div>
@endif

@if (empty($whatsapp_numero))
    <div class="bg-yellow-50 text-yellow-800 border border-yellow-200 px-4 py-2 rounded-md mb-4 text-sm">
        Todavía no configuraste el número de WhatsApp. Los botones de "Comprar por WhatsApp" del sitio no van a funcionar hasta que lo cargues acá.
    </div>
@endif

<form
    action="{{ route('admin.configuracion.update') }}"
    method="POST"
    class="bg-white p-6 rounded-lg border max-w-xl"
>
    @csrf
    @method('PUT')

    <div class="mb-5">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Número de WhatsApp
        </label>

        <input
            type="text"
            name="whatsapp_numero"
            value="{{ old('whatsapp_numero', $whatsapp_numero) }}"
            placeholder="50312345678"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-red-700 focus:border-red-700"
            required
        >

        <p class="text-xs text-gray-400 mt-1">
            Con código de país, solo números, sin "+", espacios ni guiones. Ejemplo: 50312345678. Es el número que se usa en todos los botones de "Comprar por WhatsApp" del sitio.
        </p>

        @error('whatsapp_numero')
            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-5">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Teléfono (se muestra en el pie de página)
        </label>

        <input
            type="text"
            name="telefono"
            value="{{ old('telefono', $telefono) }}"
            placeholder="+503 2222-2222"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-red-700 focus:border-red-700"
        >

        <p class="text-xs text-gray-400 mt-1">
            Este puede llevar formato normal (con "+" y guiones), es solo para mostrar.
        </p>

        @error('telefono')
            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Dirección
        </label>

        <input
            type="text"
            name="direccion"
            value="{{ old('direccion', $direccion) }}"
            placeholder="San Salvador, El Salvador"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-red-700 focus:border-red-700"
        >

        @error('direccion')
            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <button
        type="submit"
        class="w-full bg-red-800 hover:bg-red-900 text-white font-medium py-2.5 rounded-md"
    >
        Guardar configuración
    </button>
</form>

@endcomponent
