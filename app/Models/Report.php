<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function registry(): BelongsTo
    {
        return $this->belongsTo(Registry::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function scopeValid($query)
    {
        return $query->where('expiry_date', '>', now());
    }

    public function scopeValidWorkspaceRegistry($query, $workspaceId = null, $registryId = null)
    {
        // Always apply the validity filter based on expiry_date
        $query->where('expiry_date', '>', now());

        // Conditionally add workspace and registry filters if IDs are provided
        if (! is_null($workspaceId)) {
            $query->where('workspace_id', $workspaceId);
        }

        if (! is_null($registryId)) {
            $query->where('registry_id', $registryId);
        }

        return $query;
    }
}
