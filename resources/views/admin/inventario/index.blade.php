@component('admin.layout')

    @slot('header')
        Inventario
    @endslot

    @if (session('status'))
        <div class="bg-green-50 text-green-800 border border-green-200 px-4 py-2 rounded-md mb-4 text-sm">
            {{ session('status') }}
        </div>
    @endif

    @include('admin._buscador', ['placeholder' => 'Buscar por producto, SKU o motivo...'])

    <div class="flex justify-between items-center mb-6">
        <p class="text-sm text-gray-500">
            Historial de movimientos de stock
        </p>

        <a href="{{ route('admin.inventario.create') }}"
           class="bg-red-800 hover:bg-red-900 text-white text-sm font-medium px-4 py-2 rounded-md">
            + Registrar movimiento
        </a>
    </div>

    <div class="bg-white border rounded-lg overflow-hidden">

        <table class="w-full text-sm">

            <thead class="bg-gray-50 text-left text-gray-500 uppercase text-xs">
                <tr>
                    <th class="p-3">Fecha</th>
                    <th class="p-3">Producto</th>
                    <th class="p-3">Tipo</th>
                    <th class="p-3">Cantidad</th>
                    <th class="p-3">Motivo</th>
                    <th class="p-3">Usuario</th>
                </tr>
            </thead>

            <tbody class="divide-y">

                @forelse ($movimientos as $movimiento)

                    @php
                        $colores = [
                            'entrada' => 'bg-green-100 text-green-700',
                            'devolucion' => 'bg-emerald-100 text-emerald-700',
                            'venta' => 'bg-red-100 text-red-700',
                            'salida' => 'bg-red-100 text-red-700',
                            'perdida' => 'bg-orange-100 text-orange-700',
                            'ajuste' => 'bg-amber-100 text-amber-700',
                        ];
                    @endphp

                    <tr class="hover:bg-gray-50">

                        <td class="p-3 text-gray-500">
                            {{ $movimiento->created_at->format('d/m/Y H:i') }}
                        </td>

                        <td class="p-3 font-medium text-gray-800">
                            {{ $movimiento->producto->nombre }}
                        </td>

                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $colores[$movimiento->tipo] ?? 'bg-gray-100 text-gray-500' }}">
                                {{ ucfirst($movimiento->tipo) }}
                            </span>
                        </td>

                        <td class="p-3 font-medium text-gray-700">
                            {{ $movimiento->cantidad }}
                        </td>

                        <td class="p-3 text-gray-500">
                            {{ $movimiento->motivo ?? '—' }}
                        </td>

                        <td class="p-3 text-gray-500">
                            {{ $movimiento->usuario->name ?? '—' }}
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400">
                            Aún no hay movimientos registrados.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-6">
        {{ $movimientos->links() }}
    </div>

@endcomponent