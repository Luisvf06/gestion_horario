<!DOCTYPE html>
<html>
<head>
    <title>Ausencias del Día</title>
</head>
<body>
    <h1>Ausencias del Día</h1>
    <table border="1" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <th>User Name</th>
                <th>Fecha</th>
                <th>Hora</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ausencias as $ausencia)
                <tr>
                    <td>{{ $ausencia->user->user_name }}</td>
                    <td>{{ $ausencia->fecha }}</td>
                    <td>{{ $ausencia->hora ? $ausencia->hora : 'Todo el día' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
