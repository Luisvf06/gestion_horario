<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AusenciaRequest;
use App\Models\Ausencia;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\AusenciaMail;
use App\Models\User;
use App\Models\Horario;
use App\Models\Grupo;
use App\Models\Aula;
use PDF;

class AusenciaController extends Controller
{
    public function index(): JsonResponse
    {
        $ausencias = Ausencia::all(['id', 'fecha', 'hora', 'user_id']);
        return response()->json($ausencias, 200);
    }

    public function store(AusenciaRequest $request): JsonResponse
    {
        try {
            Log::info('Datos validados recibidos en store:', $request->all());
            
            $data = $request->validated();
            Log::info('Datos después de la validación:', $data);
Log::info('Datos validados recibidos en store:', $request->all());
            if (isset($data['fechas'])) {
                foreach ($data['fechas'] as $fecha) {
                    Log::info('Procesando fecha:', ['fecha' => $fecha]);
                    $formattedDate = Carbon::createFromFormat('m/d/Y', $fecha)->format('Y-m-d');
                    Log::info('Fecha formateada:', ['fecha' => $formattedDate]);

                    Ausencia::create([
                        'user_id' => $data['user_id'],
                        'fecha' => $formattedDate,
                        'hora' => $data['hora'] ?? null,
                    ]);

                    Log::info('Ausencia creada:', [
                        'user_id' => $data['user_id'],
                        'fecha' => $formattedDate,
                        'hora' => $data['hora'] ?? null,
                    ]);
                }
            } else {
                Log::info('Procesando única fecha:', ['fecha' => $data['fecha']]);
                $formattedDate = Carbon::createFromFormat('m/d/Y', $data['fecha'])->format('Y-m-d');
                Log::info('Fecha formateada:', ['fecha' => $formattedDate]);

                Ausencia::create([
                    'user_id' => $data['user_id'],
                    'fecha' => $formattedDate,
                    'hora' => $data['hora'] ?? null,
                ]);

                Log::info('Ausencia creada:', [
                    'user_id' => $data['user_id'],
                    'fecha' => $formattedDate,
                    'hora' => $data['hora'] ?? null,
                ]);
            }

            return response()->json(['message' => 'Ausencia creada correctamente'], 201);
        } catch (Exception $e) {
            Log::error('Error al crear la ausencia: '.$e->getMessage(), [
                'exception' => $e,
                'data' => $request->all(),
            ]);
            return response()->json(['error' => 'Error al crear la ausencia: ' . $e->getMessage()], 500);
        }
    }

    public function show($id): JsonResponse
    {
        $ausencia = Ausencia::find($id);

        if (!$ausencia) {
            return response()->json(['error' => 'Ausencia no encontrada'], 404);
        }

        return response()->json($ausencia, 200);
    }

