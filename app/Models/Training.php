<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'validity_period',
        'project_id',
    ];

    protected function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
