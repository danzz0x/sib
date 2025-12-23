<!DOCTYPE html>
<html>

<head>
    <title>Reporte de Pagos SIB</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }

        .info {
            margin-bottom: 15px;
            font-size: 10px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .monto {
            text-align: right;
        }

        .total {
            font-weight: bold;
            text-align: right;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>Sociedad de Ingenieros de Bolivia - Potosí</h2>
        <h3>Reporte de Pagos e Ingresos</h3>
    </div>

    <div class="info">
        <p><strong>Generado por:</strong> {{ $usuario }}</p>
        <p><strong>Fecha de Emisión:</strong> {{ $fecha }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Socio / RNI</th>
                <th>Colegio</th>
                <th>Concepto</th>
                <th>Método</th>
                <th>Estado</th>
                <th class="monto">Monto (Bs)</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach ($pagos as $pago)
                @php $total += $pago->monto_pagado; @endphp
                <tr>
                    <td>{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                    <td>
                        {{ $pago->socio->nombre }} <br>
                        <small>RNI: {{ $pago->socio->rni }}</small>
                    </td>
                    <td>{{ $pago->socio->colegio->sigla ?? 'SIB' }}</td>
                    <td>
                        {{ $pago->concepto->nombre }} <br>
                        @if ($pago->periodo_inicio)
                            <small>{{ \Carbon\Carbon::parse($pago->periodo_inicio)->format('m/y') }} -
                                {{ \Carbon\Carbon::parse($pago->periodo_fin)->format('m/y') }}</small>
                        @endif
                    </td>
                    <td>{{ $pago->metodoPago->nombre }}</td>
                    <td>{{ ucfirst($pago->estado) }}</td>
                    <td class="monto">{{ number_format($pago->monto_pagado, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total Reportado: Bs {{ number_format($total, 2) }}
    </div>

</body>

</html>
