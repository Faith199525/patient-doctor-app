<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use DB;
use App\Models\User;
use App\Notifications\SubscriptionNotification;
use App\Notifications\KeyHealthIndicator;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
            $subs= tap(DB::table('subscriptions')->whereDate('end', now()->toDateString()))->update(['active' => false])->get();

            foreach($subs as $sub){
                $user= User::find($sub->user_id);
                $user->notify(new SubscriptionNotification($sub)); 
             }       
        })->daily(); //everyMinute() 

        $schedule->call(function () {
            $subscribers= DB::table('subscriptions')->where('active', true)->get();

            foreach($subscribers as $subscriber){
                $users= User::find($subscriber->user_id);
                $users->notify(new KeyHealthIndicator($subscriber)); 
             }
        })->quarterly();
        
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
