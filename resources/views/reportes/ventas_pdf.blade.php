<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        .header h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 14px;
            color: #666;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        thead {
            background-color: #4472C4;
            color: white;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            font-weight: bold;
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals {
            margin-top: 20px;
            text-align: right;
        }
        .totals p {
            font-size: 14px;
            margin: 5px 0;
        }
        .totals .grand-total {
            font-size: 18px;
            font-weight: bold;
            color: #4472C4;
        }
        .footer {
            margin-top: 40px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE VENTAS</h1>
        <p>Sistema de Ventas - Reporte Generado</p>
        @if(isset($fecha_inicio) && isset($fecha_fin))
            <p>Período: {{ $fecha_inicio->format('d/m/Y') }} - {{ $fecha_fin->format('d/m/Y') }}</p>
        @else
            <p>Todas las ventas registradas</p>
        @endif
    </div>

    <div class="info-section">
        <div class="info-row">
            <strong>Fecha de generación:</strong>
            <span>{{ now()->format('d/m/Y H:i:s') }}</span>
        </div>
        <div class="info-row">
            <strong>Total de ventas:</strong>
            <span>{{ count($ventas) }} registros</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>N° Venta</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th class="text-right">Total (S/)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalGeneral = 0; @endphp
            @foreach($ventas as $venta)
                <tr>
                    <td class="text-center">{{ str_pad($venta->id_venta, 8, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $venta->fecha_venta->format('d/m/Y H:i') }}</td>
                    <td>{{ $venta->cliente->nombre ?? 'N/A' }}</td>
                    <td>{{ $venta->usuario->nombre ?? 'N/A' }}</td>
                    <td class="text-right">S/ {{ number_format($venta->total, 2) }}</td>
                </tr>
                @php $totalGeneral += $venta->total; @endphp
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <p class="grand-total">TOTAL GENERAL: S/ {{ number_format($totalGeneral, 2) }}</p>
        <p>Promedio por venta: S/ {{ count($ventas) > 0 ? number_format($totalGeneral / count($ventas), 2) : '0.00' }}</p>
    </div>

    <div class="footer">
        <p>Sistema de Ventas © {{ date('Y') }} - Reporte confidencial</p>
        <p>Generado automáticamente el {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
