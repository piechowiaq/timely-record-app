<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleBasedRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user = auth()->user();

        if ($user->hasRole('project-admin') && $user->workspaces->isEmpty()) {
            return redirect(route('workspaces.create', ['project' => $user->project_id]));
        } elseif ($user->hasRole('user') && $user->workspaces()->count() === 1) {
            $workspaceId = $user->workspaces()->first()->id;

            return redirect(route('workspaces.dashboard', ['project' => $user->project_id, 'workspace' => $workspaceId]));
        }

        return $next($request);
    }
}
