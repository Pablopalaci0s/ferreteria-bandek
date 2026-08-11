@component('admin.layout')

    @slot('header')
        Registrar movimiento
    @endslot

    <a href="{{ route('admin.inventario.index') }}"
       class="text-sm text-gray-500 hover:text-red-800 mb-4 inline-block">
        &larr; Volver a inventario
    </a>

    <form action="{{ route('admin.inventario.store') }}"
          method="POST"
          class="bg-white p-6 rounded-lg border max-w-lg">

        @csrf

        <div class="mb-5">

            <label class="block text-sm font-medium text-gray-700 mb-1">
                Producto
            </label>

            <select name="producto_id"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
                    required>

                <option value="">Seleccionar...</option>

                @foreach ($productos as $producto)
                    <option value="{{ $producto->id }}"
                        @selected(old('producto_id') == $producto->id)>
                        {{ $producto->nombre }} (stock actual: {{ $producto->stock }})
                    </option>
                @endforeach

            </select>

            @error('producto_id')
                <p class="text-red-600 text-xs mt-1">
                    {{ $message }}
                </p>
            @enderror

        </div>

        <div class="mb-5">

            <label class="block text-sm font-medium text-gray-700 mb-1">
                Tipo de movimiento
            </label>

            <select name="tipo"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
                    required>

                <option value="entrada"
                    @selected(old('tipo') == 'entrada')>
                    Entrada (suma al stock)
                </option>

                <option value="salida"
                    @selected(old('tipo') == 'salida')>
                    Salida (resta del stock)
                </option>

                <option value="ajuste"
                    @selected(old('tipo') == 'ajuste')>
                    Ajuste (fija el stock exacto)
                </option>

            </select>

        </div>

        <div class="mb-5">

            <label class="block text-sm font-medium text-gray-700 mb-1">
                Cantidad
            </label>

            <input type="number"
                   name="cantidad"
                   value="{{ old('cantidad') }}"
                   min="0"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
                   required>

            @error('cantidad')
                <p class="text-red-600 text-xs mt-1">
                    {{ $message }}
                </p>
            @enderror

            <p class="text-xs text-gray-400 mt-1">
                Para "Ajuste", escribí el número final exacto de stock (no la diferencia).
            </p>

        </div>

        <div class="mb-6">

            <label class="block text-sm font-medium text-gray-700 mb-1">
                Motivo (opcional)
            </label>

            <input type="text"
                   name="motivo"
                   value="{{ old('motivo') }}"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">

        </div>

        <button type="submit"
                class="w-full bg-red-800 hover:bg-red-900 text-white font-medium py-2.5 rounded-md">
            Registrar movimiento
        </button>

    </form>

@endcomponent