<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Aula extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'codigo' => $this->codigo,
            'capacidad' => $this->capacidad,
            'tipo' => $this->tipo,
            'descripcion' => $this->descripcion,
            'edificio' => $this->edificio,
            'planta' => $this->planta,
            'centro' => $this->centro,
            'departamento' => $this->departamento,
            'responsable' => $this->responsable,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'observaciones' => $this->observaciones,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,];
    }
}
