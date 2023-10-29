<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'location',
        'project_id'
    ];

    /**
     * Get the project that owns the workspace.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * The users that belong to the workspace.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

}
