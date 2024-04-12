<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrainingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'description' => ['nullable'],
            'validity_period' => ['nullable', 'integer'],
            'project_id' => ['nullable', 'exists:projects'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
