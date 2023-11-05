<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProjectAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Retrieve the project ID from the route parameter
        $projectId = $request->route('project');

        // Check if the authenticated user has access to the project
        if (! auth()->check() || ! auth()->user()->hasProjectAccess($projectId)) {
            // If the user is not authenticated or does not have access, return a 403 Forbidden response or redirect
            return response('Forbidden', 403);
        }

        // If the user has access, proceed with the request
        return $next($request);
    }
}
