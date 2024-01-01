<?php

namespace App\Providers;

use App\Repositories\Contracts\RegistryRepositoryInterface;
use App\Repositories\Contracts\WorkspaceRepositoryInterface;
use App\Repositories\Eloquent\EloquentRegistryRepository;
use App\Repositories\Eloquent\EloquentWorkspaceRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RegistryRepositoryInterface::class, EloquentRegistryRepository::class);

        $this->app->bind(WorkspaceRepositoryInterface::class, EloquentWorkspaceRepository::class);
        // ... other repository bindings ...
    }
}
