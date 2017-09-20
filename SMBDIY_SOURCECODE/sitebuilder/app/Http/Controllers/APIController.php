<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
//use App\Product;
use App\User;
use Cookie;
use Illuminate\Cookie\CookieJar;
use DB;
use App\Website;
use App\Subscription;
use App\Subscribeact;
use Illuminate\View\View;
use Session;

class APIController extends Controller
{
    public function index(Request $request)
    {
        $request->session()->flush();
		Auth::logout();
		Cookie::queue(Cookie::forget('emailid'));

        return response(array(
                'status' => false,
                'message' =>'Logout successfully',
               ),200);     
    }
	public function reviewuser(Request $request)
    {
		$uid=$request->get('userid');
        $data['sites'] = Website::where('user_id', $uid)->where('site_trashed', 0)->orderBy('id', 'asc')->get()->toArray();
		$data['sub_state'] = Subscribeact::where('user_id', $uid)->where('exdate_time', '>=', date('Y-m-d'))->get()->toArray();
		if(count($data['sites'] > 0)){
        return response(array(
                'status' => true,
                'message' =>$data['sites'],
               ),200);
		}
		else
		{
		return response(array(
                'status' => false,
                'message' =>'error',
               ),200);		
		}
    } 
    public function store(Request $request)
    {
		
        //Product::create($request->all());
        return response(array(
                'error' => false,
                'message' =>'Product created successfully',
               ),200);
    }
    public function show(Request $request)
    {
        // $request->session()->flush();
		Auth::logout();
		Cookie::queue(Cookie::forget('emailid'));

        return response(array(
                'status' => false,
                'message' =>'Logout successfully',
               ),200);
    } 
    public function update(Request $request, $id)
    {
       // Product::find($id)->update($request->all());
        return response(array(
                'error' => false,
                'message' =>'Product updated successfully',
               ),200);
    }
    public function destroy($id)
    {
       // Product::find($id)->delete();
        return response(array(
                'error' => false,
                'message' =>'Product deleted successfully',
               ),200);
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
		Auth::logout();
		Cookie::queue(Cookie::forget('emailid'));

        return response(array(
                'status' => false,
                'message' =>'Logout successfully',
               ),200);
    }
}
?>