<!DOCTYPE html>
<html>
<head>
    <title>Bienvenid@ a la aplicación de gestión de horarios</title>
</head>
<body>
    <h1>Bienvenid@ {{ $data['name'] }}</h1>
    <p>Tu usuario ha sido creado exitosamente.</p>
    <p>Tu nombre de usuario es: {{ $data['user_name'] }}</p>
    <p>Tu contraseña de usuario es: {{ $data['password'] }}</p>
    <p>Tu correo electrónico es: {{ $data['email'] }}</p>
    <p>Saludos,</p>
    <p>El equipo de gestión de horarios</p>
</body>
</html>
