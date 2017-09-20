<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SocialAccountService;
use Socialite;
use App\User;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Session;
use Mail;
use Cookie;
use Illuminate\Cookie\CookieJar;
use Exception;
 

class SocialAuthController extends Controller
{

    protected $redirectTo = '/';

    public function __construct()
    {
       // $this->middleware('guest', ['except' => 'logout']);
    }
	use AuthenticatesAndRegistersUsers, ThrottlesLogins;
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
			'name' => $data['name'],
            'first_name' => $data['name'],
            'email' => $data['email'],
            'type' => 'user',
            'password' => md5('password'),
            'user_type' => 'Admin',
            'active' => 1,
            'status' => 1,
            'deleted' => 0,
            'email_notification' => 'on',
			'expired_date' => '2017-12-19 10:10:10',
			'package_id' => 1,
            'profile_icon' => 'dude.png'
        ]);
    }

    public function redirectToFacebook()
    {
    	//echo "R"; exit;
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback(CookieJar $cookieJar)
    {
    	//echo 'sfun'; exit;
        try {
            $user = Socialite::driver('facebook')->user();
            $create['name'] = $user->name;
            $create['first_name'] = $user->name;
            $create['email'] = $user->email;
            $create['password'] = md5('password');
            $create['type'] = 'user';
            $create['user_type'] = 'Admin';
            $create['provider'] = 'facebook';
            $create['provider_id'] = $user->id;
            $create['remember_token'] = Session::token();
            $create['active'] = 1;
            $create['status'] = 1;
            $create['deleted'] = 0;
			$create['expired_date'] = '2017-12-19 10:10:10';
			$create['package_id'] = 1;
			
            $create['email_notification'] = 'on';
            $create['profile_icon'] = 'dude.png';
            //print_r($user['email']); exit;
             $userif = User::where('email', '=', $user['email'])->first();;
             //print_r($userif); exit;
             if ($userif === null)
            {
            $userModel = new User;
            

            $createdUser = $userModel->addNew($create);
            Auth::loginUsingId($createdUser->id);
            //echo $createdUser; exit;
           // return redirect()->route('userwebsite');
           // echo $createdUser->id. 'binside'; exit;
            if (Auth::attempt(['email' => $user->email, 'password' => 'password', 'active' => 1]))
			{
				$userin = User::where('id', $createdUser->id)->first();

				Session::set('name', $userin->first_name);
            	Session::set('email', $userin->email);
            	Session::set('type', 'user');
            	Session::set('active', '1');
            	$cookieJar->queue(cookie('emailid', $user->email, 1440));
                $cookieJar->queue(cookie('user_id', $createdUser->id, 1440));
                $cookieJar->queue(cookie('name', $userin->first_name, 1440));
                $cookieJar->queue(cookie('profile', 'dude.png', 1440));

				return redirect()->route('userwebsite');
				
			}
            }
            else
            {
            $userModel = new User;
            
            $createdUser = $userModel->addNew($create);
            Auth::loginUsingId($createdUser->id);
            //echo $createdUser; exit;
           // return redirect()->route('userwebsite');
           // echo $createdUser->id. 'binside'; exit;
            if (Auth::attempt(['email' => $user->email, 'password' => 'password', 'active' => 1]))
            {
                $userin = User::where('id', $createdUser->id)->first();

                Session::set('name', $userin->first_name);
                Session::set('email', $userin->email);
                Session::set('type', 'user');
                Session::set('active', '1');
                $cookieJar->queue(cookie('emailid', $user->email, 45000));
                $cookieJar->queue(cookie('user_id', $createdUser->id, 1440));
                $cookieJar->queue(cookie('name', $userin->first_name, 1440));
                $cookieJar->queue(cookie('profile', 'dude.png', 1440));
                return redirect()->route('userdashboard');
                
            } 
            }
        } catch (Exception $e) {
            return redirect('/facebook');
        }
    }


}