<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AccessControl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $projectId = $request->route('project')->id ?? null;

        $userProjectId = Auth::user()->project->id ?? null;

        if ($projectId != $userProjectId) {
            // If the project ID does not match the user's project ID, return a 403 Forbidden response
            abort(403, 'hello');
        }

        return $next($request);
    }
}
