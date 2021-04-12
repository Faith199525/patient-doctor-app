<?php

namespace App\Providers;

use App\Events\PasswordResetEvent;
use App\Listeners\SendPasswordResetEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Output\ConsoleOutput;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        PasswordResetEvent::class => [
            SendPasswordResetEmail::class
        ],
        'App\Events\PaymentMade' => [
            'App\Listeners\VerifyPayment',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        if (env('SHOW_SQL', false)) {
            DB::listen(function ($query) {
                $loggingConsole = new ConsoleOutput();
                $loggingConsole->writeln(sprintf("%s, %s, [%s]", $query->time, $query->sql, implode(",", $query->bindings)));
                info(sprintf("%s, %s, [%s]", $query->time, $query->sql, implode(",", $query->bindings)));
            });
        }
    }
}
