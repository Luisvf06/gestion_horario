<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Horario extends JsonResource
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
            'dia' => $this->dia,
            'franja' => $this->franja,
            'asinatura' => $this->asignatura,
            'aula' => $this->aula,
            'grupo' => $this->grupo,
            'periodo' => $this->periodo,
            'user' => $this->user,
            'hora_inicio' => $this->hora_inicio,
            'hora_fin' => $this->hora_fin,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,];
    }
}
