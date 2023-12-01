<?php

namespace App\Providers;

use App\Repositories\Contracts\RegistryRepositoryInterface;
use App\Repositories\Eloquent\EloquentRegistryRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RegistryRepositoryInterface::class, EloquentRegistryRepository::class);

        // ... other repository bindings ...
    }
}
