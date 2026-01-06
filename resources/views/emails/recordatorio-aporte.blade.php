<!DOCTYPE html>
<html>

<head>
    <title>Recordatorio de Aporte</title>
</head>

<body>
    <h1>Hola, {{ $socio->nombre }}</h1>

    <p>Esperamos que te encuentres bien.</p>

    <p>Este es un recordatorio amable de que tu aporte correspondiente al mes de
        <strong>{{ $nombreMes }}</strong> está próximo a vencer.
    </p>

    <p>Por favor, realiza tu pago para mantener tu estado activo en la S.I.B.</p>

    <p>Saludos,<br>
        La Administración.</p>
</body>

</html>
