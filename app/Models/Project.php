<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get the users for the project.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the workspaces for the project.
     */
    public function workspaces()
    {
        return $this->hasMany(Workspace::class);
    }
}
