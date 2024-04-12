<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class SetProjectIdInSession
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        session()->put('project_id', $event->user->project_id);
    }
}
