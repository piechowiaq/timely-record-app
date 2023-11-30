<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Registry extends Model
{
    use HasFactory;

    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class);
    }
}
