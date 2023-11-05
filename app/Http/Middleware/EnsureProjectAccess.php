<?php

namespace App\Http\Middleware;

use App\Models\Project;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProjectAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $project = $request->route('project');

        // If the route parameter is not already a Project instance, resolve it
        if (! $project instanceof Project) {
            $project = Project::find($project);
        }

        $userProjectId = auth()->user()->project?->id;

        if (! $project || $project->id !== $userProjectId) {
            abort(403, 'Unauthorized access!');
        }

        return $next($request);
    }
}
