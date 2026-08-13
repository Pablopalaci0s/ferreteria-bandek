@component('admin.layout')

    @slot('header')
        Reportes de ventas
    @endslot

    <a href="{{ route('admin.ventas.index') }}"
       class="text-sm text-gray-500 hover:text-red-800 mb-4 inline-block">
        &larr; Volver a ventas
    </a>

    {{-- Filtro de fechas --}}
    <form method="GET" action="{{ route('admin.ventas.reportes') }}"
          class="bg-white border rounded-lg p-4 mb-6 flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Desde</label>
            <input type="date" name="desde" value="{{ $desde->format('Y-m-d') }}"
                   class="border-gray-300 rounded-md text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Hasta</label>
            <input type="date" name="hasta" value="{{ $hasta->format('Y-m-d') }}"
                   class="border-gray-300 rounded-md text-sm">
        </div>
        <button type="submit" class="bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded-md">Filtrar</button>
        <a href="{{ route('admin.ventas.reportes') }}" class="text-sm text-gray-500 hover:text-red-800 py-2">Hoy</a>
    </form>

    {{-- Totales --}}
    <div class="grid sm:grid-cols-2 gap-4 mb-8">
        <div class="bg-white border rounded-lg p-5">
            <p class="text-xs text-gray-500 uppercase font-medium mb-1">Total vendido</p>
            <p class="text-3xl font-bold text-green-700">$ {{ number_format($totalVentas, 2) }}</p>
        </div>
        <div class="bg-white border rounded-lg p-5">
            <p class="text-xs text-gray-500 uppercase font-medium mb-1">Ventas confirmadas</p>
            <p class="text-3xl font-bold text-gray-800">{{ $cantidadVentas }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">

        {{-- Por vendedor (solo admin) --}}
        @if ($esAdmin)
            <div class="bg-white border rounded-lg p-5">
                <h3 class="font-medium text-gray-800 mb-4">Por vendedor</h3>
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500 uppercase text-xs border-b">
                        <tr>
                            <th class="py-2">Vendedor</th>
                            <th class="py-2 text-center">Ventas</th>
                            <th class="py-2 text-right">Importe</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($ventasPorVendedor as $fila)
                            <tr>
                                <td class="py-2 text-gray-800">{{ $fila->vendedor->name ?? '—' }}</td>
                                <td class="py-2 text-center text-gray-600">{{ $fila->cantidad }}</td>
                                <td class="py-2 text-right font-medium text-gray-800">$ {{ number_format($fila->importe, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-6 text-center text-gray-400">Sin ventas en el rango.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Productos más vendidos --}}
        <div class="bg-white border rounded-lg p-5 {{ $esAdmin ? '' : 'lg:col-span-2' }}">
            <h3 class="font-medium text-gray-800 mb-4">Productos más vendidos</h3>
            <table class="w-full text-sm">
                <thead class="text-left text-gray-500 uppercase text-xs border-b">
                    <tr>
                        <th class="py-2">Producto</th>
                        <th class="py-2 text-center">Unidades</th>
                        <th class="py-2 text-right">Importe</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($productosMasVendidos as $fila)
                        <tr>
                            <td class="py-2 text-gray-800">{{ $fila->producto_nombre }}</td>
                            <td class="py-2 text-center text-gray-600">{{ $fila->unidades }}</td>
                            <td class="py-2 text-right font-medium text-gray-800">$ {{ number_format($fila->importe, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-6 text-center text-gray-400">Sin ventas en el rango.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endcomponent
