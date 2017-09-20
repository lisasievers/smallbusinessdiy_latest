<?php

namespace App\Http\Controllers;

use App\User;
use App\Site;
use App\Page;
use App\Frame;
use App\Setting;
use App\Commonsetting;
use File;
use Image;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Session;
use Mail;
use Cookie;
use Illuminate\Cookie\CookieJar;


class UserController extends Controller
{
	/**
	 * Show list of user
	 */
	public function getUserList()
	{
		$users = array();
		$tempUsers = User::orderBy('id', 'asc')->get();
		foreach ($tempUsers as $user)
		{
			$tempData = array();
			$tempData['userData'] = $user;
			$siteData = Site::where('user_id', $user->id)->where('site_trashed', 0)->orderBy('id', 'asc')->get()->toArray();
			//array holding all sites and associated data
			$allSites = array();
			// Get page data
			foreach ($siteData as $site)
			{
				$temp = array();
				$temp['siteData'] = $site;

				// Get the number of pages
				$pages = Page::where('site_id', $site['id'])->orderBy('id', 'asc')->get()->toArray();
				$temp['nrOfPages'] = count($pages);

				// Grab the last frame of site
				$indexPage = Page::where('name', 'index')->where('site_id', $site['id'])->orderBy('id', 'asc')->get()->toArray();
				if (count($indexPage) > 0)
				{
					//dd($indexPage);
					$frame = Frame::where('page_id', $indexPage[0]['id'])->where('revision', 0)->orderBy('id', 'asc')->first();
					if ( ! empty($frame))
					{
						$temp['lastFrame'] = $frame->toArray();
					}
					else
					{
						$temp['lastFrame'] = '';
					}
				}
				else
				{
					$temp['lastFrame'] = '';
				}
				$allSites[] = $temp;
			}
			$tempData['site'] = $allSites;
			$users[] = $tempData;
		}

		//dd($users);
		//dd($siteData);
		return view('users.users', ['users' => $users]);
	}

	/**
	 * Create New User
	 * @param  Request $request
	 * @return JSON
	 */
	public function postUserCreate(Request $request)
	{
		// $this->validate($request, [
		// 	'email' => 'required|email|unique:users',
		// 	'first_name' => 'required|max:120',
		// 	'last_name' => 'required|max:120',
		// 	'password' => 'required|confirmed|min:6',
		// 	]);
		$email = $request['email'];
		$first_name = $request['first_name'];
		$last_name = $request['last_name'];
		$password = md5($request['password']);
		if (isset($request['type']))
		{
			$type = $request['type'];
		}
		else
		{
			$type = 'user';
		}

		$user = new User();
		$user->email = $email;
		$user->first_name = $first_name;
		$user->last_name = $last_name;
		$user->active = 1;
		$user->password = $password;
		$user->type = $type;
		$user->save();

		$users = User::orderBy('id', 'asc')->get();
		$temp_user = View('partials.users', array('users' => $users));
		$return['users'] = $temp_user->render();
		$temp['header'] = 'Hooray';
		$temp['content'] = 'The new account was created successfully!';
		$view = View('partials.success', array('data' => $temp));
		$return['responseHTML'] = $view->render();
		$return['responseCode'] = 1;

		return response()->json($return);
	}

	/**
	 * Update Existing User
	 * @param  Request $request
	 * @return JSON
	 */
	public function postUserUpdate(Request $request)
	{
		// $this->validate($request, [
		// 	'email' => 'required|email|unique:users',
		// 	'first_name' => 'required|max:120',
		// 	'last_name' => 'required|max:120',
		// 	'password' => 'required|confirmed|min:6',
		// 	]);
		$user_id = $request['user_id'];
		$email = $request['email'];
		$password = md5($request['password']);
		if (isset($request['type']))
		{
			$type = $request['type'];
		}
		else
		{
			$type = 'user';
		}

		$user = User::where('id', $user_id)->first();
		$user->email = $email;
		$user->password = $password;
		$user->type = $type;
		$user->update();

		$update_user = User::where('id', $user_id)->first();
		$temp_user = View('partials.userdetails', array('user' => $update_user));
		$return['userDetailForm'] = $temp_user->render();
		$temp['header'] = 'Hooray';
		$temp['content'] = 'The account was updated successfully!';
		$view = View('partials.success', array('data' => $temp) );
		$return['responseHTML'] = $view->render();
		$return['responseCode'] = 1;

		return response()->json($return);
	}

