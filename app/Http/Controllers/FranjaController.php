<?php

namespace App\Http\Controllers;

use App\Models\Franja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FranjaController extends Controller
{
    public function insertFranjas(array $franjas)
    {
        Log::info('Datos de franjas recibidos:', ['franjas' => $franjas]);

        foreach ($franjas as $franja) {
            try {
                Franja::create([
                    'franja_cod' => $franja[0],
                    'descripcion' => $franja[1],
                    'hora_desde' => $franja[2],
                    'hora_hasta' => $franja[3]
                ]);
            } catch (\Exception $e) {
                Log::error('Error al crear franja: ' . $e->getMessage(), ['franja' => $franja]);
                continue;
            }
        }
    }

    public function index()
    {
        return response()->json(Franja::all());
    }
    public function getFranjaByHora($hora)
    {
        $franja = Franja::where('hora_desde', '<=', $hora)->where('hora_hasta', '>=', $hora)->first();
        return $franja;
    }
}

