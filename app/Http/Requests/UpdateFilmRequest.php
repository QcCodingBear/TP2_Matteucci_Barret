<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFilmRequest extends FormRequest
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
        return [
                'title' => 'sometimes|required|string|max:255',
                'release_year' => 'sometimes|required|integer',
                'length' => 'sometimes|required|integer',
                'description' => 'sometimes|nullable|string',
                'rating' => 'sometimes|nullable|string|max:10',
                'language_id' => 'sometimes|required|integer|exists:languages,id',
                'special_features' => 'sometimes|nullable|string',
                'image' => 'sometimes|nullable|string|max:255',
        ];
    }
}