	/**
	 * Send Password Reset Email
	 * @param  Integer $user_id
	 * @return JSON
	 */
	public function getPasswordResetEmail($user_id)
	{
		$user = User::where('id', $user_id)->first();
		$random = str_random(40);
		$user->forgotten_password_code = $random;
		$user->update();
		$data['email'] = $user->email;
		$data['code'] = $random;

		Mail::send('auth.email.forgot_password', $data, function ($message) use ($user) {
			$message->from('owlclick@gmail.com', 'SMBDIY');
			$message->to($user->email);
			$message->subject('Forgot Password');
		});

		if (count(Mail::failures()) > 0)
		{
			$temp['header'] = 'Ouch!';
			$temp['content'] = 'Something went wrong when trying to sent the password reset email.';
			$view = View('partials.error', array('data' => $temp) );
			$return['responseHTML'] = $view->render();
			$return['responseCode'] = 0;
		}
		else
		{
			$temp['header'] = 'Hooray!';
			$temp['content'] = 'A reset password email was sent to ' . $user->email;
			$view = View('partials.success', array('data' => $temp) );
			$return['responseHTML'] = $view->render();
			$return['responseCode'] = 1;
		}

		return response()->json($return);
	}

	/**
	 * Reset password
	 * @param  string $code
	 */
	public function getPasswordResetCode($code)
	{
		$user = User::where('forgotten_password_code', $code)->first();
		//dd($user);
		if ($user->count() > 0)
		{
			return view('auth.reset_password', ['user' => $user]);
		}
		else
		{
			abort(404);
		}
	}

	/**
	 * Update New Password
	 * @param  Request $request
	 */
	public function postPasswordReset(Request $request)
	{
		$user = User::where('id', $request->user_id)->first();
		$user->password = md5($request['new']);
		//$user->update();

		if ($user->update())
		{
			Session::flash('success', 'Password reset successfully. You can login now.');
		}
		else
		{
			Session::flash('error', 'Something went wrong when trying to reset your password. Please try some time later.');
		}

		return redirect()->route('home');
	}

	/**
	 * Send Password Reset Email from forget password option
	 * @param  Request $request
	 * @return JSON
	 */
	public function postRecoverPassword(Request $request)
	{
		//dd($request);
		$user = User::where('email', $request->email)->first();
		//dd($user);
		if( empty($user) ){  return redirect()->route('forgot.password'); }
		$random = str_random(40);
		$user->forgotten_password_code = $random;
		$user->update();
		$data['email'] = $user->email;
		$data['code'] = $random;

		Mail::send('auth.email.forgot_password', $data, function ($message) use ($user) {
			$message->from('owlclick@gmail.com', 'SMBDIY');
			$message->to($user->email);
			$message->subject('Forgot Password');
		});

		if (count(Mail::failures()) > 0)
		{
			Session::flash('error', 'Something went wrong when trying to sent the password reset email.');
		}
		else
		{
			Session::flash('success', 'A reset password email was sent to ' . $user->email);
		}

		return redirect()->route('forgot.password');
	}

	/**
	 * Delete User
	 * @param  Integer $user_id
	 */
	public function getUserDelete($user_id)
	{
		$user = User::where('id', $user_id)->first();
		if (Auth::user()->type == 'admin')
		{
			$user->delete();
		}

		return redirect()->route('users');
	}

	/**
	 * Enable or Disable User
	 * @param  Integer $user_id
	 */
	public function getUserEnableDisable($user_id)
	{
		$user = User::where('id', $user_id)->first();
		if (Auth::user()->type == 'admin')
		{
			if ($user->active == 1)
			{
				$user->active = 0;
				$user->update();
				Session::flash('success', 'The user account has been de-activated; this user can no longer login.');
			}
			else
			{
				$user->active = 1;
				$user->update();
				Session::flash('success', 'The user account has been activated; this user can now login and create web sites.');
			}
		}
		else
		{
			Session::flash('success', 'You need to be admin to do this.');
		}

		return redirect()->route('users');
	}

	/**
	 * Update account first name and last name
	 * @param  Request $request
	 * @return JSON
	 */
	public function postUAccount(Request $request)
	{
		if (Auth::user()->id != $request->input('userID'))
		{
			die('You must be the account owner to do this');
		}

		$validator = Validator::make($request->all(), [
			'userID' => 'required',
			'firstname' => 'required|max:255',
			'lastname' => 'required|max:255',
			]);
		if ($validator->fails())
		{
			$temp = array();
			$temp['header'] = "Ouch! Something went wrong:";
			$temp['content'] = "Something went wrong when trying to update your details.";

			$return = array();
			$return['responseCode'] = 0;
			$view = View('partials.error', array('data' => $temp));
			$return['responseHTML'] = $view->render();

			die(json_encode($return));
		}

		$user = User::where('id', $request->input('userID'))->first();
		$user->first_name = trim($request->input('firstname'));
		$user->last_name = trim($request->input('lastname'));
		$user->update();

		$temp = array();
		$temp['header'] = "Hooray!";
		$temp['content'] = "Your account details were updated successfully.";

		$return = array();
		$return['responseCode'] = 1;
		$view = View('partials.success', array('data' => $temp));
		$return['responseHTML'] = $view->render();

		die(json_encode($return));
	}

