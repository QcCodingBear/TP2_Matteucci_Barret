<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFilmRequest extends FormRequest
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
                'title' => 'required|string|max:255',
                'release_year' => 'required|integer',
                'length' => 'required|integer',
                'description' => 'nullable|string',
                'rating' => 'nullable|string|max:10',
                'language_id' => 'required|integer|exists:languages,id',
                'special_features' => 'required|string',
                'image' => 'nullable|string|max:255',
        ];
    }
}