    public function update(Request $request, $id): JsonResponse
    {
        Log::info('Iniciando actualización de ausencia con ID: ' . $id);
        $ausencia = Ausencia::find($id);

        if (!$ausencia) {
            Log::error('Ausencia no encontrada con ID: ' . $id);
            return response()->json(['error' => 'Ausencia no encontrada'], 404);
        }

        try {
            if ($request->has('fecha')) {
                // Convertir la fecha al formato correcto
                $fecha = Carbon::createFromFormat('m/d/Y', $request->fecha);
                if (!$fecha) {
                    Log::error('Formato de fecha inválido: ' . $request->fecha);
                    return response()->json(['error' => 'Formato de fecha inválido'], 400);
                }

                // Verificar que la fecha no sea anterior a la fecha actual
                $fechaActual = Carbon::now()->startOfDay();
                if ($fecha->lessThan($fechaActual)) {
                    Log::error('La fecha proporcionada es anterior a la fecha actual: ' . $request->fecha);
                    return response()->json(['error' => 'La fecha no puede ser anterior a la fecha actual'], 400);
                }

                $ausencia->fecha = $fecha->format('Y-m-d');
            }

            if ($request->has('hora')) {
                $ausencia->hora = $request->hora;
            }

            $ausencia->save();

            Log::info('Ausencia actualizada correctamente');
            return response()->json(['message' => 'Ausencia actualizada correctamente', 'data' => $ausencia], 200);
        } catch (Exception $e) {
            Log::error('Error al actualizar la ausencia: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar la ausencia: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        $ausencia = Ausencia::find($id);

        if (!$ausencia) {
            return response()->json(['error' => 'Ausencia no encontrada'], 404);
        }

        try {
            $ausencia->delete();
            return response()->json(['message' => 'Ausencia eliminada correctamente'], 200);
        } catch (Exception $e) {
            Log::error('Error al eliminar la ausencia: ' . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar la ausencia: ' . $e->getMessage()], 500);
        }
    }

    public function sendMail(Request $request): JsonResponse
    {
        auth()->user()->ausencias()->create($request->all());
        Mail::to('luis@test.mail')->send(new AusenciaMail(''));
        return response()->json(['message' => 'Mail sent successfully'], 200);
    }

    public function getUserAusencias(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['error' => 'Usuario no autenticado'], 401);
            }

            $ausencias = $user->ausencias()->get(['id', 'fecha', 'hora']);
            return response()->json($ausencias, 200);
        } catch (Exception $e) {
            Log::error('Error al obtener las ausencias: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener las ausencias: ' . $e->getMessage()], 500);
        }
    }

    public function getAusenciasHoy(): JsonResponse
    {
        try {
            $hoy = Carbon::now()->format('Y-m-d');
            Log::info('Fecha de hoy ajustada: ' . $hoy);

            $ausencias = Ausencia::whereDate('fecha', $hoy)
                                ->with(['user.horarios.aula:id,descripcion', 'user.horarios.grupo:id,descripcion'])
                                ->get();

            $ausenciasConDetalles = [];

            foreach ($ausencias as $ausencia) {
                $horarios = $ausencia->user->horarios->unique('aula_id', 'grupo_id'); // Evitar duplicaciones 
                foreach ($horarios as $horario) {
                    $ausenciasConDetalles[] = [
                        'id' => $ausencia->id,
                        'user_id' => $ausencia->user_id,
                        'user_name' => $ausencia->user->name,
                        'fecha' => $ausencia->fecha,
                        'hora' => $ausencia->hora,
                        'aula_descripcion' => $horario->aula->descripcion,
                        'grupo_descripcion' => $horario->grupo->descripcion,
                    ];
                }
            }

            Log::info('Ausencias con detalles: ' . json_encode($ausenciasConDetalles));

            return response()->json($ausenciasConDetalles, 200);
        } catch (Exception $e) {
            Log::error('Error al obtener las ausencias de hoy: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener las ausencias de hoy: ' . $e->getMessage()], 500);
        }
    }

    //Función para obtener las ausencias de un usuario elegido además de la clase y grupo 
    public function getAusenciasWithDetails($id): JsonResponse
    {
        try {
            Log::info('Función getAusenciasWithDetails llamada con ID: ' . $id);
            $user = User::find($id);
            if (!$user) {
                Log::error('Usuario no encontrado con ID: ' . $id);
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            // Log para verificar el usuario encontrado
            Log::info('Usuario encontrado: ' . $user->name);

            // Obtener las ausencias del usuario
            $ausencias = Ausencia::where('user_id', $user->id)->get();

            // Log para verificar las ausencias encontradas
            Log::info('Ausencias encontradas: ' . $ausencias->count());

            // Preparar las ausencias
            $ausenciasConDetalles = $ausencias->map(function($ausencia) use ($user) {
                // Determinar el día de la semana
                $diaSemana = Carbon::parse($ausencia->fecha)->dayOfWeek;

                //verificar el día de la semana
                Log::info('Fecha de ausencia: ' . $ausencia->fecha . ' - Día de la semana: ' . $diaSemana);

                // Buscar los horarios del usuario para el día
                $horarios = Horario::where('user_id', $user->id)
                                    ->where('dia', $diaSemana)
                                    ->with(['aula', 'grupo'])
                                    ->get();

                // Log para verificar los horarios encontrados
                Log::info('Horarios encontrados para el día de la semana ' . $diaSemana . ': ' . $horarios->count());

                //revisar esto
                return $horarios->map(function($horario) use ($ausencia, $user) {
                    return [
                        'id' => $ausencia->id,
                        'fecha' => $ausencia->fecha,
                        'hora' => $ausencia->hora,
                        'user_name' => $user->name,
                        'aula_descripcion' => $horario->aula->descripcion ?? 'No disponible',
                        'grupo_descripcion' => $horario->grupo->descripcion ?? 'No disponible',
                    ];
                });
            })->flatten();

            // ausencias encontrados
            Log::info('Detalles de ausencias encontrados: ' . $ausenciasConDetalles->count());

            return response()->json($ausenciasConDetalles, 200);
        } catch (Exception $e) {
            Log::error('Error al obtener las ausencias: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener las ausencias: ' . $e->getMessage()], 500);
        }
    }
    public function getAusenciasMesDia(Request $request, $mes, $dia): JsonResponse
    {
        try {
            Log::info('Iniciando obtención de ausencias para mes: ' . $mes . ', día: ' . $dia);
    
            // Obtener ausencias filtradas por mes y día, y cargar las relaciones necesarias
            $ausencias = Ausencia::with([
                    'user',
                    'user.horarios.aula', 
                    'user.horarios.grupo', 
                    'user.horarios.franja'
                ])
                ->whereMonth('fecha', $mes)
                ->whereDay('fecha', $dia)
                ->get();
    
            Log::info('Ausencias encontradas:', $ausencias->toArray());
    
            // Ordenar las ausencias por la franja horaria
            $ausencias = $ausencias->sortBy(function($ausencia) {
                $horario = $ausencia->user->horarios->first();
                return $horario && $horario->franja ? $horario->franja->id : null;
            });
    
            Log::info('Ausencias ordenadas:', $ausencias->toArray());
    
            // Verificar si el cliente solicita el PDF
            if ($request->has('pdf') && $request->get('pdf') == 'true') {
                $data = [
                    'title' => 'Lista de ausencias del día ' . $dia . '/' . $mes,
                    'date' => Carbon::createFromDate(null, $mes, $dia)->format('d-m-Y'),
                    'ausencias' => []
                ];
    
                foreach ($ausencias as $ausencia) {
                    $horario = $ausencia->user->horarios->first();
                    $data['ausencias'][] = [
                        'ausencia' => $ausencia,
                        'horario' => $horario,
                        'aula' => $horario ? $horario->aula : null,
                        'grupo' => $horario ? $horario->grupo : null,
                    ];
                }
    
                Log::info('Datos para PDF:', $data);
    
                $pdf = PDF::loadView('pdf.myPDF', $data);
                return $pdf->download('ausencias_' . $dia . '_' . $mes . '.pdf');
            }
    
            return response()->json([
                'ausencias' => $ausencias,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener las ausencias:', ['exception' => $e]);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
    
    
    public function getAusenciasWithDetailsById($id): JsonResponse
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            $ausencias = $user->ausencias()->get(['id', 'fecha', 'hora']);
            return response()->json($ausencias, 200);
        } catch (Exception $e) {
            Log::error('Error al obtener las ausencias: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener las ausencias: ' . $e->getMessage()], 500);
        }
    }

}
