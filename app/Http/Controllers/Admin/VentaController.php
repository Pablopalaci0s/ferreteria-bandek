<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $ventas = Venta::with('vendedor')
            ->when(! $this->esAdmin($request), function ($q) use ($request) {
                $q->where('vendedor_id', $request->user()->id);
            })
            ->when($request->filled('estado'), function ($q) use ($request) {
                $q->where('estado', $request->string('estado')->toString());
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.ventas.index', compact('ventas'));
    }

    public function create()
    {
        return view('admin.ventas.create');
    }

    /**
     * Reporte / cierre de caja: totales de ventas confirmadas en un rango de
     * fechas (por defecto, hoy), desglose por vendedor y productos más vendidos.
     */
    public function reportes(Request $request)
    {
        $desde = $request->date('desde')?->startOfDay() ?? now()->startOfDay();
        $hasta = $request->date('hasta')?->endOfDay() ?? now()->endOfDay();

        $esAdmin = $this->esAdmin($request);
        $usuarioId = $request->user()->id;

        // Base: ventas confirmadas en el rango, scopeadas por rol.
        $ventasBase = Venta::query()
            ->confirmadas()
            ->whereBetween('confirmada_en', [$desde, $hasta])
            ->when(! $esAdmin, fn ($q) => $q->where('vendedor_id', $usuarioId));

        $totalVentas = (clone $ventasBase)->sum('total');
        $cantidadVentas = (clone $ventasBase)->count();

        // Desglose por vendedor (solo tiene sentido para el admin).
        $ventasPorVendedor = $esAdmin
            ? (clone $ventasBase)
                ->select('vendedor_id', DB::raw('COUNT(*) as cantidad'), DB::raw('SUM(total) as importe'))
                ->groupBy('vendedor_id')
                ->with('vendedor')
                ->get()
            : collect();

        // Productos más vendidos en el rango.
        $productosMasVendidos = VentaDetalle::query()
            ->select('producto_nombre', DB::raw('SUM(cantidad) as unidades'), DB::raw('SUM(subtotal) as importe'))
            ->whereHas('venta', function ($q) use ($desde, $hasta, $esAdmin, $usuarioId) {
                $q->where('estado', Venta::ESTADO_CONFIRMADA)
                    ->whereBetween('confirmada_en', [$desde, $hasta])
                    ->when(! $esAdmin, fn ($sub) => $sub->where('vendedor_id', $usuarioId));
            })
            ->groupBy('producto_nombre')
            ->orderByDesc('unidades')
            ->take(10)
            ->get();

        return view('admin.ventas.reportes', [
            'desde' => $desde,
            'hasta' => $hasta,
            'totalVentas' => $totalVentas,
            'cantidadVentas' => $cantidadVentas,
            'ventasPorVendedor' => $ventasPorVendedor,
            'productosMasVendidos' => $productosMasVendidos,
            'esAdmin' => $esAdmin,
        ]);
    }

    /**
     * Comprobante imprimible de la venta (para guardar como PDF o mandar por
     * WhatsApp como imagen). Usa una vista independiente, sin el panel.
     */
    public function ticket(Request $request, Venta $venta)
    {
        $this->autorizarAcceso($request, $venta);

        $venta->load(['detalles', 'vendedor']);

        return view('admin.ventas.ticket', compact('venta'));
    }

    /**
     * Búsqueda de productos para armar la venta (JSON, uso interno del panel).
     * Devuelve stock y precio final autoritativos del servidor.
     */
    public function buscarProductos(Request $request)
    {
        $buscar = trim($request->input('q', ''));

        $productos = Producto::where('activo', true)
            ->when($buscar !== '', function ($q) use ($buscar) {
                $q->where(function ($sub) use ($buscar) {
                    $sub->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('sku', 'like', "%{$buscar}%")
                        ->orWhere('modelo', 'like', "%{$buscar}%");
                });
            })
            ->orderBy('nombre')
            ->take(15)
            ->get();

        return response()->json([
            'productos' => $productos->map(fn ($p) => [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'sku' => $p->sku,
                'modelo' => $p->modelo,
                'precio' => (float) $p->precio_final,
                'precio_formato' => number_format($p->precio_final, 2),
                'stock' => $p->stock,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'cliente_nombre' => 'nullable|string|max:255',
            'cliente_telefono' => 'nullable|string|max:40',
            'notas' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
        ]);

        $venta = DB::transaction(function () use ($datos, $request) {
            $venta = Venta::create([
                'vendedor_id' => $request->user()->id,
                'estado' => Venta::ESTADO_PENDIENTE,
                'cliente_nombre' => $datos['cliente_nombre'] ?? null,
                'cliente_telefono' => $datos['cliente_telefono'] ?? null,
                'notas' => $datos['notas'] ?? null,
                'total' => 0,
            ]);

            $total = 0;

            foreach ($this->consolidarItems($datos['items']) as $productoId => $cantidad) {
                $producto = Producto::findOrFail($productoId);

                // El precio SIEMPRE se toma del servidor, nunca del cliente.
                $precio = (float) $producto->precio_final;
                $subtotal = round($precio * $cantidad, 2);

                $venta->detalles()->create([
                    'producto_id' => $producto->id,
                    'producto_nombre' => $producto->nombre,
                    'producto_sku' => $producto->sku,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            $venta->update(['total' => $total]);

            return $venta;
        });

        return redirect()
            ->route('admin.ventas.show', $venta)
            ->with('status', 'Venta registrada como pendiente. Confirmala para descontar el stock.');
    }

    public function show(Request $request, Venta $venta)
    {
        $this->autorizarAcceso($request, $venta);

        $venta->load(['detalles', 'vendedor', 'confirmadaPor', 'canceladaPor']);

        return view('admin.ventas.show', compact('venta'));
    }

    /**
     * Confirma una venta pendiente: verifica stock, registra los movimientos
     * de salida y descuenta el stock, todo dentro de una transacción con
     * bloqueos que impiden condiciones de carrera y doble descuento.
     */
    public function confirmar(Request $request, Venta $venta)
    {
        $this->autorizarAcceso($request, $venta);

        $bajoMinimo = [];

        try {
            DB::transaction(function () use ($venta, $request, &$bajoMinimo) {
                // Bloquea la fila de la venta: si dos requests intentan
                // confirmar la misma venta, se serializan aquí.
                $venta = Venta::whereKey($venta->id)->lockForUpdate()->firstOrFail();

                // Guard de idempotencia -> evita doble descuento.
                if (! $venta->estaPendiente()) {
                    throw ValidationException::withMessages([
                        'estado' => "La venta ya está {$venta->estado}; no se puede volver a confirmar.",
                    ]);
                }

                $detalles = $venta->detalles()->get();

                // Bloquea los productos involucrados: dos ventas distintas que
                // consuman el mismo stock se serializan y solo una gana.
                $productos = Producto::whereIn('id', $detalles->pluck('producto_id')->filter())
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                // 1) Verificar TODO el stock antes de tocar nada.
                foreach ($detalles as $detalle) {
                    $producto = $productos->get($detalle->producto_id);

                    if (! $producto) {
                        throw ValidationException::withMessages([
                            'stock' => "El producto \"{$detalle->producto_nombre}\" ya no existe.",
                        ]);
                    }

                    if ($detalle->cantidad > $producto->stock) {
                        throw ValidationException::withMessages([
                            'stock' => "Stock insuficiente de \"{$producto->nombre}\" (disponible: {$producto->stock}, pedido: {$detalle->cantidad}).",
                        ]);
                    }
                }

                // 2) Aplicar: movimiento de salida por venta + descuento de stock.
                foreach ($detalles as $detalle) {
                    $producto = $productos->get($detalle->producto_id);

                    $producto->registrarMovimiento(
                        'venta',
                        $detalle->cantidad,
                        'Venta #'.$venta->id,
                        $request->user()->id,
                        $venta->id
                    );

                    // Tras descontar, ¿quedó en o por debajo del mínimo?
                    if ($producto->stock <= $producto->stock_minimo) {
                        $bajoMinimo[] = "{$producto->nombre} (quedan {$producto->stock})";
                    }
                }

                // 3) Marcar confirmada + auditoría.
                $venta->update([
                    'estado' => Venta::ESTADO_CONFIRMADA,
                    'confirmada_por' => $request->user()->id,
                    'confirmada_en' => now(),
                ]);
            });
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }

        $redirect = redirect()
            ->route('admin.ventas.show', $venta)
            ->with('status', 'Venta confirmada. Se descontó el stock automáticamente.');

        if (! empty($bajoMinimo)) {
            $redirect->with('alerta_stock', 'Stock bajo tras la venta: '.implode(', ', $bajoMinimo).'.');
        }

        return $redirect;
    }

    public function cancelar(Request $request, Venta $venta)
    {
        $this->autorizarAcceso($request, $venta);

        if (! $venta->estaPendiente()) {
            return back()->with('error', 'Solo se puede cancelar una venta pendiente.');
        }

        $venta->update([
            'estado' => Venta::ESTADO_CANCELADA,
            'cancelada_por' => $request->user()->id,
            'cancelada_en' => now(),
        ]);

        return redirect()
            ->route('admin.ventas.show', $venta)
            ->with('status', 'Venta cancelada. No se modificó el stock.');
    }

    /**
     * Suma las cantidades si el mismo producto viene repetido en el formulario,
     * para que cada producto aparezca una sola vez en la venta.
     *
     * @return array<int, int> [producto_id => cantidad]
     */
    private function consolidarItems(array $items): array
    {
        $consolidado = [];

        foreach ($items as $item) {
            $id = (int) $item['producto_id'];
            $consolidado[$id] = ($consolidado[$id] ?? 0) + (int) $item['cantidad'];
        }

        return $consolidado;
    }

    private function esAdmin(Request $request): bool
    {
        return $request->user()->rol === 'admin';
    }

    private function autorizarAcceso(Request $request, Venta $venta): void
    {
        abort_unless(
            $this->esAdmin($request) || $venta->vendedor_id === $request->user()->id,
            403
        );
    }
}
