# Enviar correos automáticamente a las 8:00 am:
    1. apt-get update apt-get install cron
    2. service cron start 
    3. contrab -e (con este comando se abre el editor y se añade la siguiente línea al final del dicumento)
    4. * * * * * cd /TFG/gestion_horario && php artisan schedule:run >> /dev/null 2>&1
    5. Para comprobar el estaod de cron usar "service cron status"

    Los archivos que intervienen en el envío de correos de forma automática son:
    app/Console/Commands/EnviarAusenciasDiarias.php
    routes/console.php
    app/Console/Commands/kernel.php este archivo no debería existir en laravel 11 pero he tenido que crearlo porque si no, tras añadir contenido en el console y crear el /commands/EnviarAusenciaDiarias.php no dejaba iniciar el servidor
    php artisan EnviarAusenciasDiarias comando para enviar correo fuera de la hora 


