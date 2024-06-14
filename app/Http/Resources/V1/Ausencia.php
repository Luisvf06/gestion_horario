<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Ausencia extends JsonResource
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
            'fecha' => $this->fecha,
            'hora_inicio' => $this->hora_inicio,
            'hora_fin' => $this->hora_fin,
            'profesor' => $this->profesor,
            'asignatura' => $this->asignatura,
            'aula' => $this->aula,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
