<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\User;
use App\Models\Aula;
use App\Models\Franja;
use App\Models\Grupo;
use App\Models\Asignatura;
use App\Models\Periodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
class HorarioController extends Controller
{
    public function insertHorarios(array $horarios)
    {
        Log::info('Datos de horarios recibidos:', ['horarios' => $horarios]);

        foreach ($horarios as $horario) {
            try {
                $profesor = User::where('professor_cod', $horario[1])->firstOrFail();
            } catch (\Exception $e) {
                Log::error('Error al buscar profesor: ' . $e->getMessage() . ' - Código: ' . $horario[1]);
                continue;
            }

            try {
                if (!empty($horario[5])) {
                    $aula = Aula::where('aula_cod', $horario[5])->firstOrFail();
                } else {
                    $aula = null;
                }
            } catch (\Exception $e) {
                Log::error('Error al buscar aula: ' . $e->getMessage() . ' - Código: ' . $horario[5]);
                continue;
            }

            try {
                $franja = Franja::where('franja_cod', $horario[3])->firstOrFail();
            } catch (\Exception $e) {
                Log::error('Error al buscar franja: ' . $e->getMessage() . ' - Código: ' . $horario[3]);
                continue;
            }

            try {
                $grupo = Grupo::where('grupo_cod', $horario[6])->firstOrFail();
            } catch (\Exception $e) {
                Log::error('Error al buscar grupo: ' . $e->getMessage() . ' - Código: ' . $horario[6]);
                continue;
            }

            try {
                $asignatura = Asignatura::where('asignatura_cod', $horario[4])->firstOrFail();
            } catch (\Exception $e) {
                Log::error('Error al buscar asignatura: ' . $e->getMessage() . ' - Código: ' . $horario[4]);
                continue;
            }

            try {
                $periodo = Periodo::where('periodo_cod', $horario[7])->firstOrFail();
            } 
            catch (\Exception $e) {
                Log::error('Error al buscar periodo: ' . $e->getMessage() . ' - Código: ' . $horario[7]);
                continue;
            }

            try {
                $nuevoHorario = Horario::create([
                    'horario_cod' => $horario[0],
                    'dia' => $horario[2],
                    'user_id' => $profesor->id,
                    'asignatura_id' => $asignatura->id, 
                    'aula_id' => $aula ? $aula->id : null,
                    'franja_id' => $franja->id,
                    'grupo_id' => $grupo->id,
                    'periodo_id' => $periodo->id
                ]);

            } catch (\Exception $e) {
                Log::error('Error al crear horario: ' . $e->getMessage());
                continue;
            }
        }
    }
    public function index()
    {
        return response()->json(Horario::all());
    }
    public function getUserHorario()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $horarios = Horario::with(['asignatura', 'franja', 'user', 'aula', 'grupo'])
            ->where('user_id', $user->id)
            ->orderBy('franja_id')
            ->get();

        // Obtener franjas filtradas por user_id
        $franjas = Franja::whereHas('horarios', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        // Obtener asignaturas filtradas por user_id
        $asignaturas = Asignatura::whereHas('horarios', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        // Obtener aulas filtradas por user_id
        $aulas = Aula::whereHas('horarios', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        return response()->json([
            'user' => $user,
            'horarios' => $horarios,
            'franjas' => $franjas,
            'asignaturas' => $asignaturas,
            'aulas' => $aulas,
        ]);
    }

    public function getUserHorarioById($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $horarios = Horario::with(['asignatura', 'franja', 'user', 'aula', 'grupo'])
            ->where('user_id', $user->id)
            ->orderBy('franja_id')
            ->get();

        // Obtener franjas filtradas por user_id
        $franjas = Franja::whereHas('horarios', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        // Obtener asignaturas filtradas por user_id
        $asignaturas = Asignatura::whereHas('horarios', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        // Obtener aulas filtradas por user_id
        $aulas = Aula::whereHas('horarios', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        return response()->json([
            'user' => $user,
            'horarios' => $horarios,
            'franjas' => $franjas,
            'asignaturas' => $asignaturas,
            'aulas' => $aulas,
        ]);
    }

    public function getUserHorarioDetailsById($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            $horarios = Horario::with(['asignatura', 'franja', 'user', 'aula', 'grupo'])
                ->where('user_id', $user->id)
                ->orderBy('franja_id')
                ->get();

            $franjas = Franja::whereHas('horarios', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();

            return response()->json([
                'user' => $user,
                'horarios' => $horarios,
                'franjas' => $franjas,
            ]);
        } catch (Exception $e) {
            Log::error('Error al obtener el horario: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener el horario: ' . $e->getMessage()], 500);
        }
    }

    public function getGuardias()
    {
        // Obtener las asignaturas que comienzan con 'guard'
        $guardiasAsignaturas = Asignatura::where('descripcion', 'like', 'Guard%')->get();
    
        // Obtener los horarios relacionados con las asignaturas de guardias para todos los usuarios
        $horarios = Horario::with(['asignatura', 'franja', 'user', 'aula', 'grupo'])
            ->whereIn('asignatura_id', $guardiasAsignaturas->pluck('id'))
            ->orderBy('franja_id')
            ->get();
    
        // Obtener franjas para formar el horario
        $franjas = Franja::whereHas('horarios', function($query) use ($guardiasAsignaturas) {
            $query->whereIn('asignatura_id', $guardiasAsignaturas->pluck('id'));
        })->get();
    
        // Obtener aulas para saber el módulo
        $aulas = Aula::whereHas('horarios', function($query) use ($guardiasAsignaturas) {
            $query->whereIn('asignatura_id', $guardiasAsignaturas->pluck('id'));
        })->get();
    
        return response()->json([
            'horarios' => $horarios,
            'franjas' => $franjas,
            'aulas' => $aulas,
            'asignaturas' => $guardiasAsignaturas,
        ]);
    }
    
}
