<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Site;
use App\Page;
use App\Frame;
use App\Setting;
use App\Commonsetting;
use App\Payment;
use App\Site_check_report;
use App\Web_common_info;
use DB;
use App\Website;
use App\Subscription;
use App\Subscribeact;

use DateTime;
use Session;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\Datatables\Facades\Datatables;
class ReportController extends Controller
{
   public function getUsereportsHome(Request $request)
	{
		//dd($request->all());

	$data=array();
	$data['page'] = 'Reports Home';
	if (Auth::user()->type != 'admin')
		{
		  $data['sites'] = Website::where('user_id', Auth::user()->id)->where('site_trashed', 0)->orderBy('id', 'asc')->get()->toArray();
		  $data['sub_state'] = Subscribeact::where('user_id', Auth::user()->id)->where('exdate_time', '>=', date('Y-m-d'))->get()->toArray();
        }
		else
		{
			$data['sites'] = Website::where('site_trashed', 0)->orderBy('id', 'asc')->get()->toArray();
		}
	$data['builder'] = false;
	//$data['site_info'] = array();

	return view('usereport.home', $data);
	}
   public function getAddsites(Request $request)
    {
        //dd($request->all());

    $data=array();
    $data['page'] = 'Reports Home';
    if (Auth::user()->type != 'admin')
        {
          $data['sites'] = Website::where('user_id', Auth::user()->id)->where('site_trashed', 0)->orderBy('id', 'asc')->get()->toArray();
          $sub_state = Subscribeact::where('user_id', Auth::user()->id)->where('exdate_time', '>=', date('Y-m-d'))->get()->toArray();
        }
        else
        {
            $data['sites'] = Website::where('site_trashed', 0)->orderBy('id', 'asc')->get()->toArray();
        }
    $data['builder'] = false;
    if( isset($sub_state) && count( $sub_state ) > 0 ){

        return view('usereport.addsites', $data);
    }
    else
    {
        return redirect()->route('user.reportshome');
    }
    }
public function postAddsites(Request $request)
    {
        //dd($request->all());

   $isvalid=$this->validate($request, [
            'site_name' => 'required|max:180'
            ]);
    $pay = new Website();
    $pay->user_id = Auth::user()->id;
    $pay->site_name = $request->get('site_name');
    $pay->date_time = date('Y-m-d H:i:s');
    $pay->site_trashed = 0;
    $pay->save();
    return redirect()->route('user.reportshome');
    }
public function getUserShowreports(Request $request)
	{
	$data=array();
	//$cstep=$request->get('step');
	//dd($cstep);
	$data['builder'] = false;
	$data['page'] = 'Reports Tools';
	$data['sess'] = $request->session()->all();
	$doit = Commonsetting::where('name', 'doit_init_cost')->first();
	$data['doit_cost']=$doit->value;
	$scost = Commonsetting::where('name', 'need_setup_costs')->first();
	$data['setup_cost']=$scost->value;
	//$data['site_report'] = Site_check_report::where('id', 3)->get();
	
		if (Auth::user()->type != 'admin')
		{
			$data['sites'] = Website::where('user_id', Auth::user()->id)->where('site_trashed', 0)->orderBy('id', 'asc')->get()->toArray();
		}
		else
		{
			$data['sites'] = Website::where('site_trashed', 0)->orderBy('id', 'asc')->get()->toArray();
		}
		 //$data['line_chart'] = array();
         $data['siteid']='';
	return view('usereport.sitereports',$data);
	}	
public function postUserShowreports(Request $request)
	{
		//dd($request->all());
	$data=array();
	$data['page'] = 'Reports Home';
	$site_id=$request->get('sitename');
    $data['siteid']=$site_id;
	/* Site lists */
	if (Auth::user()->type != 'admin')
		{
		  $data['sites'] = Website::where('user_id', Auth::user()->id)->where('site_trashed', 0)->orderBy('id', 'asc')->get()->toArray();
		  $data['sub_state'] = Subscribeact::where('user_id', Auth::user()->id)->where('exdate_time', '>=', date('Y-m-d'))->get()->toArray();
        }
		else
		{
			$data['sites'] = Website::where('site_trashed', 0)->orderBy('id', 'asc')->get()->toArray();
		}
	/* Sitespy review reports */
	$sinfo = Website::where('id', $site_id)->first();
	$sitename=$sinfo->site_name;
	$rq = "SELECT ca_pagespeed.data FROM ca_website 
	LEFT JOIN ca_pagespeed ON ca_website.id = ca_pagespeed.wid 
	WHERE ca_website.domain LIKE '%$sitename%' 
	ORDER BY id DESC LIMIT 1";
	//dd($rq);
    $rev = DB::select(DB::raw($rq));
	/* Sitespy alexa ranking reports */
	$alxsql = "SELECT alexa_info.* FROM alexa_info 
	WHERE alexa_info.domain_name LIKE '%$sitename%' 
	ORDER BY id DESC LIMIT 2";
	//dd($alxsql);
    $data['alx'] = DB::select(DB::raw($alxsql));
	//dd($data['alx']);
   /* Sitedoctor reports */	
	//$data['site_info'] = Site_check_report::where('site_id', $request->get('sitename'))->get();
	$data['site_info'] = Site_check_report::where('domain_name', 'LIKE', "%$sitename")->select('id','speed_score','speed_score_mobile','speed_usability_mobile','searched_at' )->orderBy('id', 'asc')->get();
	//dd($data['site_info']);
 	if(isset($data["site_info"][0])) { $page_title= strtolower($data["site_info"][0]["domain_name"]);
      // else exit();

       $data["page_title"]=str_replace(array("www.","http://","https://"), "", $page_title);
   }
    /* Sitespy reports */
    $data['domain_info']=array();
    $data['domain_info'] = Web_common_info::where('id', $request->get('sitename'))->get();  

	/* Website Review reports */
    $data['_info'] = Web_common_info::where('id', $request->get('sitename'))->get(); 
   // dd($data['site_info']);

    	// Visitor page charts
        $to_date = date("Y-m-d");
        $from_date = date("Y-m-d",strtotime("$to_date-30 days"));

        $to_date = $to_date;
        $from_date = $from_date;
        $myquery1 ="SELECT * FROM domain WHERE site_id= $site_id ";
        //dd($myquery1);
        //$domain_info=array();
    $domain_info = DB::select(DB::raw($myquery1));
    if(!empty($domain_info)){
        $table = $domain_info[0]->table_name;
        // this domain name will be placed for all the pages of visitor analysis tab
        $info['domain_name'] = $domain_info[0]->domain_name;
        $data['domain_id'] = $domain_info[0]->id;
       // $total_page_view = $this->basic->get_data($table,$where,$select='');
        $sql2 = "SELECT * FROM $table WHERE date_time BETWEEN '$from_date%' AND '$to_date%'";
        //dd($sql2);
        $total_page_view = DB::select(DB::raw($sql2));
       // dd($total_page_view);
      //  $total_unique_visitor = $this->basic->get_data($table,$where,$select='',$join='',$limit='',$start='',$order_by='',$group_by='cookie_value');
         $sql3 = "SELECT * FROM $table WHERE date_time BETWEEN '$from_date%' AND '$to_date%' GROUP BY cookie_value";
        //dd($sql2);
        $total_unique_visitor = DB::select(DB::raw($sql3));


       // $select = array("count(id) as session_number","last_scroll_time","last_engagement_time");
        //$total_unique_session = $this->basic->get_data($table,$where,$select,$join='',$limit='',$start='',$order_by='',$group_by='session_value');
         $sql4 = "SELECT count(id) as session_number,last_scroll_time,last_engagement_time FROM $table WHERE date_time BETWEEN '$from_date%' AND '$to_date%' GROUP BY session_value";
        //dd($sql2);
        $total_unique_session = DB::select(DB::raw($sql4));
        $bounce = 0;
        $no_bounce = 0;
        foreach($total_unique_session as $value){
            if($value->session_number > 1)
                $no_bounce++;
            if($value->session_number == 1){
                if($value->last_scroll_time=="0000-00-00 00:00:00" && $value->last_engagement_time=="0000-00-00 00:00:00")
                    $bounce++;
                else
                    $no_bounce++;
            }
        }
        $bounce_no_bounce = $bounce+$no_bounce;
        if($bounce_no_bounce == 0)
            $bounce_rate = 0;
        else
            $bounce_rate = number_format($bounce*100/$bounce_no_bounce, 2);

        // code for average stay time
        //"if(status='1',count(book_info.id),0) as available_book"
        /*$select = array(
            "date_time as stay_from",
            "last_engagement_time",
            "last_scroll_time"
            );
        $stay_time_info = $this->basic->get_data($table,$where,$select,$join='',$limit='',$start='',$order_by='',$group_by='');
        */
        $sql4a = "SELECT date_time as stay_from,last_engagement_time,last_scroll_time FROM $table WHERE date_time BETWEEN '$from_date%' AND '$to_date%' ";
        //dd($sql5);
        $stay_time_info = DB::select(DB::raw($sql4a));

        $total_stay_time = 0;
        if(!empty($stay_time_info)) {
            foreach($stay_time_info as $value){
                $total_stay_time_individual = 0;
                if($value->last_scroll_time=='0000-00-00 00:00:00' && $value->last_engagement_time=='0000-00-00 00:00:00')
                    $total_stay_time = $total_stay_time + $total_stay_time_individual;
                else if ($value->last_scroll_time=='0000-00-00 00:00:00' && $value->last_engagement_time!='0000-00-00 00:00:00'){
                    $total_stay_time_individual = strtotime($value->last_engagement_time) - strtotime($value->stay_from);
                    $total_stay_time = $total_stay_time + $total_stay_time_individual;
                }
                else if ($value->last_scroll_time!='0000-00-00 00:00:00' && $value->last_engagement_time=='0000-00-00 00:00:00'){
                   $total_stay_time_individual = strtotime($value->last_scroll_time) - strtotime($value->stay_from);
                   $total_stay_time = $total_stay_time + $total_stay_time_individual;
                }
                else {
                    if($value->last_scroll_time > $value->last_engagement_time){
                       $total_stay_time_individual = strtotime($value->last_scroll_time) - strtotime($value->stay_from);
                       $total_stay_time = $total_stay_time + $total_stay_time_individual;
                    }
                    else{
                       $total_stay_time_individual = strtotime($value->last_engagement_time) - strtotime($value->stay_from);  
                       $total_stay_time = $total_stay_time + $total_stay_time_individual;
                    }
                }
            }
        }


        $average_stay_time = 0;
        if($total_stay_time != 0)
            $average_stay_time = $total_stay_time/count($total_unique_session);

        $hours = 0;
        $minutes = 0;
        $seconds = 0;

        $hours = floor($average_stay_time / 3600);
        $minutes = floor(($average_stay_time / 60) % 60);
        $seconds = $average_stay_time % 60;        

        // end of average stay time

        // code for line chart
      /*  $where = array();
        $where['where'] = array(
            "date_time >=" => $from_date,
            "date_time <=" => $to_date,
            "is_new" => 1
            );
        $select = array(
            "date_format(date_time,'%Y-%m-%d') as date",
            "count(id) as number_of_user"
            );
        $day_wise_visitor = $this->basic->get_data($table,$where,$select,$join='',$limit='',$start='',$order_by='',$group_by="date");
        */
        $sql5 = "SELECT date_format(date_time,'%Y-%m-%d') as date, count(id) as number_of_user FROM $table WHERE date_time BETWEEN '$from_date%' AND '$to_date%' AND is_new=1 GROUP BY date";
        //dd($sql5);
        $day_wise_visitor = DB::select(DB::raw($sql5));
        
        $day_count = date('Y-m-d', strtotime($from_date. " + 1 days"));


        foreach ($day_wise_visitor as $value){
            $day_wise_info[$value->date] = $value->number_of_user;
        }

        $dDiff = strtotime($to_date) - strtotime($from_date);
        $no_of_days = floor($dDiff/(60*60*24));
        $line_char_data = array();
        for($i=0;$i<=$no_of_days+1;$i++){
            $day_count = date('Y-m-d', strtotime($from_date. " + $i days"));
            if(isset($day_wise_info[$day_count])){
                $line_char_data[$i]['user'] = $day_wise_info[$day_count];
            } else {
                $line_char_data[$i]['user'] = 0;
            }
            $line_char_data[$i]['date'] = date('Y-m-d', strtotime($from_date. " + $i days"));
        }
        // end of code for line chart

        $data['line_chart'] = $line_char_data;
        $data['total_page_view'] = number_format(count($total_page_view));
        $data['total_unique_visitor'] = number_format(count($total_unique_visitor));
        $data['total_unique_session'] = number_format(count($total_unique_session));
        if(count($total_unique_visitor) == 0)
            $data['average_visit'] = number_format(count($total_page_view));
        else
            $data['average_visit'] = number_format(count($total_page_view)/count($total_unique_visitor), 2);

        $data['average_stay_time'] = $hours.":".$minutes.":".$seconds;
        $data['bounce_rate'] = $bounce_rate." %";
        $data['from_date'] = date("d-M-y",strtotime($from_date));
        $data['to_date'] = date("d-M-y",strtotime($to_date));
    }
	else
	{
	$data['from_date'] = date("d-M-y");	
	$data['line_chart'] = array();	
	}
       // echo json_encode($info);
        //visitor page analysis

	return view('usereport.home', $data);
	}		
public function getUserFreereports(Request $request)
	{

	$data=array();
	//$cstep=$request->get('step');
	//dd($cstep);
	$data['builder'] = false;
	$data['page'] = 'Free Report Tools';
	return view('usereport.freereport_home',$data);
	}
public function getUserPaidreports(Request $request)
	{

	$data=array();
	//$cstep=$request->get('step');
	//dd($cstep);
    if (Auth::user()->type != 'admin')
        {
            $data['sites'] = Website::where('user_id', Auth::user()->id)->where('site_trashed', 0)->orderBy('id', 'asc')->get()->toArray();
            $data['sub_state'] = Subscribeact::where('user_id', Auth::user()->id)->where('exdate_time', '>=', date('Y-m-d'))->get()->toArray();
        }
        else
        {
            $data['sites'] = Website::where('site_trashed', 0)->orderBy('id', 'asc')->get()->toArray();
        }
     //dd($data['sub_state']);   
	$data['builder'] = false;
	$data['page'] = 'Paid Report Tools';
	return view('usereport.paidreport_home',$data);
	}	
public function getQrcode(Request $request)
	{
	//dd('ss');exit;
	$data=array();
	//$cstep=$request->get('step');
	//dd($cstep);
	$data['builder'] = false;
	$data['page'] = 'Free Report Tools';
	return view('usereport.qrcode',$data);
	}	
public function getMobiletest(Request $request)
	{
	$data=array();
	//$cstep=$request->get('step');
	//dd($cstep);
	$data['builder'] = false;
	$data['page'] = 'Free Report Tools';
	return view('usereport.google_mobile_test',$data);
	}
public function postMobiletest(Request $request)
	{
	$data=array();
	$data['url']=$request->get('itemid');
	//dd($cstep);
	$data['builder'] = false;
	$data['page'] = 'Free Report Tools';
	echo json_encode($data);
	}	
}

