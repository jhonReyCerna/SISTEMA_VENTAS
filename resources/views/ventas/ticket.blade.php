<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            width: 80mm;
            margin: 0 auto;
            font-size: 11px;
        }
        .ticket {
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 16px;
            font-weight: bold;
        }
        .separator {
            border-bottom: 1px dashed #000;
            margin: 5px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        th {
            border-bottom: 1px solid #000;
            padding: 5px 0;
            text-align: left;
        }
        td {
            padding: 3px 0;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total {
            font-size: 14px;
            font-weight: bold;
            margin-top: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 10px;
        }
        @media print {
            body {
                width: 80mm;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <div class="company-name">SISTEMA DE VENTAS</div>
            <div>RUC: 20XXXXXXXXX</div>
            <div>Av. Principal 123</div>
            <div>Tel: (01) 123-4567</div>
        </div>

        <div class="separator"></div>

        <div class="text-center" style="margin: 10px 0;">
            <strong>TICKET DE VENTA</strong><br>
            N° {{ str_pad($venta->id_venta, 8, '0', STR_PAD_LEFT) }}
        </div>

        <div class="separator"></div>

        <div class="info-row">
            <span>Fecha:</span>
            <span>{{ $venta->fecha_venta->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span>Cliente:</span>
            <span>{{ Str::limit($venta->cliente->nombre, 25) }}</span>
        </div>
        <div class="info-row">
            <span>Vendedor:</span>
            <span>{{ Str::limit($venta->usuario->nombre ?? 'N/A', 25) }}</span>
        </div>

        <div class="separator"></div>

        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th class="text-center">Cant</th>
                    <th class="text-right">Importe</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venta->detalles as $detalle)
                <tr>
                    <td colspan="3">{{ Str::limit($detalle->producto->nombre ?? 'Producto', 35) }}</td>
                </tr>
                <tr>
                    <td>S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td class="text-center">{{ $detalle->cantidad }}</td>
                    <td class="text-right">S/ {{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="separator"></div>

        <div class="info-row">
            <span>Subtotal:</span>
            <span>S/ {{ number_format($venta->total / 1.18, 2) }}</span>
        </div>
        <div class="info-row">
            <span>IGV (18%):</span>
            <span>S/ {{ number_format($venta->total - ($venta->total / 1.18), 2) }}</span>
        </div>
        
        <div class="separator"></div>

        <div class="info-row total">
            <span>TOTAL:</span>
            <span>S/ {{ number_format($venta->total, 2) }}</span>
        </div>

        <div class="separator"></div>

        <div class="footer">
            <p>¡Gracias por su compra!</p>
            <p>{{ now()->format('d/m/Y H:i:s') }}</p>
        </div>

        <div class="no-print" style="margin-top: 20px; text-align: center;">
            <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">
                Imprimir Ticket
            </button>
            <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; cursor: pointer; margin-left: 10px;">
                Cerrar
            </button>
        </div>
    </div>
</body>
</html>
