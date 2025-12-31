<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #{{ $venta->id_venta }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .info-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .info-box {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 5px;
        }
        .info-box h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #555;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .info-box p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            border: 1px solid #dee2e6;
            padding: 8px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals {
            margin-top: 20px;
            float: right;
            width: 300px;
        }
        .totals table {
            margin: 0;
        }
        .totals th {
            background-color: #fff;
            text-align: right;
            border: none;
            border-bottom: 1px solid #ddd;
        }
        .totals td {
            text-align: right;
            border: none;
            border-bottom: 1px solid #ddd;
        }
        .total-final {
            font-size: 16px;
            font-weight: bold;
            background-color: #28a745 !important;
            color: white !important;
        }
        .footer {
            clear: both;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #777;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">SISTEMA DE VENTAS</div>
        <p>RUC: 20XXXXXXXXX | Dirección: Av. Principal 123, Lima, Perú</p>
        <p>Teléfono: (01) 123-4567 | Email: ventas@sistema.com</p>
    </div>

    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="margin: 0;">FACTURA ELECTRÓNICA</h2>
        <h3 style="margin: 5px 0;">N° {{ str_pad($venta->id_venta, 8, '0', STR_PAD_LEFT) }}</h3>
    </div>

    <div class="info-row">
        <div class="info-col">
            <div class="info-box">
                <h3>DATOS DEL CLIENTE</h3>
                <p><strong>Cliente:</strong> {{ $venta->cliente->nombre }}</p>
                <p><strong>Documento:</strong> {{ $venta->cliente->documento ?? 'N/A' }}</p>
                <p><strong>Dirección:</strong> {{ $venta->cliente->direccion ?? 'N/A' }}</p>
                <p><strong>Teléfono:</strong> {{ $venta->cliente->telefono ?? 'N/A' }}</p>
            </div>
        </div>
        <div class="info-col">
            <div class="info-box">
                <h3>DATOS DE LA VENTA</h3>
                <p><strong>Fecha:</strong> {{ $venta->fecha_venta->format('d/m/Y H:i') }}</p>
                <p><strong>Vendedor:</strong> {{ $venta->usuario->nombre ?? 'N/A' }}</p>
                <p><strong>Método de Pago:</strong> {{ $venta->metodo_pago ?? 'Efectivo' }}</p>
                <p><strong>Estado:</strong> <span style="color: green;">Pagado</span></p>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="10%">Cant.</th>
                <th width="50%">Descripción</th>
                <th width="15%" class="text-right">P. Unit.</th>
                <th width="25%" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $detalle)
            <tr>
                <td class="text-center">{{ $detalle->cantidad }}</td>
                <td>{{ $detalle->producto->nombre ?? 'Producto' }}</td>
                <td class="text-right">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                <td class="text-right">S/ {{ number_format($detalle->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <th>Subtotal:</th>
                <td>S/ {{ number_format($venta->total / 1.18, 2) }}</td>
            </tr>
            <tr>
                <th>IGV (18%):</th>
                <td>S/ {{ number_format($venta->total - ($venta->total / 1.18), 2) }}</td>
            </tr>
            <tr class="total-final">
                <th>TOTAL:</th>
                <td>S/ {{ number_format($venta->total, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Gracias por su compra. Documento generado el {{ now()->format('d/m/Y H:i') }}</p>
        <p>Este documento es una representación impresa de la Factura Electrónica</p>
    </div>
</body>
</html>
