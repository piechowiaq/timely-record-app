<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PositionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'departmentId' => 'required|exists:departments,id',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
