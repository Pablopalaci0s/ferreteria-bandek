@component('admin.layout')

@slot('header')
    Solicitudes de precio
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

<h2 class="text-sm font-semibold text-gray-700 mb-3">
    Pendientes de aprobación ({{ $pendientes->count() }})
</h2>

<div class="bg-white border rounded-lg overflow-hidden mb-8">

    <table class="w-full text-sm">

        <thead class="bg-gray-50 text-left text-gray-500 uppercase text-xs">
            <tr>
                <th class="p-3">Producto</th>
                <th class="p-3">Solicitado por</th>
                <th class="p-3">Precio actual</th>
                <th class="p-3">Precio propuesto</th>
                <th class="p-3">Fecha</th>
                <th class="p-3 text-right">Acciones</th>
            </tr>
        </thead>

        <tbody class="divide-y">

            @forelse ($pendientes as $solicitud)

                <tr class="hover:bg-gray-50">

                    <td class="p-3 font-medium text-gray-800">
                        {{ $solicitud->producto->nombre ?? 'Producto eliminado' }}
                    </td>

                    <td class="p-3 text-gray-500">
                        {{ $solicitud->solicitante->name ?? '—' }}
                    </td>

                    <td class="p-3 text-gray-500">
                        ${{ number_format($solicitud->precio_actual, 2) }}
                        @if (! is_null($solicitud->precio_oferta_actual))
                            <span class="block text-xs text-gray-400">
                                Oferta: ${{ number_format($solicitud->precio_oferta_actual, 2) }}
                            </span>
                        @endif
                    </td>

                    <td class="p-3 text-red-700 font-semibold">
                        ${{ number_format($solicitud->precio_nuevo, 2) }}
                        @if (! is_null($solicitud->precio_oferta_nuevo))
                            <span class="block text-xs text-red-500 font-normal">
                                Oferta: ${{ number_format($solicitud->precio_oferta_nuevo, 2) }}
                            </span>
                        @endif
                    </td>

                    <td class="p-3 text-gray-500 text-xs">
                        {{ $solicitud->created_at->format('d/m/Y H:i') }}
                    </td>

                    <td class="p-3 text-right">

                        <div class="flex justify-end gap-2">

                            <form action="{{ route('admin.solicitudes-precio.aprobar', $solicitud) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button
                                    type="submit"
                                    class="bg-green-600 hover:bg-green-700 text-white text-xs font-medium px-3 py-1.5 rounded-md"
                                >
                                    Aprobar
                                </button>
                            </form>

                            <form
                                action="{{ route('admin.solicitudes-precio.rechazar', $solicitud) }}"
                                method="POST"
                                onsubmit="return confirm('¿Rechazar este cambio de precio?')"
                            >
                                @csrf
                                @method('PUT')
                                <button
                                    type="submit"
                                    class="bg-gray-100 hover:bg-red-100 text-gray-600 hover:text-red-700 text-xs font-medium px-3 py-1.5 rounded-md"
                                >
                                    Rechazar
                                </button>
                            </form>

                        </div>

                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="6" class="p-6 text-center text-sm text-gray-400">
                        No hay solicitudes de precio pendientes.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

</div>

<h2 class="text-sm font-semibold text-gray-700 mb-3">
    Historial reciente
</h2>

<div class="bg-white border rounded-lg overflow-hidden">

    <table class="w-full text-sm">

        <thead class="bg-gray-50 text-left text-gray-500 uppercase text-xs">
            <tr>
                <th class="p-3">Producto</th>
                <th class="p-3">Solicitado por</th>
                <th class="p-3">Precio propuesto</th>
                <th class="p-3">Estado</th>
                <th class="p-3">Revisado por</th>
                <th class="p-3">Fecha</th>
            </tr>
        </thead>

        <tbody class="divide-y">

            @forelse ($historial as $solicitud)

                <tr>

                    <td class="p-3 font-medium text-gray-800">
                        {{ $solicitud->producto->nombre ?? 'Producto eliminado' }}
                    </td>

                    <td class="p-3 text-gray-500">
                        {{ $solicitud->solicitante->name ?? '—' }}
                    </td>

                    <td class="p-3 text-gray-500">
                        ${{ number_format($solicitud->precio_nuevo, 2) }}
                    </td>

                    <td class="p-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $solicitud->estado === 'aprobado' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $solicitud->estado === 'aprobado' ? 'Aprobado' : 'Rechazado' }}
                        </span>
                    </td>

                    <td class="p-3 text-gray-500">
                        {{ $solicitud->revisor->name ?? '—' }}
                    </td>

                    <td class="p-3 text-gray-500 text-xs">
                        {{ $solicitud->revisado_en?->format('d/m/Y H:i') }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="6" class="p-6 text-center text-sm text-gray-400">
                        Todavía no hay solicitudes revisadas.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

</div>

@endcomponent
