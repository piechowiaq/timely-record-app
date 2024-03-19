<?php

namespace App\Http\Resources;

use App\Models\Registry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Registry */
class RegistryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $this->resource->loadMissing('workspaces');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'validity_period' => $this->validity_period,
            'project_id' => $this->project_id,
            'description' => $this->description,
            'workspacesIds' => WorkspaceResource::collection($this->workspaces)->pluck('id')->toArray(),
        ];
    }
}
