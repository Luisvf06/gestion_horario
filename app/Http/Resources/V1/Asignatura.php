<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Asignatura extends JsonResource
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
            'grupo' => $this->grupo,
            'periodo' => $this->periodo,];
    }
}
