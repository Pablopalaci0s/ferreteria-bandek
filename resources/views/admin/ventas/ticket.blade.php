<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Comprobante venta #{{ $venta->id }} — BANDEK</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, Segoe UI, Roboto, sans-serif; color: #1f2937; margin: 0; background: #f3f4f6; }
        .ticket { max-width: 420px; margin: 24px auto; background: #fff; padding: 24px; border: 1px solid #e5e7eb; border-radius: 8px; }
        .marca { text-align: center; border-bottom: 2px solid #991b1b; padding-bottom: 12px; margin-bottom: 12px; }
        .marca h1 { margin: 0; font-size: 20px; color: #991b1b; letter-spacing: 1px; }
        .marca p { margin: 2px 0 0; font-size: 12px; color: #6b7280; }
        .meta { font-size: 12px; color: #6b7280; margin-bottom: 12px; }
        .meta strong { color: #1f2937; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; margin: 12px 0; }
        th { text-align: left; border-bottom: 1px solid #e5e7eb; padding: 6px 0; font-size: 11px; text-transform: uppercase; color: #6b7280; }
        td { padding: 6px 0; border-bottom: 1px solid #f3f4f6; }
        .der { text-align: right; }
        .cen { text-align: center; }
        .total { display: flex; justify-content: space-between; font-size: 18px; font-weight: bold; margin-top: 12px; padding-top: 12px; border-top: 2px solid #111827; }
        .pie { text-align: center; font-size: 11px; color: #9ca3af; margin-top: 16px; }
        .acciones { max-width: 420px; margin: 0 auto 24px; text-align: center; }
        .acciones button { background: #991b1b; color: #fff; border: 0; padding: 10px 20px; border-radius: 6px; font-size: 14px; cursor: pointer; }
        @media print {
            body { background: #fff; }
            .ticket { border: 0; margin: 0; }
            .acciones { display: none; }
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="marca">
            <h1>FERRETERÍA BANDEK</h1>
            <p>Comprobante de venta</p>
        </div>

        <div class="meta">
            <div>Venta <strong>#{{ $venta->id }}</strong></div>
            <div>Fecha: {{ ($venta->confirmada_en ?? $venta->created_at)->format('d/m/Y H:i') }}</div>
            @if ($venta->cliente_nombre)
                <div>Cliente: <strong>{{ $venta->cliente_nombre }}</strong></div>
            @endif
            @if ($venta->cliente_telefono)
                <div>Tel: {{ $venta->cliente_telefono }}</div>
            @endif
            <div>Atendió: {{ $venta->vendedor->name ?? '—' }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th class="cen">Cant.</th>
                    <th class="der">Unit.</th>
                    <th class="der">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($venta->detalles as $detalle)
                    <tr>
                        <td>{{ $detalle->producto_nombre }}</td>
                        <td class="cen">{{ $detalle->cantidad }}</td>
                        <td class="der">{{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td class="der">{{ number_format($detalle->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total">
            <span>TOTAL</span>
            <span>$ {{ number_format($venta->total, 2) }}</span>
        </div>

        <div class="pie">¡Gracias por su compra!</div>
    </div>

    <div class="acciones">
        <button onclick="window.print()">Imprimir / Guardar PDF</button>
    </div>
</body>
</html>
