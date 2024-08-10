<?php

namespace App\Listeners;

use App\Models\UserLog;
use Carbon\Carbon;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;
use Jenssegers\Agent\Agent;
use Throwable;

class UserEventSubscriber
{
    /**
     * @var Agent
     */
    private Agent $agent;

    /**
     * @param Agent $agent
     */
    public function __construct(Agent $agent)
    {
        $this->agent = $agent;
    }

    /**
     * Handle user login events.
     */
    public function handleUserLogin($event)
    {
        try {
            $today = Carbon::now()->toDateString();
            $user = $event->user;
            UserLog::query()->updateOrCreate(
                [
                    'date' => $today,
                    'user_id' => $user->id,
                ],
                [
                    'date' => $today,
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'last_login' => now(),
                    'login_status' => 'Logged In',
                    'browser' => $this->agent->browser(),
                    'os' => $this->agent->platform(),
                ]
            );
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    /**
     * Handle user logout events.
     */
    public function handleUserLogout($event)
    {
        try {
            $today = Carbon::now()->toDateString();
            $user = $event->user;
            UserLog::query()->updateOrCreate(
                [
                    'date' => $today,
                    'user_id' => $user->id,
                ],
                [
                    'date' => $today,
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'last_logout' => now(),
                    'login_status' => 'Logged Out',
                    'browser' => $this->agent->browser(),
                    'os' => $this->agent->platform(),
                ]
            );
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events
     * @return void
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(
            Login::class,
            [UserEventSubscriber::class, 'handleUserLogin']
        );

        $events->listen(
            Logout::class,
            [UserEventSubscriber::class, 'handleUserLogout']
        );
    }
}
