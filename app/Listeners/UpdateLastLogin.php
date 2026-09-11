<?php

namespace App\Listeners;

use App\Repositories\UserRepo;
use Illuminate\Auth\Events\Login;

class UpdateLastLogin
{
    protected $user;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(UserRepo $user)
    {
        $this->user = $user;
    }

    /**
     * Handle the event.
     *
     * @param Login $event
     * @return void
     */
    public function handle(Login $event)
    {
        $this->user->update($event->user->id, ['last_login' => now()]);
    }
}