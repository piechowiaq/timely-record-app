<?php

namespace App\Models;

use App\Models\Scopes\ProjectScope;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'project_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new ProjectScope);
    }

    /**
     * Get the project that the user belongs to.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * The workspaces that belong to the user.
     */
    public function workspaces()
    {
        return $this->belongsToMany(Workspace::class);
    }

    public function scopeApplyFilters(Builder $query, Request $request): Builder
    {
        $search = $request->input('search');
        $sortField = $request->input('field');
        $sortDirection = $request->input('direction') ?? 'asc';

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhereHas('roles', function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%");
                    });
            });
        }

        if ($sortField === 'role') {
            $query->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->orderBy('roles.name', $sortDirection)
                ->select('users.*');
        } elseif ($sortField) {
            $query->orderBy($sortField, $sortDirection);
        }

        return $query;
    }

    /**
     * Scope a query to only include users who share at least one workspace with the authenticated user.
     */
    public function scopeWithAuthUserWorkspaces(Builder $query): Builder
    {
        $workspaceIds = auth()->user()->workspaces()->pluck('workspaces.id');

        return $query->whereHas('workspaces', function ($query) use ($workspaceIds) {
            $query->whereIn('workspaces.id', $workspaceIds);
        });
    }
}
