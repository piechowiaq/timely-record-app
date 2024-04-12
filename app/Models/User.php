<?php

namespace App\Models;

use App\Models\Scopes\ProjectScope;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new ProjectScope);
    }

    /**
     * Get the project that the user belongs to.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * The workspaces that belong to the user.
     */
    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class);
    }

    public function scopeApplyFilters(Builder $query, Request $request): Builder
    {
        $search = $request->input('search');
        $sortField = $request->input('field');
        $sortDirection = $request->input('direction') ?? 'asc';
        $sortByProject = $request->input('projectId');

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

        if ($sortByProject) {
            $query->where('project_id', $sortByProject);
        }

        return $query;
    }

    /**
     * Scope a query to include users who are part of the given workspaces.
     */
    public function scopeInWorkspaces(Builder $query, array $workspaceIds): void
    {
        $query->whereHas('workspaces', function ($query) use ($workspaceIds) {
            $query->whereIn('workspaces.id', $workspaceIds);
        });
    }

    /**
     * Scope a query to apply role filters.
     */
    public function scopeWithRolesEligibleToView(Builder $query, string $userRole): void
    {
        $query->whereHas('roles', function ($query) use ($userRole) {
            if ($userRole === 'project-admin') {
                $query->whereNotIn('roles.name', ['project-admin', 'super-admin']);
            } else {
                $query->whereNotIn('roles.name', ['admin', 'project-admin', 'super-admin']);
            }
        });

    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }
}
