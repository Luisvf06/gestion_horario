<!DOCTYPE html>
<html>
<head>
    <title>Ausencias del Día</title>
</head>
<body>
    <h1>{{ $title }}</h1>
    <table border="1" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Aula</th>
                <th>Grupo</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ausencias as $entry)
                <tr>
                    <td>{{ $entry['ausencia']->id }}</td>
                    <td>{{ $entry['ausencia']->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($entry['ausencia']->fecha)->format('d-m-Y') }}</td>
                    <td>{{ $entry['ausencia']->hora ? $entry['ausencia']->hora : 'Todo el día' }}</td>
                    <td>{{ $entry['aula'] ? $entry['aula']->descripcion : 'N/A' }}</td>
                    <td>{{ $entry['grupo'] ? $entry['grupo']->descripcion : 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
