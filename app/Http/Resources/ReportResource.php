<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'report_date' => $this->report_date,
            'expiry_date' => $this->expiry_date,
            'report_path' => $this->report_path,
            'created_by_user' => UserResource::make(User::find($this->created_by_user_id)),
            'updated_by_user' => UserResource::make(User::find($this->updated_by_user_id)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
