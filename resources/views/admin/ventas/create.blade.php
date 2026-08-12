@component('admin.layout')

    @slot('header')
        Nueva venta
    @endslot

    <a href="{{ route('admin.ventas.index') }}"
       class="text-sm text-gray-500 hover:text-red-800 mb-4 inline-block">
        &larr; Volver a ventas
    </a>

    @if ($errors->any())
        <div class="bg-red-50 text-red-800 border border-red-200 px-4 py-2 rounded-md mb-4 text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div
        x-data="ventaForm()"
        class="grid lg:grid-cols-3 gap-6"
    >

        {{-- BUSCADOR DE PRODUCTOS --}}
        <div class="lg:col-span-2 bg-white border rounded-lg p-5">

            <label class="block text-sm font-medium text-gray-700 mb-1">
                Buscar producto (nombre, SKU o modelo)
            </label>

            <input
                type="text"
                x-model="busqueda"
                @input.debounce.300ms="buscar()"
                placeholder="Escribí para buscar…"
                class="w-full border-gray-300 rounded-md text-sm mb-3"
            >

            <div class="border rounded-md divide-y max-h-72 overflow-y-auto" x-show="resultados.length > 0" x-cloak>
                <template x-for="p in resultados" :key="p.id">
                    <button
                        type="button"
                        @click="agregar(p)"
                        class="w-full text-left px-3 py-2 hover:bg-gray-50 flex justify-between items-center gap-3"
                    >
                        <span>
                            <span class="font-medium text-gray-800" x-text="p.nombre"></span>
                            <span class="block text-xs text-gray-400">
                                SKU: <span x-text="p.sku"></span> ·
                                Stock: <span x-text="p.stock"></span>
                            </span>
                        </span>
                        <span class="text-sm font-medium text-gray-700">$ <span x-text="p.precio_formato"></span></span>
                    </button>
                </template>
            </div>

            <p class="text-sm text-gray-400 mt-2" x-show="busqueda.length > 0 && resultados.length === 0" x-cloak>
                Sin resultados.
            </p>

            {{-- LÍNEAS DE LA VENTA --}}
            <table class="w-full text-sm mt-6" x-show="items.length > 0" x-cloak>
                <thead class="text-left text-gray-500 uppercase text-xs border-b">
                    <tr>
                        <th class="py-2">Producto</th>
                        <th class="py-2 w-24">Cantidad</th>
                        <th class="py-2 text-right">Unitario</th>
                        <th class="py-2 text-right">Subtotal</th>
                        <th class="py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <template x-for="(item, i) in items" :key="item.id">
                        <tr>
                            <td class="py-2">
                                <span class="font-medium text-gray-800" x-text="item.nombre"></span>
                                <span class="block text-xs" :class="item.cantidad > item.stock ? 'text-red-600' : 'text-gray-400'">
                                    Stock: <span x-text="item.stock"></span>
                                </span>
                            </td>
                            <td class="py-2">
                                <input
                                    type="number" min="1" :max="item.stock"
                                    x-model.number="item.cantidad"
                                    class="w-20 border-gray-300 rounded-md text-sm"
                                >
                            </td>
                            <td class="py-2 text-right text-gray-600">$ <span x-text="item.precio.toFixed(2)"></span></td>
                            <td class="py-2 text-right font-medium text-gray-800">$ <span x-text="(item.precio * item.cantidad).toFixed(2)"></span></td>
                            <td class="py-2 text-right">
                                <button type="button" @click="quitar(i)" class="text-gray-400 hover:text-red-700">✕</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
                <tfoot>
                    <tr class="border-t">
                        <td colspan="3" class="py-3 text-right font-medium text-gray-600">Total</td>
                        <td class="py-3 text-right text-lg font-bold text-gray-900">$ <span x-text="total().toFixed(2)"></span></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

            <p class="text-sm text-gray-400 mt-6" x-show="items.length === 0">
                Agregá productos a la venta desde el buscador.
            </p>
        </div>

        {{-- DATOS DEL CLIENTE + CONFIRMAR --}}
        <div class="bg-white border rounded-lg p-5 h-fit">
            <h3 class="font-medium text-gray-800 mb-4">Datos del cliente <span class="text-gray-400 text-xs">(opcional)</span></h3>

            <form method="POST" action="{{ route('admin.ventas.store') }}" @submit="prepararEnvio($event)">
                @csrf

                <label class="block text-sm text-gray-600 mb-1">Nombre</label>
                <input type="text" name="cliente_nombre" x-model="cliente.nombre" class="w-full border-gray-300 rounded-md text-sm mb-3">

                <label class="block text-sm text-gray-600 mb-1">Teléfono</label>
                <input type="text" name="cliente_telefono" x-model="cliente.telefono" class="w-full border-gray-300 rounded-md text-sm mb-3">

                <label class="block text-sm text-gray-600 mb-1">Notas</label>
                <textarea name="notas" x-model="cliente.notas" rows="2" class="w-full border-gray-300 rounded-md text-sm mb-4"></textarea>

                {{-- Inputs ocultos con los items --}}
                <template x-for="(item, i) in items" :key="'h' + item.id">
                    <span>
                        <input type="hidden" :name="`items[${i}][producto_id]`" :value="item.id">
                        <input type="hidden" :name="`items[${i}][cantidad]`" :value="item.cantidad">
                    </span>
                </template>

                <button
                    type="submit"
                    :disabled="items.length === 0"
                    class="w-full bg-red-800 hover:bg-red-900 disabled:opacity-40 text-white text-sm font-medium px-4 py-2.5 rounded-md"
                >
                    Registrar venta
                </button>
                <p class="text-xs text-gray-400 mt-2">Se guarda como <strong>pendiente</strong>. El stock se descuenta al confirmarla.</p>
            </form>
        </div>
    </div>

    <script>
        function ventaForm() {
            return {
                busqueda: '',
                resultados: [],
                items: [],
                cliente: { nombre: '', telefono: '', notas: '' },

                async buscar() {
                    if (this.busqueda.trim() === '') { this.resultados = []; return; }
                    const url = new URL(@js(route('admin.ventas.buscar-productos')));
                    url.searchParams.set('q', this.busqueda);
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();
                    this.resultados = data.productos;
                },

                agregar(p) {
                    const existe = this.items.find(i => i.id === p.id);
                    if (existe) {
                        existe.cantidad++;
                    } else {
                        this.items.push({ id: p.id, nombre: p.nombre, sku: p.sku, precio: p.precio, stock: p.stock, cantidad: 1 });
                    }
                    this.busqueda = '';
                    this.resultados = [];
                },

                quitar(i) { this.items.splice(i, 1); },

                total() { return this.items.reduce((t, i) => t + i.precio * (i.cantidad || 0), 0); },

                prepararEnvio(e) {
                    if (this.items.length === 0) { e.preventDefault(); }
                },
            }
        }
    </script>

@endcomponent
