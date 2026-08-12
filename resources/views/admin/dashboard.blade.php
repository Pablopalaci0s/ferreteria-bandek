@component('admin.layout')

@slot('header')
    Dashboard
@endslot

<p class="text-sm text-gray-500 mb-6">Bienvenido al panel, {{ auth()->user()->name }}.</p>

{{-- Tarjetas de métricas --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-5 rounded-lg border">
        <p class="text-xs text-gray-500 uppercase font-medium mb-1">Productos activos</p>
        <p class="text-2xl font-semibold text-gray-800">{{ $totalProductos }}</p>
    </div>

    <div class="bg-white p-5 rounded-lg border">
        <p class="text-xs text-gray-500 uppercase font-medium mb-1">Stock bajo (≤10)</p>
        <p class="text-2xl font-semibold {{ $productosStockBajo->count() > 0 ? 'text-red-700' : 'text-gray-800' }}">
            {{ $productosStockBajo->count() }}
        </p>
    </div>

    <div class="bg-white p-5 rounded-lg border">
        <p class="text-xs text-gray-500 uppercase font-medium mb-1">Movimientos este mes</p>
        <p class="text-2xl font-semibold text-gray-800">{{ $movimientosDelMes }}</p>
    </div>

    <div class="bg-white p-5 rounded-lg border">
        <p class="text-xs text-gray-500 uppercase font-medium mb-1">Usuarios</p>
        <p class="text-2xl font-semibold text-gray-800">{{ $totalUsuarios }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $totalAdmins }} admin · {{ $totalVendedores }} vendedor{{ $totalVendedores === 1 ? '' : 'es' }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Productos con stock bajo --}}
    <div class="bg-white border rounded-lg overflow-hidden">
        <div class="px-5 py-3 border-b bg-gray-50">
            <p class="text-sm font-medium text-gray-700">Productos con stock bajo</p>
        </div>
        <table class="w-full text-sm">
            <thead class="text-left text-gray-500 uppercase text-xs">
                <tr>
                    <th class="p-3">Producto</th>
                    <th class="p-3 text-right">Stock</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($productosStockBajo as $producto)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 text-gray-800">{{ $producto->nombre }}</td>
                        <td class="p-3 text-right">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $producto->stock == 0 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $producto->stock }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="p-6 text-center text-gray-400">Ningún producto con stock bajo. 🎉</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Movimientos recientes --}}
    <div class="bg-white border rounded-lg overflow-hidden">
        <div class="px-5 py-3 border-b bg-gray-50 flex justify-between items-center">
            <p class="text-sm font-medium text-gray-700">Movimientos recientes</p>
            <a href="{{ route('admin.inventario.index') }}" class="text-xs text-red-800 hover:underline">Ver todos</a>
        </div>
        <table class="w-full text-sm">
            <thead class="text-left text-gray-500 uppercase text-xs">
                <tr>
                    <th class="p-3">Producto</th>
                    <th class="p-3">Tipo</th>
                    <th class="p-3 text-right">Cant.</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($movimientosRecientes as $movimiento)
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
                        <td class="p-3 text-gray-800">{{ $movimiento->producto->nombre }}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $colores[$movimiento->tipo] ?? 'bg-gray-100 text-gray-500' }}">
                                {{ ucfirst($movimiento->tipo) }}
                            </span>
                        </td>
                        <td class="p-3 text-right text-gray-700">{{ $movimiento->cantidad }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="p-6 text-center text-gray-400">Sin movimientos aún.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endcomponent