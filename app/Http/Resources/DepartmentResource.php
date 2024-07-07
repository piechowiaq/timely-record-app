<?php

namespace App\Http\Resources;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

/** @mixin Department */
class DepartmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'project_id' => $this->project_id,
        ];

        // Add workspacesIds if the relationship is loaded
        $data['workspacesIds'] = $this->whenLoaded('workspaces', function () {
            return array_intersect(
                $this->project->workspaces->pluck('id')->toArray(),
                Auth::user()->workspaces->pluck('id')->toArray()
            );
        }, []);

        return $data;

    }
}
