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
        $projectId = (int) $request->route('project');

        $userProjectId = auth()->user()->project?->id;

        if ($projectId !== $userProjectId) {
            return redirect()->route('login')->with('error', 'Unauthorized access!');
        }

        return $next($request);
    }
}
