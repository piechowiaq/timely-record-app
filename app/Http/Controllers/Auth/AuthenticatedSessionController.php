<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user(); // Assuming $user is already defined as the authenticated user

        // Determine the redirection path based on the user's role and workspace status
        $redirectionPath = route('projects.dashboard', ['project' => $user->project_id]); // Default fallback route

        if ($user->hasRole('project-admin') && $user->workspaces->isEmpty()) {
            $redirectionPath = route('workspaces.create', ['project' => $user->project_id]);
        } elseif ($user->hasRole('user') && $user->workspaces()->count() === 1) {
            $workspaceId = $user->workspaces()->first()->id;
            $redirectionPath = route('workspaces.dashboard', ['project' => $user->project_id, 'workspace' => $workspaceId]);
        }

        // Execute the redirection, using intended() to respect any previously intended route, if applicable
        return redirect()->intended($redirectionPath);
    }

    /**
     * Destroy an authenticated session.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
