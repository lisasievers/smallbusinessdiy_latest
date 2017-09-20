<?php

namespace App\Http\Controllers;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
//use Validator;
use URL;
use Session;
use Redirect;
use Input;
use App\User;
use App\Site;
use App\Payment;
use App\Commonsetting;
use App\Website;
use App\Siteform;
use App\Subscription;
use App\Subscribeact;
use Mail;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Stripe\Error\Card;


class AddMoneyController extends Controller
{
    
    public function __construct()
    {
        parent::__construct();
        $this->user = new User;
    }
    
    /**
     * Show the application paywith stripe.
     *
     * @return \Illuminate\Http\Response
     */
    public function payWithStripe($site_id)
    {
        $data=array();
        $data['site_id']=$site_id;
        $site = Site::where('id', $site_id)->get();
        $data['site_name']= $site[0]->site_name;
        //$data['email']='';
        $ncost = Commonsetting::where('name', 'need_web_cost_month')->first();
        $data['ncost']=$ncost->value;
        return view('userboard.stripe',$data);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postPaymentWithStripe(Request $request)
    {
        //echo 'ret'; exit;
        $validator = Validator::make($request->all(), [
            'card_no' => 'required',
            'ccExpiryMonth' => 'required',
            'ccExpiryYear' => 'required',
            'cvvNumber' => 'required|max:3',
            'amount' => 'required',
        ]);
        
        $input = $request->all();
        if ($validator->passes()) {           
            $input = array_except($input,array('_token'));            
            $stripe = Stripe::make('sk_test_MiPA3zcCi2dxbnu07yY01HaH');
            
            try {
                $token = $stripe->tokens()->create([
                    'card' => [
                        'number'    => $request->get('card_no'),
                        'exp_month' => $request->get('ccExpiryMonth'),
                        'exp_year'  => $request->get('ccExpiryYear'),
                        'cvc'       => $request->get('cvvNumber'),
                    ],
                ]);
                if (!isset($token['id'])) {
                    \Session::put('error','The Stripe Token was not generated correctly');
                    return redirect()->route('addmoney.paywithstripe', ['site_id' => $request->get('site')]);
                }
                
              
              $charge = $stripe->charges()->create([
                    'card' => $token['id'],
                    'currency' => 'USD',
                    'amount'   => $request->get('amount'),
                    'description' => $request->get('site').'/'.$request->get('sitename').'/'.$request->get('email')
                ]); 
            
                //$charge['status']='succeeded';
                if($charge['status'] == 'succeeded') {
                    //Payment sucess query write in DB
                   $pay = new Payment();
                    $pay->stripeToken = $charge['id'];
                    $pay->amount = $request->get('amount');
                    $pay->date_time = date('Y-m-d H:i:s');
                    $pay->user_id = Auth::user()->id;
                    $pay->site_id = $request->get('site');
                    $pay->status = 1;
                    $pay->save();
                    $updata=array('payid'=>$pay->id);
                    Site::where('id', $request->get('site'))->update($updata);
                    //Email Notification
                    $email=$request->session()->get('email');

                    $name=$request->session()->get('name');
                    $mdata = array('from'=>'acethamarai@gmail.com','aname'=>'Administrator','name'=>$name, 'email'=>$email,'site'=>$request->get('sitename'), 'amount'=>$request->get('amount'),'payid'=>$charge['id'],'date'=>date('Y-m-d H:i:s'));
                      
                    Mail::send( 'auth.email.payment_mail_user', $mdata, function( $message ) use ($mdata)
                    {
                        $message->to( $mdata['email'] )->from( $mdata['from'], $mdata['aname'] )->subject( 'Payment Acknowledgement: Sitebuilder - '.$mdata['payid'] );
                    });
                    Mail::send( 'auth.email.payment_mail_admin', $mdata, function( $message ) use ($mdata)
                    {
                        $message->to( 'thamaraiselvan@wemagination.net' )->from( $mdata['from'], $mdata['aname'] )->subject( 'New Payment: Sitebuilder - '.$mdata['payid'] );
                    }); 
                      
                    /**
                    * Write Here Your Database insert logic.
                    */
                    \Session::put('success','Payment successful for this site');
                    //return redirect()->route('addmoney.paywithstripe', ['site_id' => $request->get('site')]);
                    return redirect()->route('site', ['site_id' => $request->get('site')]);
                } else {
                    \Session::put('error','Money not add in wallet!!');
                    return redirect()->route('addmoney.paywithstripe', ['site_id' => $request->get('site')]);
                }

            } catch (Exception $e) {
                \Session::put('error',$e->getMessage());
                return redirect()->route('addmoney.paywithstripe', ['site_id' => $request->get('site')]);
            } catch(\Cartalyst\Stripe\Exception\CardErrorException $e) {
                \Session::put('error',$e->getMessage());
                return redirect()->route('addmoney.paywithstripe', ['site_id' => $request->get('site')]);
            } catch(\Cartalyst\Stripe\Exception\MissingParameterException $e) {
                \Session::put('error',$e->getMessage());
                return redirect()->route('addmoney.paywithstripe', ['site_id' => $request->get('site')]);
            }
        }
        \Session::put('error','All fields are required!!');
        return redirect()->route('addmoney.paywithstripe', ['site_id' => $request->get('site')]);
    }    
    //Do IT for me section
     public function payWithDoitforme($site_id)
    {
        $data=array();
        $data['site_id']=$site_id;
        $site = Siteform::where('id', $site_id)->get();
        $data['site_name']= $site[0]->site_name;
        //$data['email']='';
        $bcost = Commonsetting::where('name', 'need_setup_costs')->first();
        $data['bcost']=$bcost->value;
        $ncost = Commonsetting::where('name', 'doit_init_cost')->first();
        $data['ncost']=$ncost->value + $data['bcost'];
        return view('userboard.stripe_doitforme',$data);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postPaymentWithDoitforme(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_name' => 'required',
            'card_no' => 'required',
            'ccExpiryMonth' => 'required',
            'ccExpiryYear' => 'required',
            'cvvNumber' => 'required',
            'amount' => 'required',
        ]);
        
        $input = $request->all();
        if ($validator->passes()) {           
            $input = array_except($input,array('_token'));            
            $stripe = Stripe::make('sk_test_MiPA3zcCi2dxbnu07yY01HaH');
            
            try {
                $token = $stripe->tokens()->create([
                    'card' => [
                        'number'    => $request->get('card_no'),
                        'exp_month' => $request->get('ccExpiryMonth'),
                        'exp_year'  => $request->get('ccExpiryYear'),
                        'cvc'       => $request->get('cvvNumber'),
                    ],
                ]);
                if (!isset($token['id'])) {
                    \Session::put('error','The Stripe Token was not generated correctly');
                    return redirect()->route('paymentodo', ['site_id' => $request->get('site')]);
                }
                
              
            /*  $charge = $stripe->charges()->create([
                    'card' => $token['id'],
                    'currency' => 'USD',
                    'amount'   => $request->get('amount'),
                    'description' => $request->get('site').'/'.$request->get('sitename').'/'.$request->get('email')
                ]); 
            */
                $charge['status']='succeeded';
                $charge['id']='stripeid_5345435';
                if($charge['status'] == 'succeeded') {
                    //Payment sucess query write in DB
                    $pay = new Payment();
                    $pay->stripeToken = $charge['id'];
                    $pay->amount = $request->get('amount');
                    $pay->date_time = date('Y-m-d H:i:s');
                    $pay->user_id = Auth::user()->id;
                    $pay->site_id = '';
                    $pay->status = 1;
                    $pay->save();

                    $updata=array('payid'=>$pay->id);
                    Siteform::where('id', $request->get('site'))->update($updata);
                    //Email Notification
                    $email=$request->session()->get('email');

                    $name=$request->session()->get('name');
                    $mdata = array('from'=>'acethamarai@gmail.com','aname'=>'Administrator','name'=>$name, 'email'=>$email,'site'=>$request->get('site_name'), 'amount'=>$request->get('amount'),'payid'=>$charge['id'],'date'=>date('Y-m-d H:i:s'));
                      
                    Mail::send( 'auth.email.payment_mail_user', $mdata, function( $message ) use ($mdata)
                    {
                        $message->to( $mdata['email'] )->from( $mdata['from'], $mdata['aname'] )->subject( 'Payment Acknowledgement: Sitebuilder - '.$mdata['payid'] );
                    });
                    Mail::send( 'auth.email.payment_mail_admin', $mdata, function( $message ) use ($mdata)
                    {
                        $message->to( 'thamaraiselvan@wemagination.net' )->from( $mdata['from'], $mdata['aname'] )->subject( 'New Payment: Sitebuilder - '.$mdata['payid'] );
                    }); 
                      
                    /**
                    * Write Here Your Database insert logic.
                    */
                    \Session::put('success','Payment successful.');
                    //return redirect()->route('addmoney.paywithstripe', ['site_id' => $request->get('site')]);
                   return redirect()->route('success_site')->with('state', 'payment_success');
                    //return redirect()->route('userdashboard')->with('state', 'payment_success');
                } else {
                    \Session::put('error','Money not add in wallet!!');
                    return redirect()->route('paymentodo', ['site_id' => $request->get('site')]);
                }

            } catch (Exception $e) {
                \Session::put('error',$e->getMessage());
                return redirect()->route('paymentodo', ['site_id' => $request->get('site')]);
            } catch(\Cartalyst\Stripe\Exception\CardErrorException $e) {
                \Session::put('error',$e->getMessage());
                return redirect()->route('paymentodo', ['site_id' => $request->get('site')]);
            } catch(\Cartalyst\Stripe\Exception\MissingParameterException $e) {
                \Session::put('error',$e->getMessage());
                return redirect()->route('paymentodo', ['site_id' => $request->get('site')]);
            }
        }
        \Session::put('error','All fields are required!!');
        return redirect()->route('paymentodo', ['site_id' => $request->get('site')]);
    }  
    //Reports section
   public function getUserAddwebsite(Request $request,$sub_id)
    {

    $data=array();
    //$cstep=$request->get('step');
    //dd($cstep);
    $data['builder'] = false;
    $data['page'] = 'Website Addition';
    $data['packages'] = Subscription::where('id', $sub_id)->first();
    $bcost = Commonsetting::where('name', 'need_setup_costs')->first();
    $data['bcost']=$bcost->value;
    $ncost = Commonsetting::where('name', 'doit_init_cost')->first();
    $data['ncost']=$ncost->value + $data['bcost'];
    $data['sess'] = $request->session()->all();
    
    return view('usereport.add_report_sites',$data);
    }  

public function postUserAddwebsiteSubmit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subscribe_id' => 'required',
            'card_no' => 'required',
            'ccExpiryMonth' => 'required',
            'ccExpiryYear' => 'required',
            'cvvNumber' => 'required',
            'amount' => 'required',
        ]);
        
        $input = $request->all();
        if ($validator->passes()) {           
            $input = array_except($input,array('_token'));            
            $stripe = Stripe::make('sk_test_MiPA3zcCi2dxbnu07yY01HaH');
            
            try {
                $token = $stripe->tokens()->create([
                    'card' => [
                        'number'    => $request->get('card_no'),
                        'exp_month' => $request->get('ccExpiryMonth'),
                        'exp_year'  => $request->get('ccExpiryYear'),
                        'cvc'       => $request->get('cvvNumber'),
                    ],
                ]);
                if (!isset($token['id'])) {
                    \Session::put('error','The Stripe Token was not generated correctly');
                    return redirect()->route('user-reports-addition', ['sub_id' => $request->get('subscribe_id')]);
                }
                
              
             /* $charge = $stripe->charges()->create([
                    'card' => $token['id'],
                    'currency' => 'USD',
                    'amount'   => $request->get('amount'),
                    'description' => $request->get('site').'/'.$request->get('sitename').'/'.$request->get('email')
                ]); 
            */
                $charge['status']='succeeded';
                $charge['id']='stripeid_5345435';
                if($charge['status'] == 'succeeded') {
                     //Payment sucess query write in DB
                  /*  $pay = new Payment();
                    $pay->stripeToken = $charge['id'];
                    $pay->amount = $request->get('amount');
                    $pay->date_time = date('Y-m-d H:i:s');
                    $pay->user_id = $request->get('email');
                    $pay->site_id = $request->get('site');
                    $pay->status = 1;
                    $pay->save();
                    */
                    $issuedate = date('Y-m-d');
                    $periodtype=$request->get('subscribe_id');
                    //dd( $periodtype);
                        if($periodtype=='1'){ 
                        $t=3;
                        $maturityday =date('Y-m-d', strtotime($issuedate. '+'.$t. 'months'));
                       // $tmp = explode('-', $maturityday);
                       // $maturityday = $tmp[2] . '-' . $tmp[1] . '-' . $tmp[0];
                    }elseif($periodtype=='2'){ 
                        $t=6; 
                        $maturityday =date('Y-m-d', strtotime($issuedate.'+'.$t. 'months'));
                        //$tmp = explode('-', $maturityday);
                       // $maturityday = $tmp[2] . '-' . $tmp[1] . '-' . $tmp[0];
                    }else{
                        $t=12;
                        $maturityday =date('Y-m-d', strtotime($issuedate.'+'.$t. 'months'));
                        //$tmp = explode('-', $maturityday);
                        //$maturityday = $tmp[2] . '-' . $tmp[1] . '-' . $tmp[0];
                    }
                    $exists=Subscribeact::where('user_id', Auth::user()->id)->get()->toArray();
                    //dd($exists);

                    if(count($exists > 0 ) && !empty($exists) ){
                     //dd('in');   
                    $updata=array('sub_id'=>$request->get('subscribe_id'),'pay_id'=>5,'exdate_time'=>$maturityday);
                    Subscribeact::where('user_id', Auth::user()->id)->update($updata);
					//For sitespy tool
					$updata2=array('package_id'=>1,'expired_date'=>$maturityday.' 10:10:10');
                    User::where('id', Auth::user()->id)->update($updata2);
					
                    }
                    else
                    {
                    $ws = new Subscribeact();
                    $ws->sub_id = $request->get('subscribe_id');
                    $ws->user_id = Auth::user()->id;
                    $ws->pay_id = '5';
                    $ws->exdate_time = $maturityday;
                    $ws->save();
					//For sitespy tool
                    $updata2=array('package_id'=>1,'expired_date'=>$maturityday.' 10:10:10');
                    User::where('id', Auth::user()->id)->update($updata2);
                    }
                    //Email Notification
                    $email=$request->session()->get('email');

                    $name=$request->session()->get('name');
                  /*  $mdata = array('from'=>'owlclick@gmail.com','aname'=>'Administrator','name'=>$name, 'email'=>$email,'site'=>$request->get('site_name'), 'amount'=>$request->get('amount'),'payid'=>$charge['id'],'date'=>date('Y-m-d H:i:s'));
                      
                    Mail::send( 'auth.email.payment_mail_user', $mdata, function( $message ) use ($mdata)
                    {
                        $message->to( $mdata['email'] )->from( $mdata['from'], $mdata['aname'] )->subject( 'Payment Acknowledgement: Sitebuilder - '.$mdata['payid'] );
                    });
                    Mail::send( 'auth.email.payment_mail_admin', $mdata, function( $message ) use ($mdata)
                    {
                        $message->to( 'thamaraiselvan@wemagination.net' )->from( $mdata['from'], $mdata['aname'] )->subject( 'New Payment: Sitebuilder - '.$mdata['payid'] );
                    }); 
                      */
                    /**
                    * Write Here Your Database insert logic.
                    */
                    \Session::put('success','Payment successful.');
                    //return redirect()->route('addmoney.paywithstripe', ['site_id' => $request->get('site')]);
                    //return redirect()->route('/site/'.$request->get('site'));
                    return redirect()->route('user.reportshome')->with('state', 'payment_success');
                } else {
                    \Session::put('error','Money not add in wallet!!');
                    return redirect()->route('user-reports-addition', ['sub_id' => $request->get('subscribe_id')]);
                }

            } catch (Exception $e) {
                \Session::put('error',$e->getMessage());
                return redirect()->route('user-reports-addition', ['sub_id' => $request->get('subscribe_id')]);
            } catch(\Cartalyst\Stripe\Exception\CardErrorException $e) {
                \Session::put('error',$e->getMessage());
                return redirect()->route('user-reports-addition', ['sub_id' => $request->get('subscribe_id')]);
            } catch(\Cartalyst\Stripe\Exception\MissingParameterException $e) {
                \Session::put('error',$e->getMessage());
                return redirect()->route('user-reports-addition', ['sub_id' => $request->get('subscribe_id')]);
            }
        }
        \Session::put('error','All fields are required!!');
        return redirect()->route('user-reports-addition', ['sub_id' => $request->get('subscribe_id')]);
    }     
}