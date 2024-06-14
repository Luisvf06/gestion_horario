<!DOCTYPE html>
<html>
<head>
    <title>Ausencias del Día</title>
</head>
<body>
    <h1>Ausencias del Día</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Fecha</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($ausencias as $ausencia)
            <tr>
                <td>{{ $ausencia->id }}</td>
                <td>{{ $ausencia->nombre }}</td>
                <td>{{ $ausencia->fecha }}</td>

            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
