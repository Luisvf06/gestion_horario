<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GrupoController extends Controller
{
    public function insertGrupos(array $grupos)
    {
        Log::info('Datos de grupos recibidos:', ['grupos' => $grupos]);

        foreach ($grupos as $grupo) {
            try {
                Grupo::create([
                    'grupo_cod' => $grupo[0],
                    'descripcion' => $grupo[1]
                ]);
            } catch (\Exception $e) {
                Log::error('Error al crear grupo: ' . $e->getMessage(), ['grupo' => $grupo]);
                continue;
            }
        }
    }
    public function index()
    {
        return response()->json(Grupo::all());
    }
}
