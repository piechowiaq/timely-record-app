<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkspaceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'location' => $this->location,
            'registryMetrics' => $this->when(isset($this->registryMetrics), $this->registryMetrics),
            'registriesIds' => $this->whenLoaded('registries', function () {
                return $this->registries->pluck('id');
            }),

        ];
    }
}
