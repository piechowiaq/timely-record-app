<?php

namespace App\Listeners;

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
    public function handle(object $event): void
    {
        session()->put('project_id', $event->user->project_id);
    }
}
