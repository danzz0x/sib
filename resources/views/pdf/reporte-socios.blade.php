<!DOCTYPE html>
<html>

<head>
    <title>Reporte de Socios - SIB</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #ccc;
            padding-bottom: 10px;
        }

        h2 {
            margin: 0;
            color: #333;
        }

        .meta {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .estado-activo {
            color: green;
            font-weight: bold;
        }

        .estado-inactivo {
            color: red;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>Padrón de Socios - SIB Potosí</h2>
        <div class="meta">
            Fecha de Reporte: {{ $fecha }} <br>
            Total Registros: {{ $total }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>RNI</th>
                <th>Nombre Completo</th>
                <th>C.I.</th>
                <th>Colegio</th>
                <th>Tipo</th>
                <th>Especialidad</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($socios as $socio)
                <tr>
                    <td>{{ $socio->rni ?? '-' }}</td>
                    <td>{{ $socio->nombre }}</td>
                    <td>{{ $socio->cedula }}</td>
                    <td>{{ $socio->colegio->sigla ?? substr($socio->colegio->nombre, 0, 15) }}</td>
                    <td>{{ $socio->tipoSocio->nombre }}</td>
                    <td>{{ $socio->especialidad }}</td>
                    <td>
                        <span class="{{ $socio->estado == 'Activo' ? 'estado-activo' : 'estado-inactivo' }}">
                            {{ $socio->estado }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
