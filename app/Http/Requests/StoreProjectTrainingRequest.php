<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectTrainingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255'],
            'description' => ['nullable'],
            'validity_period' => ['required', 'integer'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
