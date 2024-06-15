<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bienvenid@ a la aplicación de gestión de horarios</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background-color: #4CAF50;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        p {
            line-height: 1.6;
            margin-bottom: 10px;
        }
        .example {
            background-color: #f0f0f0;
            padding: 10px;
            border-left: 4px solid #4CAF50;
            margin: 20px 0;
        }
        .footer {
            background-color: #f0f0f0;
            padding: 10px;
            text-align: center;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Bienvenid@ {{ $data['name'] }}</h1>
        </div>
        <div class="content">
            <p>Su usuario ha sido creado exitosamente.</p>
            <p>Su nombre de usuario es: <strong>{{ $data['user_name'] }}</strong></p>
            <p>Su contraseña de usuario es: <strong>{{ $data['password'] }}</strong></p>
            <p>Su correo electrónico es: <strong>{{ $data['email'] }}</strong></p>
            <p>Su contraseña es: su primer nombre + 2024 Puede cambiarla en cualquier momento en la sección "Mi cuenta"</p>
            <div class="example">
                <p><strong>Ej:</strong></p>
                <p>María del Carmen García García -> María2024</p>
            </div>
            <p>Saludos,</p>
            <p>El equipo de gestión de horarios</p>
        </div>
        <div class="footer">
            <p>&copy; 2024 Aplicación de Gestión de Horarios. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
