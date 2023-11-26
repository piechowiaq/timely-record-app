<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;

class UserRegistrationNotificationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(User $user): \Illuminate\Http\RedirectResponse
    {
        $user->notify(new \App\Notifications\SendUserRegistrationNotification());

        return Redirect::route('users.edit', ['project' => $user->project, 'user' => $user])->with('success', 'User registration email sent.');
    }
}
