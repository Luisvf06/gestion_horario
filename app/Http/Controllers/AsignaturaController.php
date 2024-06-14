<?php

namespace App\Http\Controllers;

use App\Models\Asignatura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AsignaturaController extends Controller
{
    public function insertAsignaturas(array $asignaturas)
    {
        Log::info('Datos de asignaturas recibidos:', ['asignaturas' => $asignaturas]);

        foreach ($asignaturas as $asignatura) {
            try {
                Asignatura::create([
                    'asignatura_cod' => $asignatura[0],
                    'descripcion' => $asignatura[1]
                ]);
            } catch (\Exception $e) {
                Log::error('Error al crear asignatura: ' . $e->getMessage(), ['asignatura' => $asignatura]);
                continue;
            }
        }
    }
    public function index()
    {
        return response()->json(Asignatura::all());
    }
}
