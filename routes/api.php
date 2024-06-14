<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AusenciaController;
use App\Http\Controllers\Api\V1\LoginController;
use Illuminate\Http\Request;
use App\Http\Controllers\XmlController;
use App\Http\Controllers\AsignaturaController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\AulaController;
use App\Http\Controllers\FranjaController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\PeriodoController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\RoleController;
use App\Mail\UserMail;
use App\Http\Controllers\PasswordController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user/horario', [HorarioController::class, 'getUserHorario']);
    Route::resource('/users', UserController::class);
    Route::resource('/ausencias', AusenciaController::class);
    Route::resource('/asignaturas', AsignaturaController::class);
    Route::post('/upload-xml', [XmlController::class, 'uploadXML']);
    Route::resource('/horarios', HorarioController::class);
    Route::get('/ausencias', [AusenciaController::class, 'getUserAusencias']);
    Route::get('users/{id}/ausencias', [AusenciaController::class, 'getUserAusencias']);
    Route::get('/ausenciasHoy', [AusenciaController::class, 'getAusenciasHoy']);
    Route::get('/getAusenciasUsuario/{id}', [AusenciaController::class, 'getAusenciasUsuario']);
    Route::get('/users/{id}/ausencias-with-details', [AusenciaController::class, 'getAusenciasWithDetails']);
    Route::resource('/aulas', AulaController::class);
    Route::resource('/franjas', FranjaController::class);
    Route::resource('/grupos', GrupoController::class);
    Route::resource('/periodos', PeriodoController::class);
    Route::resource('/getUserHorario', HorarioController::class);
    Route::get('/horario/user/{id}', [HorarioController::class, 'getUserHorario']);//ruta para ver el horario de un usuario determinado y no solo el propio
    Route::get('/ausencias/mes/{mes}/dia/{dia}', [AusenciaController::class, 'getAusenciasMesDia']);
    Route::get('/generate-pdf', [PDFController::class, 'generatePDF']);
    Route::resource('/roles',RoleController::class);
    Route::get('/horario/user/{id}/detalle', [HorarioController::class, 'getUserHorarioById']);
    //Estas dos son para ver el horario y ausencias de los usuarios buscados por el admin o jefe de estudios
    Route::get('/users/{id}/ausencias-with-details', [AusenciaController::class, 'getAusenciasWithDetailsById']);
    Route::get('/horario/user-details/{id}', [HorarioController::class, 'getUserHorarioDetailsById']);
    Route::get('/user/roles', [UserController::class, 'getUserRoles']);
    //Ruta para ver guardias
    Route::get('/horario/guardias', [HorarioController::class, 'getGuardias']);

 
    
});

Route::get('/send-test-email', function () {
    $user = \App\Models\User::first();
    Mail::to($user->email)->send(new UserMail([
        'name' => $user->name,
        'email' => $user->email,
        'user_name' => $user->user_name,
        'password' => $user->password
    ]));
    return 'Email sent!';
});

// Ruta OPTIONS para manejar las solicitudes preflight
Route::options('/{any}', function (Request $request) {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, DELETE, PUT')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
})->where('any', '.*');

//Rutas para la recuperacion de contraseña. No funciona
Route::post('/forgot-password', [PasswordController::class, 'forgotPassword'])->middleware('guest')->name('password.email');
Route::post('/reset-password', [PasswordController::class, 'resetPassword'])->middleware('guest')->name('password.update');