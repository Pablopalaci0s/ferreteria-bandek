@component('admin.layout')

    @slot('header')
        Ventas
    @endslot

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

    <div class="flex justify-between items-center mb-6">
        <div class="flex gap-2">
            <a href="{{ route('admin.ventas.index') }}"
               class="text-sm px-3 py-1.5 rounded-md border {{ !request('estado') ? 'bg-gray-900 text-white' : 'bg-white text-gray-600' }}">Todas</a>
            <a href="{{ route('admin.ventas.index', ['estado' => 'pendiente']) }}"
               class="text-sm px-3 py-1.5 rounded-md border {{ request('estado') === 'pendiente' ? 'bg-gray-900 text-white' : 'bg-white text-gray-600' }}">Pendientes</a>
            <a href="{{ route('admin.ventas.index', ['estado' => 'confirmada']) }}"
               class="text-sm px-3 py-1.5 rounded-md border {{ request('estado') === 'confirmada' ? 'bg-gray-900 text-white' : 'bg-white text-gray-600' }}">Confirmadas</a>
            <a href="{{ route('admin.ventas.index', ['estado' => 'cancelada']) }}"
               class="text-sm px-3 py-1.5 rounded-md border {{ request('estado') === 'cancelada' ? 'bg-gray-900 text-white' : 'bg-white text-gray-600' }}">Canceladas</a>
        </div>

        <a href="{{ route('admin.ventas.create') }}"
           class="bg-red-800 hover:bg-red-900 text-white text-sm font-medium px-4 py-2 rounded-md">
            + Nueva venta
        </a>
    </div>

    <div class="bg-white border rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-500 uppercase text-xs">
                <tr>
                    <th class="p-3">#</th>
                    <th class="p-3">Fecha</th>
                    <th class="p-3">Cliente</th>
                    <th class="p-3">Vendedor</th>
                    <th class="p-3">Estado</th>
                    <th class="p-3 text-right">Total</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @php
                    $colores = [
                        'pendiente' => 'bg-amber-100 text-amber-700',
                        'confirmada' => 'bg-green-100 text-green-700',
                        'cancelada' => 'bg-gray-200 text-gray-600',
                    ];
                @endphp
                @forelse ($ventas as $venta)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-medium text-gray-700">#{{ $venta->id }}</td>
                        <td class="p-3 text-gray-500">{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                        <td class="p-3 text-gray-700">{{ $venta->cliente_nombre ?? '—' }}</td>
                        <td class="p-3 text-gray-500">{{ $venta->vendedor->name ?? '—' }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $colores[$venta->estado] ?? 'bg-gray-100 text-gray-500' }}">
                                {{ ucfirst($venta->estado) }}
                            </span>
                        </td>
                        <td class="p-3 text-right font-medium text-gray-800">$ {{ number_format($venta->total, 2) }}</td>
                        <td class="p-3 text-right">
                            <a href="{{ route('admin.ventas.show', $venta) }}" class="text-red-800 hover:underline">Ver</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-6 text-center text-gray-400">Todavía no hay ventas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $ventas->links() }}
    </div>

@endcomponent
