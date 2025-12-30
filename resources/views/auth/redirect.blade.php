<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Redirigiendo...</title>
</head>
<body>
    <script>
        // Usar location.replace para no dejar rastro en el historial
        window.location.replace('{{ $url }}');
    </script>
    <p>Redirigiendo...</p>
</body>
</html>
