<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AulaController extends Controller
{
    public function insertAulas(array $aulas)
    {
        Log::info('Datos de aulas recibidos:', ['aulas' => $aulas]);

        foreach ($aulas as $aula) {
            try {
                Aula::create([
                    'aula_cod' => $aula[0],
                    'descripcion' => $aula[1]
                ]);
            } catch (\Exception $e) {
                Log::error('Error al crear aula: ' . $e->getMessage(), ['aula' => $aula]);
                continue;
            }
        }
    }
    public function index()
    {
        return response()->json(Aula::all());
    }
}

