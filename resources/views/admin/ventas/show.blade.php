@component('admin.layout')

    @slot('header')
        Venta #{{ $venta->id }}
    @endslot

    <a href="{{ route('admin.ventas.index') }}"
       class="text-sm text-gray-500 hover:text-red-800 mb-4 inline-block">
        &larr; Volver a ventas
    </a>

    @if (session('status'))
        <div class="bg-green-50 text-green-800 border border-green-200 px-4 py-2 rounded-md mb-4 text-sm">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 text-red-800 border border-red-200 px-4 py-2 rounded-md mb-4 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if (session('alerta_stock'))
        <div class="bg-amber-50 text-amber-800 border border-amber-200 px-4 py-2 rounded-md mb-4 text-sm">
            ⚠️ {{ session('alerta_stock') }}
        </div>
    @endif

    @php
        $colores = [
            'pendiente' => 'bg-amber-100 text-amber-700',
            'confirmada' => 'bg-green-100 text-green-700',
            'cancelada' => 'bg-gray-200 text-gray-600',
        ];
    @endphp

    <div class="grid lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 bg-white border rounded-lg p-5">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-medium text-gray-800">Detalle</h3>
                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $colores[$venta->estado] ?? 'bg-gray-100 text-gray-500' }}">
                    {{ ucfirst($venta->estado) }}
                </span>
            </div>

            <table class="w-full text-sm">
                <thead class="text-left text-gray-500 uppercase text-xs border-b">
                    <tr>
                        <th class="py-2">Producto</th>
                        <th class="py-2">SKU</th>
                        <th class="py-2 text-center">Cant.</th>
                        <th class="py-2 text-right">Unitario</th>
                        <th class="py-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach ($venta->detalles as $detalle)
                        <tr>
                            <td class="py-2 font-medium text-gray-800">{{ $detalle->producto_nombre }}</td>
                            <td class="py-2 text-gray-500">{{ $detalle->producto_sku ?? '—' }}</td>
                            <td class="py-2 text-center text-gray-700">{{ $detalle->cantidad }}</td>
                            <td class="py-2 text-right text-gray-600">$ {{ number_format($detalle->precio_unitario, 2) }}</td>
                            <td class="py-2 text-right font-medium text-gray-800">$ {{ number_format($detalle->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t">
                        <td colspan="4" class="py-3 text-right font-medium text-gray-600">Total</td>
                        <td class="py-3 text-right text-lg font-bold text-gray-900">$ {{ number_format($venta->total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="bg-white border rounded-lg p-5 h-fit space-y-4">
            <div>
                <h3 class="font-medium text-gray-800 mb-2">Cliente</h3>
                <p class="text-sm text-gray-600">{{ $venta->cliente_nombre ?? 'Sin nombre' }}</p>
                <p class="text-sm text-gray-500">{{ $venta->cliente_telefono ?? '—' }}</p>
                @if ($venta->notas)
                    <p class="text-sm text-gray-500 mt-2 italic">{{ $venta->notas }}</p>
                @endif
            </div>

            <div class="border-t pt-3 text-xs text-gray-500 space-y-1">
                <p>Vendedor: <span class="text-gray-700">{{ $venta->vendedor->name ?? '—' }}</span></p>
                <p>Registrada: {{ $venta->created_at->format('d/m/Y H:i') }}</p>
                @if ($venta->confirmada_en)
                    <p>Confirmada: {{ $venta->confirmada_en->format('d/m/Y H:i') }}
                        @if ($venta->confirmadaPor) por {{ $venta->confirmadaPor->name }} @endif
                    </p>
                @endif
                @if ($venta->cancelada_en)
                    <p>Cancelada: {{ $venta->cancelada_en->format('d/m/Y H:i') }}
                        @if ($venta->canceladaPor) por {{ $venta->canceladaPor->name }} @endif
                    </p>
                @endif
            </div>

            @if ($venta->estado === 'confirmada')
                <div class="border-t pt-4">
                    <a href="{{ route('admin.ventas.ticket', $venta) }}" target="_blank"
                       class="block text-center w-full bg-gray-900 hover:bg-black text-white text-sm font-medium px-4 py-2.5 rounded-md">
                        Ver comprobante
                    </a>
                </div>
            @endif

            @if ($venta->estado === 'pendiente')
                <div class="border-t pt-4 space-y-2">
                    <form method="POST" action="{{ route('admin.ventas.confirmar', $venta) }}"
                          onsubmit="return confirm('¿Confirmar la venta? Se descontará el stock.');">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="w-full bg-green-700 hover:bg-green-800 text-white text-sm font-medium px-4 py-2.5 rounded-md">
                            Confirmar venta
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.ventas.cancelar', $venta) }}"
                          onsubmit="return confirm('¿Cancelar esta venta?');">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="w-full bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium px-4 py-2.5 rounded-md">
                            Cancelar venta
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

@endcomponent
