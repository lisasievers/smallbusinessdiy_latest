<?php

namespace App\Console;
use DB;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\SendEmails;
use App\Console\Commands\SendEmailsNuser;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Mail;
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
         Commands\Inspire::class,
         Commands\SendEmails::class,
         Commands\SendEmailsNuser::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
         $schedule->call(function () {
            DB::table('users')->delete(5);
        })->everyMinute();

      
         $schedule->command('sendmails:paynotdone')
         ->daily()
         ->sendOutputTo('auth.email.payment_mail_admin')
         ->emailOutputTo('thamaraiselvan@wemagination.net');  

          $schedule->command('nuseremails:send')
         ->daily()
         ->sendOutputTo('auth.email.payment_mail_admin')
         ->emailOutputTo('thamaraiselvan@wemagination.net');       
    }
}
