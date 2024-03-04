<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
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
            'report_date' => ['required', 'date', 'before_or_equal:today'],
            'workspace_id' => ['required', 'exists:workspaces,id'],
            'registry_id' => ['required', 'exists:registries,id'],
            'report_path' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpeg,jpg', 'max:2048'],
        ];
    }
}
