<?php

namespace App\Http\Controllers;

use App\Models\Periodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PeriodoController extends Controller
{
    public function insertPeriodos(array $periodos)
    {
        Log::info('Datos de periodos recibidos:', ['periodos' => $periodos]);

        foreach ($periodos as $periodo) {
            try {
                Periodo::create([
                    'periodo_cod' => $periodo[0],
                    'descripcion' => $periodo[1],
                    'fecha_desde' => $periodo[2],
                    'fecha_hasta' => $periodo[3]
                ]);
            } catch (\Exception $e) {
                Log::error('Error al crear periodo: ' . $e->getMessage(), ['periodo' => $periodo]);
                continue;
            }
        }
    }
}

