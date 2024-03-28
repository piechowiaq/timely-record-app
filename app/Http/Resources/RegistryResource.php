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

        return [
            'id' => $this->id,
            'name' => $this->name,
            'validity_period' => $this->validity_period,
            'project_id' => $this->project_id,
            'description' => $this->description,
            'workspacesIds' => $this->whenLoaded('workspaces', function () {
                return $this->workspaces->pluck('id')->toArray();
            }),
            'expiry_date' => $this->whenLoaded('reports', function () {
                return $this->reports->first()?->expiry_date;
            }),

        ];
    }
}
