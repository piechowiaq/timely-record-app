<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Training */
class TrainingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'validity_period' => $this->validity_period,

            'project_id' => $this->project_id,

            'project' => new ProjectResource($this->whenLoaded('project')),
        ];
    }
}
