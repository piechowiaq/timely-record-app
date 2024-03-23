<?php

namespace App\Services;

use App\Models\Registry;
use App\Repositories\Contracts\RegistryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use LaravelIdea\Helper\App\Models\_IH_Registry_C;

class RegistryService
{
    protected RegistryRepositoryInterface $registryRepository;

    public function __construct(RegistryRepositoryInterface $registryRepository)
    {
        $this->registryRepository = $registryRepository;
    }

    public function getRegistriesWithLatestReport(int $workspaceId): array|Collection|_IH_Registry_C
    {
        return Registry::with(['reports' => function ($query) use ($workspaceId) {
            $query->where('workspace_id', $workspaceId)
                ->latest('expiry_date');
        }])->whereHas('workspaces', function ($query) use ($workspaceId) {
            $query->where('workspace_id', $workspaceId);
        })->get();
    }

    public function getRegistriesWithLatestReportQuery(int $workspaceId): Registry|\Illuminate\Database\Eloquent\Builder|\LaravelIdea\Helper\App\Models\_IH_Registry_QB
    {
        return Registry::whereHas('workspaces', function ($query) use ($workspaceId) {
            $query->where('workspace_id', $workspaceId);
        })->with(['reports' => function ($query) use ($workspaceId) {
            $query->where('workspace_id', $workspaceId)
                ->latest('expiry_date');
        }]);
    }

    public function getExpiringRegistries(Collection $registries): Collection
    {
        return $registries->filter(function ($registry) {
            return ! is_null($registry->reports->first()) && $registry->reports->first()->expiry_date > now() && $registry->reports->first()->expiry_date <= now()->addMonth();
        });
    }

    public function getUpToDateRegistries(Collection $registries): Collection
    {
        return $registries->filter(function ($registry) {
            return ! is_null($registry->reports->first()) && $registry->reports->first()->expiry_date > now();
        });
    }

    public function getNonCompliantRegistries(Collection $registries): Collection|\Illuminate\Support\Collection
    {
        return $registries->filter(function ($registry) {
            return is_null($registry->reports->first()) || $registry->reports->first()->expiry_date < now();
        });
    }

    public function getRegistryMetrics(Collection $registries): float|int
    {
        return $registries->count() > 0 ? ($this->getUpToDateRegistries($registries)->count() / $registries->count()) * 100 : 0;
    }
}
