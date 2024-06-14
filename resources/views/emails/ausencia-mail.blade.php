<!DOCTYPE html>
<html>
<head>
    <title>Reporte diario de ausencias</title>
</head>
<body>
    <h1>Reporte diario de ausencias</h1>
    <p>Estimado(a) {{ $data['user_name'] }},</p>
    <p>Adjunto a este correo encontrarás el reporte de ausencias del día {{ $data['date'] }}.</p>

    <p>Resumen de ausencias:</p>
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
            @foreach ($data['ausencias'] as $entry)
                <tr>
                    <td>{{ $entry['ausencia']->id }}</td>
                    <td>{{ $entry['ausencia']->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($entry['ausencia']->fecha)->format('d-m-Y') }}</td>
                    <td>{{ $entry['ausencia']->hora }}</td>
                    <td>{{ $entry['aula'] ? $entry['aula']->descripcion : 'N/A' }}</td>
                    <td>{{ $entry['grupo'] ? $entry['grupo']->descripcion : 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Para más detalles, revisa el archivo adjunto.</p>
    <p>Atentamente,</p>
    <p>El equipo de Ausencias</p>
</body>
</html>
