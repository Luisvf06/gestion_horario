<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    #sometimes lo que hace es validarlo sólo en el caso de que se envíe ese dato. De esta forma se puede autenticar con el email o con el username.
    public function rules(): array
{
    return [
        'user_name' => 'required|string',
        'email' => 'sometimes|email',

    ];
}
}
