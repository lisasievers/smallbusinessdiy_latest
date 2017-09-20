<?php

namespace App\Http\Controllers;

use App\Setting;
use App\Commonsetting;
use App\Site;
use App\Payment;
use App\Page;
use App\Frame;
use App\Siteform;
use File;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
	/**
	 * List all image from image directory
	 */
	public function getAsset()
	{
		//Get Admin Images
		$adminImages = array();
		//get image file array
		$images_dir = Setting::where('name', 'images_dir')->first();
		$folderContentAdmin = File::files($images_dir->value);
		//dd($images_dir); exit;
		//check the allowed file extension and make the allowed file array
		$allowedExt = Setting::where('name', 'images_allowedExtensions')->first();
		$temp = explode('|', $allowedExt->value);
		foreach ($folderContentAdmin as $key => $item)
		{
			if( ! is_array($item))
			{
    			//check the file extension
				$ext = pathinfo($item, PATHINFO_EXTENSION);
    			//prep allowed extensions array
				if (in_array($ext, $temp))
				{
					array_push($adminImages, $item);
				}
			}
		}

		//Get User Images
		$userImages = array();
		$userID = Auth::user()->id;
		$images_uploadDir = Setting::where('name', 'images_uploadDir')->first();
		if (is_dir( $images_uploadDir->value . "/" .$userID ))
		{
			$folderContentUser = File::files($images_uploadDir->value . "/" .$userID );
			if ($folderContentUser)
			{
				foreach ($folderContentUser as $key => $item)
				{
					if ( ! is_array($item))
					{
    					//check the file extension
						$ext = pathinfo($item, PATHINFO_EXTENSION);
    					//prep allowed extensions array
    					//$temp = explode("|", $this->config->item('images_allowedExtensions'));
						if (in_array($ext, $temp))
						{
							array_push($userImages, $item);
						}
					}
				}
			}
		}

		//var_dump($folderContent);
		//var_dump($adminImages);

		return view('assets/images', compact('adminImages', 'userImages'));
	}

	/**
	 * Upload image file
	 * @param  Request $request
	 */
	public function uploadImage(Request $request)
	{
		if ($request->hasFile('userFile'))
		{
			//User upload directory
			$userID = Auth::user()->id;
			$images_uploadDir = Setting::where('name', 'images_uploadDir')->first();
			$userFolder = $images_uploadDir->value . '/' . $userID;

			//Check if the file extension is valid
			$allowedExt = Setting::where('name', 'images_allowedExtensions')->first();
			$temp = explode('|', $allowedExt->value);
			$file = $request->file('userFile');
			$ext = File::extension($file->getClientOriginalName());

			if (in_array($ext, $temp))
			{
				if ($file->move($userFolder, $file->getClientOriginalName()))
				{
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
		return redirect()->route('assets');
	}

	public function imageUploadAjax(Request $request)
	{
		if ($request->hasFile('imageFile'))
		{
			//User upload directory
			$userID = Auth::user()->id;
			$images_uploadDir = Setting::where('name', 'images_uploadDir')->first();
			$userFolder = $images_uploadDir->value . '/' . $userID;

			//Check if the file extension is valid
			$allowedExt = Setting::where('name', 'images_allowedExtensions')->first();
			$tempExt = explode('|', $allowedExt->value);
			$file = $request->file('imageFile');
			$ext = File::extension($file->getClientOriginalName());

			if (in_array($ext, $tempExt))
			{
				if ($file->move($userFolder, $file->getClientOriginalName()))
				{
					$return = array();
					$temp = array();
					$temp['header'] = 'All set!';
					$temp['content'] = 'Your image was uploaded successfully and can now be found under the \'My Images\' tab.';
					//include the partials "myimages" with all the uploaded images
					$userFolderContent = directory_map($userFolder, 2);
					if ($userFolderContent)
					{
						$userImages = array();
						foreach ($userFolderContent as $userKey => $userItem)
						{
							if ( ! is_array($userItem))
							{
								// Check the file extension
								$ext = pathinfo($userItem, PATHINFO_EXTENSION);
								// Prepared allowed extensions file array
								if (in_array($ext, $tempExt))
								{
									array_push($userImages, $userItem);
								}
							}
						}
					}
					if (isset($userImages))
					{
						$elementsDir = Setting::where('name', 'elements_dir')->first();
						$uploadDir = Setting::where('name', 'images_uploadDir')->first();
						$userSrc = url('/') . '/' . $userFolder;
						$dataURL = str_replace($elementsDir->value . '/', '', $uploadDir->value);
						$myImages = View('partials.myimages', array('userImages' => $userImages, 'userSrc' => $userSrc, 'dataURL' => $dataURL));
						$return['myImages'] = $myImages->render();
					}
					$return['responseCode'] = 1;
					$view = View('partials.success', array('data' => $temp));
					$return['responseHTML'] = $view->render();
					die(json_encode($return));
				}
				else
				{
					$temp = array();
					$temp['header'] = 'Ouch! Something went wrong:';
					$temp['content'] = 'Something went wrong when trying to upload your image.';
					$return = array();
					$return['responseCode'] = 0;
					$view = View('partials.error', array('data' => $temp));
					$return['responseHTML'] = $view->render();
					die(json_encode($return));
				}
			}
			else
			{
				$temp = array();
				$temp['header'] = 'Ouch! Something went wrong:';
				$temp['content'] = 'Something went wrong when trying to upload your image.';
				$return = array();
				$return['responseCode'] = 0;
				$view = View('partials.error', array('data' => $temp));
				$return['responseHTML'] = $view->render();
				die(json_encode($return));
			}
		}
		
	}

	/**
	 * Delete image file of user with ajax request
	 */
	public function delImage()
	{
		//echo "Rts"; exit;
		if (isset($_POST['file']) && $_POST['file'] != '')
		{
			$userID = Auth::user()->id;
			//disect the URL
			$temp = explode("/", $_POST['file']);
			$fileName = array_pop( $temp );
			$userDirID = array_pop( $temp );
			//make sure this is the user's images
			if ($userID == $userDirID)
			{
				//print_r($userDirID); exit;
				//all good, remove!
				$images_uploadDir = Setting::where('name', 'images_uploadDir')->first();
				unlink( './'. $images_uploadDir->value . '/' . $userID. '/' . $fileName );
			}
		}
	}
public function getuploadDoitforme(Request $request)
	{
	$data=array();
	$data['builder'] = false;
	$data['page'] = 'Website Form';
	$data['sess'] = $request->session()->all();
	$doit = Commonsetting::where('name', 'doit_init_cost')->first();
	$data['doit_cost']=$doit->value;
	$scost = Commonsetting::where('name', 'need_setup_costs')->first();
	$data['setup_cost']=$scost->value;
	return view('userboard.doitforme_form', $data);
	}

public function uploadDoitforme(Request $request)
	{
		$data = $request->input();
		$newdata=array(
		'user_id'=>Auth::user()->id,
		'site_category'=>$data['site_category'],
		'site_id'=>$data['site_id'],
		'site_name'=>$data['site_name'],
		'menu_list'=>$data['menu_list'],
		'slider1_desc'=>$data['slider1_desc'],
		'slider2_desc'=>$data['slider2_desc'],
		'slider3_desc'=>$data['slider3_desc'],
		'slider_headline'=>$data['slider_headline'],
		'about_us_desc'=>$data['about_us_desc'],
		'team_headline'=>$data['team_headline'],
		'team1_desc'=>$data['team1_desc'],
		'team2_desc'=>$data['team2_desc'],
		'team3_desc'=>$data['team3_desc'],
		'team4_desc'=>$data['team4_desc'],
		'team5_desc'=>$data['team5_desc'],
		'product_headline'=>$data['product_headline'],
		'product1_desc'=>$data['product1_desc'],
		'product2_desc'=>$data['product2_desc'],
		'product3_desc'=>$data['product3_desc'],
		'product4_desc'=>$data['product4_desc'],
		'product5_desc'=>$data['product5_desc'],
		'service_headline'=>$data['service_headline'],
		'service1_desc'=>$data['service1_desc'],
		'service2_desc'=>$data['service2_desc'],
		'service3_desc'=>$data['service3_desc'],
		'service4_desc'=>$data['service4_desc'],
		'service5_desc'=>$data['service5_desc'],
		'company_address'=>$data['company_address'],
		'phone_number'=>$data['phone_number'],
		'email_address'=>$data['email_address'],
		'contact_desc'=>$data['contact_desc'],
		'footer_type'=>$data['footer_type'],
		'footer_desc'=>$data['footer_desc'],
		'social_media'=>$data['social_media'],
		'blog1_title'=>$data['blog1_title'],
		'blog1_desc'=>$data['blog1_desc'],
		'blog2_title'=>$data['blog2_title'],
		'blog2_desc'=>$data['blog2_desc'],
		'other_desc'=>$data['other_desc']
		);
    $doc_id=Siteform::insertGetId($newdata);
    //Get upload directory & create a folder
    $images_uploadDir = Commonsetting::where('name', 'docs_uploadDir')->first();
	$locFolder = $images_uploadDir->value;
	File::makeDirectory($locFolder.'/'.$doc_id, 0775, true);
	$userFolder=$locFolder.'/'.$doc_id;
	//Check if the file extension is valid
	$allowedExt = Commonsetting::where('name', 'docs_allowedExtensions')->first();
	$temp = explode('|', $allowedExt->value);
			
    //Website Logo
    $imgList=array(
    	'website_logo',
    	'slider1',
    	'slider2',
    	'slider3',
    	'about_us_img',
    	'team1_img',
    	'team2_img',
    	'team3_img',
    	'team4_img',
    	'team5_img',
    	'product1_img',
    	'product2_img',
    	'product3_img',
    	'product4_img',
    	'product5_img',
    	'service1_img',
    	'service2_img',
    	'service3_img',
    	'service4_img',
    	'service5_img',
    	'blog1_img',
    	'blog2_img'
    	);
    foreach($imgList as $imup){
    if ($request->hasFile($imup))
		{
			$file = $request->file($imup);
			$ext = File::extension($file->getClientOriginalName());

			if (in_array($ext, $temp))
			{
				if ($file->move($userFolder, $file->getClientOriginalName()))
				{
					//Update document name in DB
					$updata1=array($imup =>$file->getClientOriginalName());
					Siteform::where('id', $doc_id)->update($updata1);
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
	}	
	return redirect()->route('paymentodo', ['site_id' => $doc_id]);
	}
public function getDoitformeForm(Request $request,$cat_id)
	{
	$data=array();
	$data['builder'] = false;
	$data['page'] = 'Website Form';
	$data['sess'] = $request->session()->all();
	$data['form'] = Siteform::where('id',$cat_id)->first();
	return view('userboard.update_doitforme_form', $data);
	}
public function viewDoitformeForm(Request $request,$cat_id)
	{
	$data=array();
	$data['builder'] = false;
	$data['page'] = 'Website Form';
	$data['sess'] = $request->session()->all();
	$data['form'] = Siteform::where('id',$cat_id)->first();
	return view('userboard.view_doitforme_form', $data);
	}	
public function postDoitformeForm(Request $request)
	{
	$data=array();
	$data = $request->input();
	$updata=array(
		'site_name'=>$data['site_name'],
		'menu_list'=>$data['menu_list'],
		'slider1_desc'=>$data['slider1_desc'],
		'slider2_desc'=>$data['slider2_desc'],
		'slider3_desc'=>$data['slider3_desc'],
		'slider_headline'=>$data['slider_headline'],
		'about_us_desc'=>$data['about_us_desc'],
		'team_headline'=>$data['team_headline'],
		'team1_desc'=>$data['team1_desc'],
		'team2_desc'=>$data['team2_desc'],
		'team3_desc'=>$data['team3_desc'],
		'team4_desc'=>$data['team4_desc'],
		'team5_desc'=>$data['team5_desc'],
		'product_headline'=>$data['product_headline'],
		'product1_desc'=>$data['product1_desc'],
		'product2_desc'=>$data['product2_desc'],
		'product3_desc'=>$data['product3_desc'],
		'product4_desc'=>$data['product4_desc'],
		'product5_desc'=>$data['product5_desc'],
		'service_headline'=>$data['service_headline'],
		'service1_desc'=>$data['service1_desc'],
		'service2_desc'=>$data['service2_desc'],
		'service3_desc'=>$data['service3_desc'],
		'service4_desc'=>$data['service4_desc'],
		'service5_desc'=>$data['service5_desc'],
		'company_address'=>$data['company_address'],
		'phone_number'=>$data['phone_number'],
		'email_address'=>$data['email_address'],
		'contact_desc'=>$data['contact_desc'],
		'footer_type'=>$data['footer_type'],
		'footer_desc'=>$data['footer_desc'],
		'social_media'=>$data['social_media'],
		'blog1_title'=>$data['blog1_title'],
		'blog1_desc'=>$data['blog1_desc'],
		'blog2_title'=>$data['blog2_title'],
		'blog2_desc'=>$data['blog2_desc'],
		'other_desc'=>$data['other_desc']
		);
    Siteform::where('id', $data['form_id'])->update($updata);
	//Get upload directory & create a folder
    $images_uploadDir = Commonsetting::where('name', 'docs_uploadDir')->first();
	$locFolder = $images_uploadDir->value;
	//File::makeDirectory($locFolder.'/'.$doc_id, 0775, true);
	$userFolder=$locFolder.'/'.$data['form_id'];
	//Check if the file extension is valid
	$allowedExt = Commonsetting::where('name', 'docs_allowedExtensions')->first();
	$temp = explode('|', $allowedExt->value);
			
    //Website Logo
    $imgList=array(
    	'website_logo',
    	'slider1',
    	'slider2',
    	'slider3',
    	'about_us_img',
    	'team1_img',
    	'team2_img',
    	'team3_img',
    	'team4_img',
    	'team5_img',
    	'product1_img',
    	'product2_img',
    	'product3_img',
    	'product4_img',
    	'product5_img',
    	'service1_img',
    	'service2_img',
    	'service3_img',
    	'service4_img',
    	'service5_img',
    	'blog1_img',
    	'blog2_img'
    	);
    foreach($imgList as $imup){
    if ($request->hasFile($imup))
		{
			$file = $request->file($imup);
			$ext = File::extension($file->getClientOriginalName());

			if (in_array($ext, $temp))
			{
				if ($file->move($userFolder, $file->getClientOriginalName()))
				{
					//Update document name in DB
					$updata1=array($imup =>$file->getClientOriginalName());
					Siteform::where('id', $data['form_id'])->update($updata1);
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
	}
		
	 return redirect()->route('mysites');
	}

}