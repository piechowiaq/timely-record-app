<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartmentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'workspacesIds' => ['sometimes', 'array'],
            'workspacesIds.*' => ['sometimes',
                Rule::exists('workspaces', 'id')->where(function ($query) {
                    $query->where('project_id', $this->session()->get('project_id'));
                })],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
