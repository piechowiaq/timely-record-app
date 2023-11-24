<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $project = Project::findOrFail($this->route('project'));

        dd($project);

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'role' => ['required', 'exists:roles,name'],
            'workspacesIds' => [
                'required',
                'array',
                Rule::exists('workspaces', 'id')->where(function ($query) use ($project) {
                    $query->where('project_id', $project->id);
                }),
            ],
            'workspacesIds.*' => ['required', 'exists:workspaces,id'],
        ];

    }
}
