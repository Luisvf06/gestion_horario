<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Grupo extends JsonResource
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
            'curso' => $this->curso,
            'cuatrimestre' => $this->cuatrimestre,
            'profesor' => $this->profesor,
            'aula' => $this->aula,
            'horario' => $this->horario,
            'asignatura' => $this->asignatura,
            'periodo' => $this->periodo,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
