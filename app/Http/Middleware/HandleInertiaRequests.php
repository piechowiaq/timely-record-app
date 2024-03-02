<?php

namespace App\Http\Middleware;

use App\Models\Project;
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

        if ($user && $user->project_id) {
            // Assuming you use route model binding or retrieve it somehow
            $project = Project::find($user->project_id);
            $canManageProject = $project ? $user->can('manage', $project) : false;
            $canViewProject = $project ? $user->can('view', $project) : false;
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user()?->load('workspaces'),
                'canManageProject' => $canManageProject,
                'canViewProject' => $canViewProject,
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
        ];
    }
}