	/**
	 * Update account's login credentials
	 * @param  Request $request
	 * @return JSON
	 */
	public function postULogin(Request $request)
	{
		if (Auth::user()->id != $request->input('userID'))
		{
			die('You must be the account owner to do this');
		}

		$validator = Validator::make($request->all(), [
			'userID' => 'required',
			'email' => 'required|email',
			'password' => 'required',
			]);
		if ($validator->fails())
		{
			$temp = array();
			$temp['header'] = "Ouch! Something went wrong:";
			$temp['content'] = "Something went wrong when trying to update your details.";

			$return = array();
			$return['responseCode'] = 0;
			$view = View('partials.error', array('data' => $temp));
			$return['responseHTML'] = $view->render();

			die(json_encode($return));
		}

		$user = User::where('id', $request->input('userID'))->first();
		$user->email = trim($request->input('email'));
		$user->password = md5(trim($request->input('password')));
		$user->update();

		$temp = array();
		$temp['header'] = "Hooray!";
		$temp['content'] = "Your account details were updated successfully.";

		$return = array();
		$return['responseCode'] = 1;
		$view = View('partials.success', array('data' => $temp));
		$return['responseHTML'] = $view->render();

		die(json_encode($return));
	}
	public function getSignup(Request $request )
	{
		if(request()->cookie('emailid')){
		return redirect()->route('userdashboard');
		}
		else{ 
		$res = Commonsetting::select('value')->where('name', 'need_web_cost_month')->first();
		$data['cost_month']=$res->value;
		$res = Commonsetting::select('value')->where('name', 'need_web_cost_year')->first();
		$data['cost_year']=$res->value;
		$res = Commonsetting::select('value')->where('name', 'need_setup_costs')->first();
		$data['setup_cost']=$res->value;	
		//$data['ip'] = $request->ip();
		return view('auth.register_user',$data);
		}
	}
	/**
	 * User Sign-up process
	 * @param  Request $request
	 */
	public function postSignUp(CookieJar $cookieJar,Request $request)
	{
		//dd('postsignup');
		$isvalid=$this->validate($request, [
			'email' => 'required|email|unique:users',
			'first_name' => 'required|max:120',
			'last_name' => 'required|max:120',
			'password' => 'required|min:6',
			/*'password_confirmation' => 'required' 
			'city' => 'required|max:120',
			'state' => 'required|max:120'*/
			]);
	//print_r($isvalid); echo "red"; exit;
		//$code = $request->input('CaptchaCode');
		//$isHuman = captcha_validate($code);

		/* if ($isHuman)
		{ */
			$email = $request['email'];
			$first_name = $request['first_name'];
			$last_name = $request['last_name'];
			$password = md5($request['password']);

			$user = new User();
			$user->email = $email;
			$user->password = $password;
			$user->name = $first_name.' '.$last_name;
			$user->first_name = $first_name;
			$user->last_name = $last_name;
			$user->type = 'user';
			$user->user_type = 'Admin';
			//$user->city = $request['city'];
			//$user->state = $request['state'];
			$user->remember_token = $request->get ( '_token' );
			$user->user_from = $request['userfrom'];
			$user->active = 1;
			$user->status = 1;
			$user->deleted = 0;
			$user->email_notification = 'on';
			$user->expired_date = '2017-12-19 10:10:10';
			$user->package_id = 1;
			$user->profile_icon = 'dude.png';
			//print_r($user); exit;
			$user->save();
			$user_id=$user->id;

/* cooke storage */
  $cookieJar->queue(cookie('emailid', $email, 120));
  $cookieJar->queue(cookie('user_id', $user_id, 120));
  $cookieJar->queue(cookie('name', $first_name.' '.$last_name, 1440));
  $cookieJar->queue(cookie('profile', 'dude.png', 1440));
 //Cookie::make('rts', $email, 360,$path = null, $domain = 'http://glimpsesvr.com');
  
  			$mdata = array('from'=>'owlclick@gmail.com','aname'=>'SMBDIY Team','name'=>$first_name.' '.$last_name, 'email'=>$email);
    
			Mail::send( 'auth.email.signup_user', $mdata, function( $message ) use ($mdata)
            {
                $message->to( $mdata['email'] )->from( $mdata['from'], $mdata['aname'] )->subject( 'Sitebuilder Signup Notification ' );
            });
			/*
            Mail::send( 'auth.email.signup_admin', $mdata, function( $message ) use ($mdata)
            {
                $message->to( 'info@smallbusinessdiy.com')->from( $mdata['from'], $mdata['aname'] )->subject( 'SMBDIY Signup' );
            }); */
     // echo "HTML Email Sent. Check your inbox.";

			Session::flash('message', 'Register successfully.');
			//return redirect()->route('home');
			if (Auth::attempt(['email' => $email, 'password' => $request['password'], 'active' => 1]))
			{
				$user = User::where('id', $user_id)->first();
				if($user->user_domain_info=='1'){
				return redirect()->route('userdashboard');
				}
				else
				{
				return redirect()->route('userwebsite.domain');
				//return redirect('userwebsite?step=domain');
				}
			}

		/*}
		else
		{
			Session::flash('message', 'Captcha value mismatched.');
			return redirect()->route('signup.link');
		} */
	}

