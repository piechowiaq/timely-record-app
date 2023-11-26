<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserRegistrationController extends Controller
{
    public function __invoke(Request $request): RedirectResponse|Response
    {
        $user = User::where('email', $request->email)->first();

        $user->markEmailAsVerified();

        return Inertia::render('Auth/RegisterUser', [
            'email' => $request->email,
            'token' => $request->route('token'),
            'name' => $user->name,
        ]);
    }
}
