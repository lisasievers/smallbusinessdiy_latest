<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Site;
use User;
use DB;
use Mail;
use Illuminate\Support\Facades\Auth;
use Log;
class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendmails:paynotdone';
   // protected $signature = 'sendmails:yettried';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mailing the Payment pending users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $today=date('Y-m-d');
        $users = DB::table('sites')
            ->join('users', 'users.id', '=', 'sites.user_id')
            ->join('payments', 'payments.id', '=', 'sites.payid')
            ->whereNull('sites.payid')
            ->where('users.id', '!=', [1])
            ->select('users.*', 'sites.site_name','sites.payid', 'sites.updated_at')
            ->get();
         dd($users);   
        foreach($users as $usr){
            //Finding difference day from site create date upto today
            $tmp = explode(' ', $usr->updated_at);
            $datediff = strtotime($today) - strtotime($tmp[0]);
            $daydiff=floor($datediff / (60 * 60 * 24));
            //Finding difference day from last send mail upto today
            if($usr->email_notify_date!=''){$umday=$usr->email_notify_date;}else{$umday=$today;}
            $emdiff = strtotime($today) - strtotime($umday);
            $mdiff=floor($emdiff / (60 * 60 * 24));
            //dd($mdiff);
            if($usr->email_notification=='on' && $mdiff > 7){
            
            $mdata = array('from'=>'acethamarai@gmail.com','aname'=>'SMBDIY Team','name'=>$usr->first_name.' '.$usr->last_name, 'email'=>$usr->email,'site'=>$usr->site_name);
            if($daydiff <= 2 ){
                Mail::send( 'auth.email.signup_user', $mdata, function( $message ) use ($mdata)
                {
                    $message->to( $mdata['email'] )->from( $mdata['from'], $mdata['aname'] )->subject( 'Payment pending diff 2+ days ');
                });
            }
            elseif($daydiff <= 7 ){
                Mail::send( 'auth.email.signup_user', $mdata, function( $message ) use ($mdata)
                {
                    $message->to( $mdata['email'] )->from( $mdata['from'], $mdata['aname'] )->subject( 'Payment pending diff 7+ days ');
                });
            }
             elseif($daydiff <= 15 ){
                Mail::send( 'auth.email.signup_user', $mdata, function( $message ) use ($mdata)
                {
                    $message->to( $mdata['email'] )->from( $mdata['from'], $mdata['aname'] )->subject( 'Payment pending diff 15+ days ');
                });
            }
             elseif($daydiff <= 30 ){
                Mail::send( 'auth.email.signup_user', $mdata, function( $message ) use ($mdata)
                {
                    $message->to( $mdata['email'] )->from( $mdata['from'], $mdata['aname'] )->subject( 'Payment pending diff 30+ days');
                });
            }
            else{
                Mail::send( 'auth.email.signup_user', $mdata, function( $message ) use ($mdata)
                {
                    $message->to( $mdata['email'] )->from( $mdata['from'], $mdata['aname'] )->subject( 'Payment pending diff 60+ days ');
                });
            }
            } //condition end of user-email notify status and last send email difference eligible
        }
    }
}
