<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the workspaces for the project.
     */
    public function workspaces(): HasMany
    {
        return $this->hasMany(Workspace::class);
    }

    /**
     * Get the custom registries for the project.
     */
    public function registries(): HasMany
    {
        //
        //        // Get the specific registries for this project via the pivot table
        //        $specificRegistriesQuery = $this->belongsToMany(Registry::class, 'project_registry');
        //
        //        // Get the generic registries (those not linked to any project)
        //        $genericRegistriesQuery = Registry::whereDoesntHave('projects');
        //
        //        // Combine the specific registries with the generic registries
        //        return $specificRegistriesQuery->union($genericRegistriesQuery);
        $specificRegistries = $this->hasMany(Registry::class);

        $genericRegistries = Registry::whereNull('project_id');

        return $specificRegistries->union($genericRegistries);
    }
}
