<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Critic;
use Illuminate\Auth\Access\AuthorizationException;

class StoreCriticRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
            $userId = auth()->user()->id;

            $existingCritic = Critic::where('user_id', $userId)
                ->where('film_id', $this->input('film_id'))
                ->exists();

            if ($existingCritic) {
                throw new AuthorizationException('User has already submitted a critic for this film.');
            }
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
            'film_id' => 'required|integer|exists:films,id',
            'score' => 'required|numeric|min:0|max:10',
            'comment' => 'required|string|max:1000',
        ];
    }
}
