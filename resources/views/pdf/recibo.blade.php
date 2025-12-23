<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Recibo de Pago</title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        /* Encabezado */
        .header {
            width: 100%;
            border-bottom: 2px solid #166534;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .logo {
            width: 80px;
            float: left;
        }

        .company-info {
            float: right;
            text-align: right;
        }

        .title {
            text-align: center;
            position: absolute;
            width: 100%;
            top: 20px;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            color: #166534;
        }

        /* Detalles */
        .details-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .row {
            display: block;
            clear: both;
            margin-bottom: 5px;
        }

        .label {
            font-weight: bold;
            width: 120px;
            display: inline-block;
            color: #555;
        }

        /* Tabla */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background-color: #166534;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }

        td {
            border-bottom: 1px solid #ddd;
            padding: 10px;
        }

        .total-row td {
            border-top: 2px solid #166534;
            font-weight: bold;
            font-size: 14px;
            background-color: #f0fdf4;
        }

        /* Footer */
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .qr {
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="header">
            <div class="title">Comprobante de Ingreso</div>
            <div class="company-info">
                <strong>Sociedad de Ingenieros de Bolivia</strong><br>
                Departamental Potosí<br>
                NIT: 1020304050<br>
                Fecha: {{ $pago->fecha_pago->format('d/m/Y') }}
            </div>
            <div style="clear: both;"></div>
        </div>

        <div class="details-box">
            <div class="row"><span class="label">Recibido de:</span> {{ $pago->socio->nombre }}</div>
            <div class="row"><span class="label">C.I. / RNI:</span> {{ $pago->socio->cedula }} /
                {{ $pago->socio->rni }}</div>
            <div class="row"><span class="label">Colegio:</span> {{ $pago->socio->colegio->nombre }}</div>
            <div class="row"><span class="label">Nro. Transacción:</span> {{ $pago->nro_transaccion ?? 'Efectivo' }}
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Cantidad</th>
                    <th>Descripción / Concepto</th>
                    <th style="text-align: right;">Periodo</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>
                        {{ $pago->concepto->nombre }}
                        <br>
                        <small style="color: #666;">{{ $pago->observacion }}</small>
                    </td>
                    <td style="text-align: right;">
                        @if ($pago->periodo_inicio)
                            {{ \Carbon\Carbon::parse($pago->periodo_inicio)->format('M Y') }} al
                            {{ \Carbon\Carbon::parse($pago->periodo_fin)->format('M Y') }}
                        @else
                            Pago Único
                        @endif
                    </td>
                    <td style="text-align: right;">{{ number_format($pago->monto_pagado, 2) }} Bs</td>
                </tr>
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;">TOTAL PAGADO:</td>
                    <td style="text-align: right;">{{ number_format($pago->monto_pagado, 2) }} Bs</td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            <p>Este documento es un comprobante válido de su aporte a la institución.</p>
            <p>Generado electrónicamente por el Sistema SIB Potosí el {{ now()->format('d/m/Y H:i') }}</p>
            <p>Usuario responsable: {{ $pago->usuario->name ?? 'Sistema Web' }}</p>
        </div>

    </div>
</body>

</html>
