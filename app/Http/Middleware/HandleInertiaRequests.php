<?php

namespace App\Http\Middleware;

use App\Models\Project;
use App\Models\Report;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {

        $user = auth()->user();

        $canManageProject = false;
        $canViewProject = false;
        $canCreateReport = false;

        if (session()->has('project_id')) {

            $project = Project::find(session('project_id'));
            $canManageProject = $project ? $user->can('manage', $project) : false;
            $canViewProject = $project ? $user->can('view', $project) : false;
            $canCreateReport = $project ? $user->can('create', Report::class) : false;
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user()?->load('workspaces'),
            ],
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'error' => $request->session()->get('error'),
                ];
            },
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'route' => $request->route()->uri,
            'permissions' => [
                'canManageProject' => $canManageProject,
                'canViewProject' => $canViewProject,
                'canCreateReport' => $canCreateReport,
            ],
            'projectId' => session('project_id'),

        ];
    }
}