	/**
	 * User sign-in process
	 * @param  Request $request
	 */
	public function postSignIn(CookieJar $cookieJar,Request $request)
	{
		$this->validate($request, [
			'email' => 'required',
			'password' => 'required'
			]);

		if (Auth::attempt(['email' => $request['email'], 'password' => $request['password'], 'active' => 1]))
		{
			//return redirect()->route('dashboard');
			/* cooke storage */
  			 $cookieJar->queue(cookie('emailid', $request['email'], 1440));
  			 $cookieJar->queue(cookie('user_id', Auth::user()->id, 1440));
  			 $cookieJar->queue(cookie('name', Auth::user()->name, 1440));
  			 $cookieJar->queue(cookie('profile', Auth::user()->profile_icon, 1440));

  			// $cookieJar->queue(cookie('logged_in', 1, 0));
  			 //$cookieJar->queue(cookie('user_type', 'admin', 0));
  			 //Session::set('email', $request['email']);
  			 //Session::set('logged_in', 1);
  			 //Session::set('user_type', 'admin');
  			// Cookie::make('rts', $request['email'], 360,$path = null, $domain = 'http://glimpsesvr.com');
			return redirect()->route('userdashboard');
		}
		return redirect()->back()->with('message', 'Email address and Password mismatch or account not yet activated.');
	}

	/**
	 * User Logout process
	 */
	public function getLogout(Request $request)
	{
		$request->session()->flush();
		Auth::logout();
		return redirect()->route('home')->withCookie(Cookie::forget('user_id'))->withCookie(Cookie::forget('emailid'))->withCookie(Cookie::forget('name'))->withCookie(Cookie::forget('profile'))->withCookie(Cookie::forget('laravel_session'))->withCookie(Cookie::forget('ci_session'));
	}
    public function getUserSettings(Request $request )
	{
		$user_id=Auth::user()->id;
		$data['user'] = User::where('id', $user_id)->first();
		$data['page'] = 'User Settings';
		return view('userboard.user_settings',$data);
		
	}
	public function postUserSettings(Request $request )
	{
			$change='';
			$user_id=Auth::user()->id;
		
			$data = $request->input();
			$updata=array('name' =>$data['first_name'].' '.$data['last_name'],'first_name' =>$data['first_name'],'last_name' =>$data['last_name'],'email_notification' =>$data['email_notification']);
			User::where('id', $user_id)->update($updata);
			$change=1;

			if($data['password'] !='' && $data['password'] !=NULL){
			$upPass=array( 'password' =>md5(trim($data['password'])) );
			User::where('id', $user_id)->update($upPass);
			$change=2;
			}

		if ($request->hasFile('profile_icon'))
		{
			//User upload directory
			//$userID = Auth::user()->id;
			$images_uploadDir = Commonsetting::where('name', 'user_profile_uploadDir')->first();
			$userFolder = $images_uploadDir->value;

			//Check if the file extension is valid
			$allowedExt = Commonsetting::where('name', 'docs_allowedExtensions')->first();
			$temp = explode('|', $allowedExt->value);
			$file = $request->file('profile_icon');
			$ext = File::extension($file->getClientOriginalName());

			if (in_array($ext, $temp))
			{
				if ($file->move($userFolder, $file->getClientOriginalName()))
				{
				/*	$image = Image::make($userFolder.$file->getClientOriginalName());
                	$image->resize(90,90);
                	$image->save($userFolder.'/1.jpg');

				*/	//Update document name in DB
					$updata1=array('profile_icon' =>$file->getClientOriginalName());
					User::where('id', $user_id)->update($updata1);
					$change=3;
					$request->session()->flash('success', 'Successfully image uploaded!');
				}
				else
				{
					$request->session()->flash('error', 'There was an error in upload image!');
				}
			}
			else
			{
				$request->session()->flash('error', 'File extension is not a valid one!');
			}
		}
		
		//return redirect()->route('userdashboard');
		return redirect()->back()->with('message', 'Account Profile Updated.');
	
		
	}
}