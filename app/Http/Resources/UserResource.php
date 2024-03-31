<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'role' => $this->roles->first()?->name,
            'email' => $this->email,
            'email_verified' => $this->hasVerifiedEmail(),
        ];

        if (Auth::user()->isSuperAdmin()) {
            $data['workspacesIds'] = $this->workspaces->pluck('id')->toArray();
        } else {
            $data['workspacesIds'] = array_intersect(
                $this->workspaces->pluck('id')->toArray(),
                auth()->user()->workspaces->pluck('id')->toArray()
            );
        }

        return $data;
    }
}
