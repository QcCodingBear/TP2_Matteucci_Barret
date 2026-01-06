<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterAuthRequest extends FormRequest
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
    public function rules(): array
    {
        // Meme si pas demandé dans l'enoncé, il est préférable de rendre l'email unique comme pour le login.
        return [
            'login' => 'required|string|unique:users,login',
            'password' => 'required|string|min:8',
            'email' => 'required|string|email|unique:users,email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
        ];
    }
}
