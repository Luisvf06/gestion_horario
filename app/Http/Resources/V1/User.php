<?php
namespace App\Http\Resources\v1;
use Illuminate\Http\Resources\Json\JsonResource;
class User extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'rol' => $this->rol,
            'user_name' => $this->user_name,
            'professor_code' => $this->professor_code,
            'id' => $this->id,
        ];
    }
}