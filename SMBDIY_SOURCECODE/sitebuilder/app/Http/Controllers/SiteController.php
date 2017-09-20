<?php

namespace App\Http\Controllers;

use App\User;
use App\Site;
use App\Page;
use App\Frame;
use App\Setting;
use App\Commonsetting;
use App\Payment;
use App\Sitecat;
use App\Siteform;
use App\Subscription;
use App\Subscribeact;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use File;
use DB;
use Illuminate\View\View;
use Session;
use Sunra\PhpSimple\HtmlDomParser;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use App\CI_Library\FTP\CI_FTP;
use Maatwebsite\Excel\Facades\Excel;
use DateTime;
use Yajra\Datatables\Facades\Datatables;

class SiteController extends Controller
{
	/**
	 * Dashboard after login
	 */
	public function getDashboard()
	{
		// Get site data
		if (Auth::user()->type != 'admin')
		{
			$siteData = Site::with('user')->where('user_id', Auth::user()->id)->where('site_trashed', 0)->orderBy('id', 'asc')->get()->toArray();
		}
		else
		{
			$siteData = Site::with('user')->where('site_trashed', 0)->orderBy('id', 'asc')->get()->toArray();
		}

		//dd($siteData);
		//echo $siteData[0]['user']['email'];

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
		//dd($allSites);
		$users = User::orderBy('id', 'asc')->get();

		//dd($allSites);
		return view('sites.sites', array('sites' => $allSites, 'users' => $users));
	}

	/**
	 * Create New Site
	 */
	public function getSiteCreate()
	{
		$site = new Site();
		$site->user_id = Auth::user()->id;
		//position to setup sitename //
		if(Session::get('site_name')!=""){$site->site_name = Session::get('site_name');}else{$site->site_name = 'My New Site';}
		if(Session::get('whodo')!=""){$site->create_category = Session::get('whodo');}else{$site->create_category  = '';}
		if(Session::get('site_email')!=""){$site->domain_email = Session::get('site_email');}else{$site->domain_email = '';}
		
		$site->site_trashed = 0;
		$site->save();

		$page = new Page();
		$page->site_id = $site->id;
		$page->name = 'index';
		$page->save();

		return redirect()->route('site', ['site_id' => $site->id]);
	}

	/**
	 * Bring saved site on canvas
	 * @param  Request $request
	 * @param  Integer $site_id
	 */
	public function getSite(Request $request, $site_id)
	{
		$request->session()->put('siteID', $site_id);
		$site = Site::where('id', $site_id)->get();

		// If user is not an admin then check if user own this site
		if (Auth::user()->type != 'admin')
		{
			if ($site[0]['user_id'] != Auth::user()->id)
			{
				return redirect()->route('dashboard');
			}
		}

		// Get site details
		$siteArray['site'] = Site::where('id', $site_id)->get();
		$data['payif']=array();
		$data['payif'] = Payment::where('site_id', $site_id)->get();
		// Get page details
		$pages = Page::where('site_id', $site_id)->get();		
		foreach ($pages as $page)
		{
			$frames = Frame::where('page_id', $page->id)->where('revision', 0)->orderBy('id', 'ASC')->get();
			$pageDetails['blocks'] = $frames;
			$pageDetails['page_id'] = $page->id;
			$pageDetails['pages_title'] = $page->title;
			$pageDetails['meta_description'] = $page->meta_description;
			$pageDetails['meta_keywords'] = $page->meta_keywords;
			$pageDetails['header_includes'] = $page->header_includes;
			$pageDetails['css'] = $page->css;
			$pageFrames[$page->name] = $pageDetails;
		}
		$siteArray['pages'] = $pageFrames;

		// Get directory details
		$settings = Setting::where('name', 'elements_dir')->first();
		$siteArray['assetFolders'] = File::directories($settings['value']);

		if (count($siteArray) > 0)
		{
			// Get Site Data
			$data['siteData'] = $siteArray;

			// Get Page Data
			$pageA = Page::where('site_id', $site_id)->get();
			foreach ($pageA as $pageB)
			{
				$framesA = Frame::where('page_id', $pageB->id)->where('revision', 0)->orderBy('id', 'ASC')->get();
				$pageFrame[$pageB->name] = $pageB;
			}
			$data['pagesData'] = $pageFrame;

			// Collect data for the image library
			//
			// User Images
			$elementsDir = Setting::where('name', 'elements_dir')->first();
			$uploadDir = Setting::where('name', 'images_uploadDir')->first();
			$imageDir = Setting::where('name', 'images_dir')->first();
			$allowedExt = Setting::where('name', 'images_allowedExtensions')->first();
			$temp = explode('|', $allowedExt->value);
			if (is_dir($uploadDir->value . '/' . Auth::user()->id))
			{
				$userFolderContent = directory_map($uploadDir->value . '/' . Auth::user()->id, 2);
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
							if (in_array($ext, $temp))
							{
								array_push($userImages, $userItem);
							}
						}
					}
				}
				else
				{
					$userImages = false;
				}
			}
			else
			{
				$userImages = false;
			}
			//dd($userImages);
			//$data['userImages'] = $userImages;
			if (isset($userImages))
			{
				$userSrc = url('/') . '/' . $uploadDir->value . '/' . Auth::user()->id;
				$dataURL = str_replace($elementsDir->value . '/', '', $uploadDir->value);
				$data['userImages'] = View('partials.myimages', array('userImages' => $userImages, 'userSrc' => $userSrc, 'dataURL' => $dataURL));
			}
			// Admin Images
			$adminFolderContent = directory_map($imageDir->value, 2);
			if ($adminFolderContent)
			{
				$adminImages = array();
				foreach ($adminFolderContent as $adminKey => $adminItem)
				{
					if ( ! is_array($adminItem))
					{
							// Check the file extension
						$ext = pathinfo($adminItem, PATHINFO_EXTENSION);
							// Prepared allowed extensions file array
						if (in_array($ext, $temp))
						{
							array_push($adminImages, $adminItem);
						}
					}
				}
			}
			else
			{
				$adminImages = false;
			}
			//dd($adminImages);
			//$data['adminImages'] = $adminImages;
			if (isset($adminImages))
			{
				$adminSrc = url('/') . '/' . $imageDir->value;
				$dataURL = str_replace($elementsDir->value . '/', '', $imageDir->value);
				$data['adminImages'] = View('partials.adminimages', array('adminImages' => $adminImages, 'adminSrc' => $adminSrc, 'dataURL' => $dataURL));
			}
			// Pre-build templates
			$templatePages = Page::where('template', 1)->get();
			//dd($templatePages);
			foreach ($templatePages as $templatePage)
			{
				$templaePageFrames = array();
				$templateFrames = Frame::where('page_id', $templatePage->id)->where('revision', 0)->get();
				foreach ($templateFrames as $templateFrame)
				{
					$tFrame = array();
					$tFrame['pageName'] = $templatePage->name;
					$tFrame['pageID'] = $templatePage->id;
					$tFrame['id'] = $templateFrame->id;
					$tFrame['height'] = $templateFrame->height;
					$tFrame['original_url'] = $templateFrame->original_url;
					$templatePageFrames[] = $tFrame;
				}
				$templates[$templatePage->id] = $templatePageFrames;
			}
			if (isset($templates))
			{
				$data['templates'] = View('partials.templateframes', array('pages' => $templates));
			}
		}
		else
		{
			return redirect()->route('dashboard');
		}

		// Grab all revisions
		$pages = Page::where('site_id', $site_id)->where('name', 'index')->get();
		if (count($pages[0]) > 0)
		{
			$pageID = $pages[0]->id;
			$frames = Frame::where('site_id', $site_id)->where('revision', 1)->where('page_id', $pageID)->orderBy('updated_at', 'DESC')->get();

		}
		else
		{
			$frames = false;
		}

		$data['revisions'] = $frames;
		//dd($data['revisions']);
		$revisionView = View('partials.revisions', array('data' => $data['revisions'], 'page' => $pages[0]->name));
		$data['revisionView'] = $revisionView->render();
		//dd($data['revisionView']);

		// Generate pagedata view
		if (count($data['pagesData']) > 0)
		{
			$pageView = View('partials.pagedata', array('data' => $data['pagesData']));
			$data['pagedataView'] = $pageView->render();
		}
		else
		{
			$pageView = View('partials.pagedata', array('data' => $data['siteData']['pages']['index']));
			$data['pagedataView'] = $pageView->render();
		}

		$data['builder'] = true;
		$data['page'] = 'site';

		//dd($data);
		return view('sites.create', ['data' => $data]);
	}

	/**
	 * Bring the site data
	 * @param  Request $request
	 * @return JSON
	 */
	public function getSiteData(Request $request)
	{
		$siteID = $request->session()->get('siteID');
		//echo var_dump($request->session());
		$site = Site::where('id', $siteID)->get();
		//echo var_dump($site);
		$siteArray['site'] = $site[0];

		$pages = Page::where('site_id', $siteID)->get();
		foreach ($pages as $page)
		{
			$frames = Frame::where('page_id', $page->id)->where('revision', 0)->orderBy('id', 'ASC')->get();
			$pageDetails['blocks'] = $frames;
			$pageDetails['page_id'] = $page->id;
			$pageDetails['pages_title'] = $page->title;
			$pageDetails['meta_description'] = $page->meta_description;
			$pageDetails['meta_keywords'] = $page->meta_keywords;
			$pageDetails['header_includes'] = $page->header_includes;
			$pageDetails['page_css'] = $page->css;
			$pageFrames[$page->name] = $pageDetails;
		}
		$siteArray['pages'] = $pageFrames;

		echo json_encode($siteArray);
	}

	/**
	 * Save page and frame
	 * @param  Request $request
	 * @param  Integer $forPublish
	 * @return JSON
	 */
	public function postSave(Request $request, $forPublish = 0)
	{
		if ( ! isset($request['siteData']))
		{
			$temp = array();
			$temp['header'] = 'Ouch! Something went wrong:';
			$temp['content'] = 'The siteData is missing, please try again.';
			$return = array();
			$return['responseCode'] = 0;
			$view = View('partials.error', array('data' => $temp));
			$return['responseHTML'] = $view->render();

			die(json_encode($return));
		}
		$siteData = $request['siteData'];
		//dd($siteData);
		$site = Site::where('id', $siteData['id'])->first();
		//die(var_dump($site));
		$site->site_name = $siteData['site_name'];
		$site->update();
		//dd($request['pages']);
		foreach ($request['pages'] as $page => $pageData)
		{
			//dd($pageData);
			//dd($page);
			//dealing with a changed page
			if ($pageData['status'] == 'changed')
			{
				// Get page data
				$pageNew = Page::where('site_id', $siteData['id'])->where('name', $page)->first();
				$pageNew->site_id = $siteData['id'];
				$pageNew->name = $page;
				$pageNew->title = $pageData['pageSettings']['title'];
				$pageNew->meta_keywords = $pageData['pageSettings']['meta_keywords'];
				$pageNew->meta_description = $pageData['pageSettings']['meta_description'];
				$pageNew->header_includes = $pageData['pageSettings']['header_includes'];
				$pageNew->css = $pageData['pageSettings']['page_css'];
				$pageNew->update();
				$pageID = $pageNew->id;
			}
			elseif ($pageData['status'] == 'new')
			{
				$pageNew = new Page();
				$pageNew->site_id = $siteData['id'];
				$pageNew->name = $page;
				$pageNew->title = $pageData['pageSettings']['title'];
				$pageNew->meta_keywords = $pageData['pageSettings']['meta_keywords'];
				$pageNew->meta_description = $pageData['pageSettings']['meta_description'];
				$pageNew->header_includes = $pageData['pageSettings']['header_includes'];
				$pageNew->css = $pageData['pageSettings']['page_css'];
				$pageNew->save();
				$pageID = $pageNew->id;
			}

            // Page done, onto the blocks
            // Push existing frames into revision
			$frames = Frame::where('page_id', $pageID)->get();
			if ($frames)
			{
				foreach ($frames as $frame)
				{
					$frame->revision = 1;
					$frame->update();
				}

			}
			//dd($frames);
			$frame_url = Frame::where('page_id', $pageID)->where('site_id', $siteData['id'])->first();
			//dd($frame_url);
			//dd($frame_url->original_url);exit;
			if (isset($pageData['blocks']))
			{
				//dd$pageData['blocks']);
				foreach ($pageData['blocks'] as $block)
				{
					//dd($block);
					$frames = new Frame();
					$frames->page_id = $pageID;
					$frames->site_id = $siteData['id'];
					$frames->content = $block['frameContent'];
					$frames->height = $block['frameHeight'];
					if(isset($block['originalUrl'])){ $frames->original_url = $block['originalUrl']; }else{$frames->original_url =$frame_url->original_url; }
					$frames->loaderfunction = $block['loaderFunction'];
					$frames->sandbox = ($block['sandbox'] == 'true') ? 1 : 0;
					$frames->save();
				}
			}
		}

		// Delete any pages?
		//dd($request['toDelete']);
		if (isset($request['toDelete']) && is_array($request['toDelete']) && count($request['toDelete']) > 0)
		{
			foreach ($request['toDelete'] as $page)
			{
				$page = Page::where('site_id', $siteData['id'])->where('name', $page)->first();
				if ($page)
				{
					$page->delete();
				}
			}

		}

		$return = array();

        // Regular site save
		if ($forPublish == 0)
		{
			$temp = array();
			$temp['header'] = "Success!";
			$temp['content'] = "The site has been saved successfully!";
		}
        // Saving before publishing, requires different message
		elseif ($forPublish == 1)
		{
			$temp = array();
			$temp['header'] = "Success!";
			$temp['content'] = "The site has been saved successfully! You can now proceed with publishing your site.";
		}

		$return['responseCode'] = 1;
		$view = View('partials.success', array('data' => $temp));
		$return['responseHTML'] = $view->render();

		die(json_encode($return));
	}

	/**
	 * Get frame content by Frame ID
	 * @param  Integer 	$frame_id
	 * @return String
	 */
	public function getFrame($frame_id)
	{
		$frame = Frame::where('id', $frame_id)->first();
		//dd($frame);
		echo $frame->content;
	}

	/**
	 * Get site info with ajax call
	 * @param  Request $request
	 * @param  Integer $site_id
	 * @return JSON
	 */
	public function getSiteAjax(Request $request, $site_id)
	{
		$siteData = Site::where('id', $site_id)->get();
		$siteCData = Sitecat::where('status', '1')->get();
		$return['responseCode'] = 1;
		$view = View('partials.sitedata', array('data' => $siteData,'cat' => $siteCData));
		$return['responseHTML'] = $view->render();
		echo json_encode($return);
	}

	/**
	 * Live preview site
	 * @param  Request $request
	 * @return HTML
	 */
	public function postLivePreview(Request $request)
	{
		//die(print_r( $request->all() ));
		$payif = Payment::where('site_id', $request->input('siteID'))->first();
		if($payif->status != 1 || $payif->amount <= 0 ){ return redirect()->route('dashboard'); }

		if ($request->has('siteID'))
		{
			$siteData = Site::where('id', $request->input('siteID'))->first();
		}
		$head = "";
		// Title
		if ($request->has('meta_title'))
		{
			$head .= '<title>'.$request->input('meta_title').'</title>'."\n";
		}
		// Meta description
		if ($request->has('meta_description'))
		{
			$head .= '<meta name="description" content="'.$request->input('meta_description').'"/>'."\n";
		}
		// Meta keywords
		if ($request->has('meta_keywords'))
		{
			$head .= '<meta name="keywords" content="'.$request->input('meta_keywords').'"/>'."\n";
		}
		// Header includes
		if ($request->has('header_includes'))
		{
			$head .= $request->input('header_includes')."\n";
		}
		// Page css
		if ($request->has('page_css'))
		{
			$head .= "\n<style>".$request->input('page_css')."</style>\n";
		}
		// Global css
		if ($siteData->global_css != '')
		{
			$head .= "\n<style>".$siteData->global_css."</style>\n";
		}
        // Custom header to deal with XSS protection
		header("X-XSS-Protection: 0");
		echo str_replace(' <!--headerIncludes-->', $head, "<!DOCTYPE html>\n".$request['page']);
	}

	/**
	 * Get revision info
	 * @param  Integer $site_id
	 * @param  String  $page
	 */
	public function getRevisions($site_id, $page)
	{
		if ($site_id != '' && $page != '')
		{
			// Grab all revisions
			$pages = Page::where('site_id', $site_id)->where('name', $page)->get();
			if (count($pages[0]) > 0)
			{
				$page_id = $pages[0]->id;
				$frames = Frame::where('site_id', $site_id)->where('revision', 1)->where('page_id', $page_id)->orderBy('updated_at', 'DESC')->get();

			}
			else
			{
				$frames = false;
			}

			$data['revisions'] = $frames;
			//dd($data['revisions']);
			$revisionView = View('partials.revisions', array('data' => $data['revisions'], 'page' => $pages[0]->name));
			$revision = $revisionView->render();
			echo $revision;
		}
	}

	/**
	 * Retrive a preview for a revision
	 * @param  Integer $site_id
	 * @param  Integer $datetime
	 * @param  String  $page
	 */
	public function getRevisionPreview($site_id, $datetime, $page)
	{
		if ($site_id == '' || $datetime == '' || $page == '')
		{
			die('Missing data, revision could not be loaded');
		}

		$page = Page::where('site_id', $site_id)->where('name', $page)->first();
		$page_id = $page->id;
		$frames = Frame::where('site_id', $site_id)->where('updated_at', date('Y-m-d H:i:s', $datetime))->where('revision', 1)->where('page_id', $page_id)->get();
		$skeleton = HtmlDomParser::file_get_html('./elements/skeleton.html');

		// Get the page container
		$ret = $skeleton->find('div[id=page]', 0);

		$page = '';

		foreach($frames as $frame)
		{
			//dd($frame);
			$frameHTML = HtmlDomParser::str_get_html( $frame->content );
			//dd($frameHTML);
			$frameContent = $frameHTML->find('div[id=page]', 0);
			$page .= $frameContent->innertext;
		}

		$ret->innertext = $page;

		// Print it!
		echo $skeleton;

		//$revisionOutput = $this->revisionmodel->buildRevision($siteID, $revisionStamp, $page);

		//echo $revisionOutput;
	}

	/**
	 * Delete Revision
	 * @param  Integer $site_id
	 * @param  Integer $datetime
	 * @param  String  $page
	 * @return JSON
	 */
	public function getRevisionDelete($site_id, $datetime, $page)
	{
		//dd($page);
		//DB::enableQueryLog();
		$return = array();
		if ($site_id == '' || $datetime == '' || $page == '')
		{
			$return['code'] = 0;
			$return['message'] = 'Some data is missing, we can not delete this revision right now. Please try again later.';
			die(json_encode($return));
		}

		$q_page = Page::where('site_id', $site_id)->where('name', $page)->first();
		$frame = Frame::where('site_id', $site_id)->where('page_id', $q_page->id)->where('updated_at', date('Y-m-d H:i:s', $datetime))->where('revision', 1)->first();
		//dd(DB::getQueryLog());
		//dd($frame);
		$frame->delete();


		$return['code'] = 1;
		$return['message'] = 'The revision was removed successfully.';

		echo json_encode($return);
	}

	/**
	 * Restore revision site
	 * @param  Integer $site_id
	 * @param  Integer $datetime
	 * @param  String  $page
	 */
	public function getRevisionRestore($site_id, $datetime, $page)
	{
		if ($site_id == '' || $datetime == '' || $page == '')
		{
			die('Missing data, revision could not be restore.');
		}

		$page = Page::where('site_id', $site_id)->where('name', $page)->first();
		//dd($page);

		// Update current frame as revision
		$page_id = $page->id;

		$frame = Frame::where('site_id', $site_id)->where('page_id', $page_id)->where('revision', 0)->update(['revision' => 1]);

		// Restore revision by recreating the old revision
		$frames = Frame::where('site_id', $site_id)->where('page_id', $page_id)->where('updated_at', date('Y-m-d H:i:s', $datetime))->get();
		//dd($frames);
		foreach ($frames as $frame)
		{
			$new_frame = new Frame();
			$new_frame->page_id = $page_id;
			$new_frame->site_id = $site_id;
			$new_frame->content = $frame->content;
			$new_frame->height = $frame->height;
			$new_frame->original_url = $frame->original_url;
			$new_frame->loaderfunction = $frame->loaderfunction;
			$new_frame->sandbox = $frame->sandbox;
			$new_frame->revision = 0;
			$new_frame->save();
		}

		return redirect()->route('site', [$site_id]);
	}

	/**
	 * Site Settings data update with ajax
	 * @param  Request $request
	 * @return JSON
	 */
	public function postAjaxUpdate(Request $request)
	{
		// Test the FTP connection
		$ftp = new CI_FTP;
		$config = array(
			'hostname' => trim($request->input('siteSettings_ftpServer')),
			'username' => trim($request->input('siteSettings_ftpUser')),
			'password' => trim($request->input('siteSettings_ftpPassword')),
			'port' => trim($request->input('siteSettings_ftpPort')),
			'debug' => FALSE,
			);
		if ($ftp->connect($config))
		{
			$ftpOK = 1;
		}
		else
		{
			$ftpOK = 0;
		}


		// Update site data
		$site = Site::where('id', $request->input('siteID'))->first();
		$site->site_name = trim($request->input('siteSettings_siteName'));
		if($site->user_id=='1'){$site->site_category = trim($request->input('siteSettings_category')); }
		$site->ftp_server = trim($request->input('siteSettings_ftpServer'));
		$site->ftp_user = trim($request->input('siteSettings_ftpUser'));
		$site->ftp_password = trim($request->input('siteSettings_ftpPassword'));
		$site->ftp_path = trim($request->input('siteSettings_ftpPath'));
		$site->ftp_port = trim($request->input('siteSettings_ftpPort'));
		$site->ftp_ok = $ftpOK;
		$site->global_css = trim($request->input('siteSettings_siteCSS'));
		$site->remote_url = trim($request->input('siteSettings_remoteUrl'));
		$site->update();

		// Send success message
		$temp['header'] = 'Yeah! All went well.';
		if ($ftpOK)
		{
			$temp['content'] = 'The site\'s details were saved successfully!';
			$return['ftpOK'] = 1;
		}
		else
		{
			$temp['content'] = 'The site\'s details were saved successfully, <b>however the provided FTP details could not be used to successfully establish a connection; you won\'t be able to publish your site.</b>';
			$return['ftpOK'] = 0;
		}

		$return['responseCode'] = 1;
		$view1 = View('partials.success', array('data' => $temp));
		$return['responseHTML'] = $view1->render();

		// Send back the updated data
		$siteData = Site::where('id', $request->input('siteID'))->get();
		$siteCData = Sitecat::where('status', '1')->get();
		$view2 = View('partials.sitedata', array('data' => $siteData,'cat' => $siteCData));
		$return['responseHTML2'] = $view2->render();

		$return['siteName'] = $request->input('siteSettings_siteName');
		$return['siteID'] = $request->input('siteID');

		// $return['responseCode'] = 1;
		// $return['ftp'] = config('filesystems.disk.ftp.host');
		echo json_encode($return);
	}

	/**
	 * Update page data with ajax call
	 * @param  Request $request
	 * @return JSON
	 */
	public function postUpdatePageData(Request $request)
	{
		// Validation

		// Update page data
		$page = Page::firstOrNew(array('id' => $request->input('pageID')));
		$page->site_id = $request->input('siteID');
		$page->title = $request->input('pageData_title');
		$page->meta_keywords = $request->input('pageData_metaKeywords');
		$page->meta_description = $request->input('pageData_metaDescription');
		$page->header_includes = $request->input('pageData_headerIncludes');
		$page->css = $request->input('pageData_headerCss');
		$page->save();

		// Return page data as well
		// Get page details
		$pages = Page::where('site_id', $request->input('siteID'))->get();
		foreach ($pages as $page)
		{
			$frames = Frame::where('page_id', $page->id)->where('revision', 0)->orderBy('id', 'ASC')->get();
			$pageDetails['blocks'] = $frames;
			$pageDetails['page_id'] = $page->id;
			$pageDetails['pages_title'] = $page->title;
			$pageDetails['meta_description'] = $page->meta_description;
			$pageDetails['meta_keywords'] = $page->meta_keywords;
			$pageDetails['header_includes'] = $page->header_includes;
			$pageDetails['css'] = $page->css;
			$pageFrames[$page->name] = $pageDetails;
		}
		$siteArray['pages'] = $pageFrames;

		if (count($siteArray) > 0)
		{
			$return['siteData'] = $siteArray;

			$pageA = Page::where('site_id', $site_id)->get();
			foreach ($pageA as $pageB)
			{
				$framesA = Frame::where('page_id', $pageB->id)->where('revision', 0)->orderBy('id', 'ASC')->get();
				$pageFrame[$pageB->name] = $pageB;
			}
			$return['pagesData'] = $pageFrame;
		}

		$temp['header'] = 'All set!';
		$temp['content'] = 'The page settings were successfully updated.';

		$return['responseCode'] = 1;
		$view = View('partials.success', array('data' => $temp));
		$return['responseHTML'] = $view->render();

		die(json_encode($return));
	}

	/**
	 * Export Site
	 * @param  Request $request
	 */
	public function postExport(Request $request)
	{
		$user_id = Auth::user()->id;
		//dd($user_id);
		$file_name = Setting::where('name', 'export_fileName')->first();
		$zip = new ZipArchive();
		$zip->open(public_path() . '/tmp/' . $file_name->value, ZipArchive::CREATE);
		$asset_path = Setting::where('name', 'export_pathToAssets')->first();
		$temp = explode('|', $asset_path->value);
		foreach ($temp as $thePath)
		{
			// Create recursive directory iterator
			$files = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator($thePath),
				RecursiveIteratorIterator::LEAVES_ONLY
				);
			foreach ($files as $name => $file)
			{
				if ($file->getFilename() != '.' && $file->getFilename() != '..')
				{
		    		// Get real path for current file
					$filePath = $file->getRealPath();
					$temp = explode("/", $name);
					array_shift( $temp );
					$newName = implode("/", $temp);
					if ($thePath == 'elements/images')
					{
                        // Check if this is a user file
						if (strpos($file,'/uploads') !== false)
						{
							if (strpos($file, '/uploads/' . Auth::user()->id . '/') !== false || Auth::user()->type == 'admin')
							{
                                // Add current file to archive
								$zip->addFile($filePath, $newName);
                                //echo $filePath."<br>";
							}
						}
						else
						{
                            // Add current file to archive
							$zip->addFile($filePath, $newName);
                            //echo $filePath."<br>";
						}
					}
					else
					{
		    		    // Add current file to archive
						$zip->addFile($filePath, $newName);
                        //echo $filePath."<br>";
					}
				}
			}
		}
		//dd($request);
		$theSite = Site::where('id', $request->input('site_id'))->first();
		//dd($theSite);
		foreach ($request->input('pages') as $page => $content)
		{
			$meta = '';
			$pageMeta = Page::where('site_id', $request->input('site_id'))->where('name', $page)->first();
			//dd($pageMeta);
			if ($pageMeta)
			{
				$meta .= '<title>' . $pageMeta->title . '</title>' . "\r\n";
				$meta .= '<meta name="keywords" content="' . $pageMeta->meta_keywords . '">' . "\r\n";
				$meta .= '<meta name="description" content="' . $pageMeta->meta_description . '">' . "\r\n";

				$pageContent = str_replace('<!--pageMeta-->', $meta, $content);

				// Insert header includes;
				$includesPlusCss = '';
				if ($pageMeta->header_includes != '')
				{
					$includesPlusCss .= $pageMeta->header_includes;
				}
				if ($pageMeta->css != '')
				{
					$includesPlusCss .= "\n<style>" . $pageMeta->css . "</style>\n";
				}
				if ($theSite->global_css != '')
				{
					$includesPlusCss .= "\n<style>" . $theSite->global_css . "</style>\n";
				}
				$pageContent = str_replace("<!--headerIncludes-->", $includesPlusCss, $pageContent);
				// Remove frameCovers
				$pageContent = str_replace('<div class="frameCover" data-type="video"></div>', "", $pageContent);
			}
			else
			{
				$pageContent = $content;
			}
			$zip->addFromString($page . ".html", $request->input('doctype') . "\n" . stripslashes($pageContent));
		}
		$zip->close();

		//$yourfile = $this->config->item('export_fileName');
		$file_name_dl = basename($file_name->value);
		//dd($file_name_dl);
		header("Content-Type: application/zip");
		header("Content-Transfer-Encoding: Binary");
		header("Content-Disposition: attachment; filename=$file_name_dl");
		header("Content-Length: " . filesize(public_path() . "/tmp/" . $file_name->value));

		readfile(public_path() . "/tmp/" . $file_name->value);

		unlink(public_path() . '/tmp/' . $file_name->value);

		exit;
	}

	/**
	 * Publish site with ajax call
	 * @param  Request $request
	 * @param  String  $type
	 * @return JSON
	 */
	public function postPublish(Request $request, $type = null)
	{
		$site_id = $request->input('site_id');
		// Get site details
		$siteArray['site'] = Site::where('id', $site_id)->first();
		//dd($siteArray['site']);

		// Get page details
		$pages = Page::where('site_id', $site_id)->get();
		foreach ($pages as $page)
		{
			$frames = Frame::where('page_id', $page->id)->where('revision', 0)->orderBy('id', 'ASC')->get();
			$pageDetails['blocks'] = $frames;
			$pageDetails['page_id'] = $page->id;
			$pageDetails['pages_title'] = $page->title;
			$pageDetails['meta_description'] = $page->meta_description;
			$pageDetails['meta_keywords'] = $page->meta_keywords;
			$pageDetails['header_includes'] = $page->header_includes;
			$pageDetails['css'] = $page->css;
			$pageFrames[$page->name] = $pageDetails;
		}
		$siteArray['pages'] = $pageFrames;

		// Get directory details
		$settings = Setting::where('name', 'elements_dir')->first();
		$siteArray['assetFolders'] = File::directories($settings['value']);

		// Site ok?
		if ($siteArray == false)
		{
			$temp = array();
			$temp['header'] = 'Ouch! Something went wrong:';
			$temp['content'] = 'It appears the site ID is missing OR incorrect. Please refresh your page and try again.';
			$return = array();
			$return['responseCode'] = 0;
			$view = View('partials.error', array('data' => $temp));
			$return['responseHTML'] = $view->render();
			die( json_encode( $return ) );
		}

		// Do we have anythin to publish at all?
		if( ! isset($request->item) || $request->item == '') // Nothing to upload
		{
			$temp = array();
			$temp['header'] = 'Ouch! Something went wrong:';
			$temp['content'] = 'It appears there are no assets selected for publication. Please select the assets you\'d like to publish and try again.';
			$return = array();
			$return['responseCode'] = 0;
			$view = View('partials.error', array('data' => $temp));
			$return['responseHTML'] = $view->render();
			die(json_encode($return));
		}

		// Site info for FTP
		$site = Site::where('id', $site_id)->first();
		$ftp = new CI_FTP;
		$config = array(
			'hostname' => $site->ftp_server,
			'username' => $site->ftp_user,
			'password' => $site->ftp_password,
			'port' => $site->ftp_port,
			);
		if ( ! $ftp->connect($config))
		{
			$temp = array();
			$temp['header'] = 'Ouch! Something went wrong:';
			$temp['content'] = 'It appears there are no assets selected for publication. Please select the assets you\'d like to publish and try again.';
			$return = array();
			$return['responseCode'] = 0;
			$view = View('partials.error', array('data' => $temp));
			$return['responseHTML'] = $view->render();
			die(json_encode($return));
		}
		if (trim($site->ftp_path) == '/')
		{
			$ftp_path = '';
		}
		else
		{
			$ftp_path = trim($site->ftp_path);
		}

		// Uploading files
		if ($type == 'asset') // Asset publishing
		{
			//echo $request->input('item');
			//dd($ftp_path);
			set_time_limit(0); // Prevent timeout
			if ($request->input('item') == 'elements/images')
			{
                // Create the /imaged folder?
				if( ! $ftp->list_files($ftp_path . "/images/"))
				{
					$ftp->mkdir($ftp_path . "/images/");
				}
				$dirMap = directory_map(public_path() . '/elements/images/', 2 );
				//dd($dirMap);

				foreach ($dirMap as $key => $entry)
				{
					if (is_array($entry))
					{
                        // Folder, do all but take special care of /uploads
						if ($key != 'uploads/')
						{
							$ftp->mirror(public_path() . '/elements/images/' . $key, $ftp_path . "/images/" . $key);
						}
                        else // Take special care of the uploads folder
                        {
                        	$user_id = Auth::user()->id;
                        	$uploadsMap = directory_map(public_path() . '/elements/images/uploads/', 1 );

                        	foreach ($uploadsMap as $userIDFolder)
                        	{
                        		if ($userIDFolder == $user_id . "/")
                        		{
                                    // Create the /imaged folder?
                        			if ( ! $ftp->list_files( $ftp_path . "/images/uploads/"))
                        			{
                        				$ftp->mkdir( $ftp_path . "/images/uploads/");
                        			}

                        			$ftp->mirror(public_path() . '/elements/images/uploads/' . $userIDFolder, $ftp_path . "/images/uploads/" . $userIDFolder);
                        		}
                        	}
                        }
                    }
                    else
                    {
                        // File
                    	$sourceFile = public_path() . '/elements/images/' . $entry;
                    	$destinationFile = $ftp_path . "/images/" . $entry;
                        //echo $sourceFile."\n";
                        //echo $_SERVER['DOCUMENT_ROOT'].$sourceFile."\n";
                    	$ftp->upload($sourceFile, $destinationFile);

                    }
                }
            }
            else
            {
            	$item = explode('/', $request->input('item'));
            	$locpath = public_path() . '/' . $request->input('item') . '/';
            	$rempath = $ftp_path . '/' . $item[1] . '/';
            	$st = $ftp->mirror($locpath, $rempath);
            	// echo $locpath . '<br>';
            	// echo $rempath;
            	// dd($st);
            }
        }
		elseif ($type == 'page') // Page publishing
		{
			// Create temp files
			// Check to make sure the /tmp folder is writable
			if( ! is_writable(public_path() . '/tmp/'))
			{
				$temp = array();
				$temp['header'] = 'Ouch! Something went wrong:';
				$temp['content'] = 'It appears there are no assets selected for publication. Please select the assets you\'d like to publish and try again.';
				$return = array();
				$return['responseCode'] = 0;
				$view = View('partials.error', array('data' => $temp));
				$return['responseHTML'] = $view->render();
				die(json_encode($return));
			}

			// Get page meta
			$meta = '';
			$pageMeta = Page::where('site_id', $site_id)->where('name', $request->input('item'))->first();
			if ($pageMeta)
			{
				// Insert title, meta keywords and meta description
				$meta .= '<title>' . $pageMeta->title . '</title>' . "\r\n";
				$meta .= '<meta name="keywords" content="' . $pageMeta->meta_keywords . '">' . "\r\n";
				$meta .= '<meta name="description" content="' . $pageMeta->meta_description . '">';

				$pageContent = str_replace('<!--pageMeta-->', $meta, $request->input('pageContent'));

				// Insert header includes;
				$includesPlusCss = '';
				if ($pageMeta->header_includes != '')
				{
					$includesPlusCss .= $pageMeta->header_includes;
				}
				if ($pageMeta->css != '')
				{
					$includesPlusCss .= "\n<style>" . $pageMeta->css . "</style>\n";
				}
				if ($site->global_css != '')
				{
					$includesPlusCss .= "\n<style>" . $site->global_css . "</style>\n";
				}
				// Insert header includes;
				$pageContent = str_replace("<!--headerIncludes-->", $includesPlusCss, $pageContent);
			}
			else
			{
				$pageContent = $request->input('pageContent');
			}

			if ( ! write_file(public_path() . '/tmp/' . $request->input('item') . ".html", "<!-- DOCTYPE html -->" . $pageContent))
			{
				//echo 'Unable to write the file';
			}
			else
			{
				//echo 'File written!';
			}

			// Upload temp files
			set_time_limit(0);//prevent timeout
			$st = $ftp->mirror(public_path() . '/tmp/', $ftp_path . "/");
			//dd($st);
			// Remove all temp fiels
			File::deleteDirectory(public_path() . '/tmp/', true);
		}

		// All went well
		//$this->sitemodel->published( $_POST['siteID'] );
		$site = Site::where('id', $site_id)->first();
		$site->ftp_published = 1;
		$site->update();

		$return = array();
		$return['responseCode'] = 1;
		die(json_encode($return));
	}

	/**
	 * Trash site with ajax call
	 * @param  Integer $site_id
	 * @return JSON
	 */
	public function getTrash($site_id)
	{
		if ($site_id == '' || $site_id == 'undefined')
		{
			$temp = array();
			$temp['header'] = 'Ouch! Something went wrong:';
			$temp['content'] = 'The site ID is missing or corrupt. Please try reloading the page and then try deleting the site once more.';

			$return = array();
			$return['responseCode'] = 0;
			$view = View('partials.error', array('data' => $temp));
			$return['responseHTML'] = $view->render();

			die( json_encode( $return ) );
		}

		// All good, move to trash
		$site = Site::where('id', $site_id)->first();
		$site->site_trashed = 1;
		$site->update();

		$temp = array();
		$temp['header'] = 'All set!';
		$temp['content'] = 'The site was successfully deleted from the system.';

		$return = array();
		$return['responseCode'] = 1;
		$view = View('partials.success', array('data' => $temp));
		$return['responseHTML'] = $view->render();

		die(json_encode($return));
	}

	/**
	 * List FTP folder content
	 * @param  Request $request
	 * @return JSON
	 */
	public function postFTPConnect(Request $request)
	{
		$ftp = new CI_FTP;
		$config = array(
			'hostname' => trim($request->input('siteSettings_ftpServer')),
			'username' => trim($request->input('siteSettings_ftpUser')),
			'password' => trim($request->input('siteSettings_ftpPassword')),
			'port' => trim($request->input('siteSettings_ftpPort')),
			'debug' => FALSE,
			);
		if ($ftp->connect($config))
		{
			$path = ($request->input('siteSettings_ftpPath')) ? trim($request->input('siteSettings_ftpPath')) : '/';
			$list = $ftp->list_files($path);
			if ($list)
			{
				$temp = array();
				$temp['list'] = $list;
				$temp['data'] = $_POST;

				$return = array();
				$return['responseCode'] = 1;
				$view = View('partials.ftplist', array('data' => $temp));
				$return['responseHTML'] = $view->render();
			}
			else
			{
				$temp = array();
				$temp['header'] = 'Error:';
				$temp['content'] = 'The path you have provided is not correct or you might not have the required permissions to access this path.';

				$return = array();
				$return['responseCode'] = 0;
				$view = View('partials.error', array('data' => $temp));
				$return['responseHTML'] = $view->render();
			}
		}
		else
		{
			$temp = array();
			$temp['header'] = 'Error:';
			$temp['content'] = 'The connection details (server, username, password and/or port) you provided are not correct. Please update the details and try again.';

			$return = array();
			$return['responseCode'] = 0;
			$view = View('partials.error', array('data' => $temp));
			$return['responseHTML'] = $view->render();
		}

		$ftp->close();
		die(json_encode($return));
	}

	/**
	 * Test FTP connection
	 * @param  Request $request
	 * @return JSON
	 */
	public function postFTPTest(Request $request)
	{
		$path = ($request->input('siteSettings_ftpPath')) ? trim($request->input('siteSettings_ftpPath')) : '/';
		$ftp = new CI_FTP;
		$config = array(
			'hostname' => trim($request->input('siteSettings_ftpServer')),
			'username' => trim($request->input('siteSettings_ftpUser')),
			'password' => trim($request->input('siteSettings_ftpPassword')),
			'port' => trim($request->input('siteSettings_ftpPort')),
			'debug' => FALSE,
			);
		if ($ftp->connect($config))
		{
			$list = $ftp->list_files($path);
			if ($list)
			{
				$temp = array();
				$temp['header'] = 'All good!';
				$temp['content'] = 'The provided FTP details are all good and can be used to publish this site.';

				$return = array();
				$return['responseCode'] = 1;
				$view = View('partials.success', array('data' => $temp));
				$return['responseHTML'] = $view->render();
			}
			else
			{
				$temp = array();
				$temp['header'] = 'Error:';
				$temp['content'] = 'The path you have provided is not correct or you might not have the required permissions to access this path.';

				$return = array();
				$return['responseCode'] = 0;
				$view = View('partials.error', array('data' => $temp));
				$return['responseHTML'] = $view->render();
			}
		}
		else
		{
			$temp = array();
			$temp['header'] = 'Error:';
			$temp['content'] = 'The connection details (server, username, password and/or port) you provided are not correct. Please update the details and try again.';

			$return = array();
			$return['responseCode'] = 0;
			$view = View('partials.error', array('data' => $temp));
			$return['responseHTML'] = $view->render();
		}

		$ftp->close();
		die(json_encode($return));
	}

	/**
	 * Test for FTP call
	 */
	public function getTest()
	{
		$ftp = new CI_FTP;
		$config = array(
			'hostname' => 'innovativebd.net',
			'username' => 'latest@innovativebd.info',
			'password' => 'admin123!',
			'port' => 21,
			);
		if ($ftp->connect($config))
		{
			$ftp->mirror(public_path() . '/elements/images/', "/");
			dd($ftp->list_files());
		}
		else
		{
			die("can't connect");
		}

	}
	/**
	 * Create New Site
	 */
	public function getBlog()
	{
		//dd('Thamarai');
		$data="rts";
		return view('auth.email.signup_user', ['data' => $data]);
	}

public function getUserdashboard(Request $request)
	{
	//$data['builder'] = false;
	$data['sess'] = $request->session()->all();
	$data['page'] = 'User Dashboard';
	$wpurl = Commonsetting::where('name', 'home_url')->first();
	$data['wpurl']=$wpurl->value;
	$mtitle = Commonsetting::where('name', 'meta_title')->first();
	//$data['metatitle']=$mtitle->value;
	if (Auth::user()->type != 'admin')
		{
			$data['sites'] = Site::with('user')->where('user_id', Auth::user()->id)->where('site_trashed', 0)->orderBy('id', 'asc')->get()->toArray();
		}
		else
		{
			$data['sites'] = Site::with('user')->where('site_trashed', 0)->orderBy('id', 'asc')->get()->toArray();
		}

	return view('userboard.basic',$data);
	//return view('userboard.basic', ['data' => $data]);
	}
public function getUseriwilldoit(Request $request)
	{
	//$data['builder'] = false;
	//$data['sess'] = $request->session()->all();
	$data['page'] = 'I will do IT';
	$wpurl = Commonsetting::where('name', 'home_url')->first();
	$data['wpurl']=$wpurl->value;
	$mtitle = Commonsetting::where('name', 'meta_title')->first();
	//$data['metatitle']=$mtitle->value;
	if (Auth::user()->type != 'admin')
		{
			$siteData = Site::with('user')->where('user_id', Auth::user()->id)->where('site_trashed', 0)->orderBy('id', 'asc')->get()->toArray();
		}
		else
		{
			$siteData = Site::with('user')->where('site_trashed', 0)->orderBy('id', 'asc')->get()->toArray();
		}

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
	return view('userboard.iwilldoit', array('data' => $data,'sites' => $allSites));
	//return view('userboard.basic', ['data' => $data]);
	}

public function getUserdoitforme(Request $request)
	{
	//$data['builder'] = false;
	//$data['sess'] = $request->session()->all();
	$data['page'] = 'Do IT for me';
	$wpurl = Commonsetting::where('name', 'home_url')->first();
	$data['wpurl']=$wpurl->value;
	$mtitle = Commonsetting::where('name', 'meta_title')->first();
	//$data['metatitle']=$mtitle->value;
	if (Auth::user()->type != 'admin')
		{
			$siteData = Site::with('user')->where('user_id', Auth::user()->id)->where('site_trashed', 0)->orderBy('id', 'asc')->get()->toArray();
		}
		else
		{
			$siteData = Site::with('user')->where('site_trashed', 0)->orderBy('id', 'asc')->get()->toArray();
		}

		//dd($siteData);
		//echo $siteData[0]['user']['email'];

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
		//dd($allSites);
		//$users = User::orderBy('id', 'asc')->get();
	return view('userboard.doitforme', array('data' => $data,'sites' => $allSites));
	//return view('userboard.basic', ['data' => $data]);
	}
public function getUserwebinit(Request $request)
	{
	$data=array();
	//$cstep=$request->get('step');
	//dd($cstep);
	$data['builder'] = false;
	$data['page'] = 'Domain Registration';
	$data['sess'] = $request->session()->all();
	$doit = Commonsetting::where('name', 'doit_init_cost')->first();
	$data['doit_cost']=$doit->value;
	$scost = Commonsetting::where('name', 'need_setup_costs')->first();
	$data['setup_cost']=$scost->value;
	return view('userboard.step1', ['data' => $data]);
	}
public function getUserwebinitsubmit(Request $request)
	{
	Session::set('site_name',$request->input('domain_name'));
	Session::set('site_email',$request->input('domain_email'));
	return redirect()->route('userwebsite.builtby');
	}	
	
	public function getUserwebchoice(Request $request)
	{
	$data=array();
	//$cstep=$request->get('step');
	//dd($cstep);
	$data['builder'] = false;
	$data['page'] = 'Website Build';
	$data['sess'] = $request->session()->all();
	$doit = Commonsetting::where('name', 'doit_init_cost')->first();
	$data['doit_cost']=$doit->value;
	$scost = Commonsetting::where('name', 'need_setup_costs')->first();
	$data['setup_cost']=$scost->value;
	return view('userboard.step2', ['data' => $data]);
	}
	public function getUserwebchoicesubmit(Request $request)
	{
	Session::set('site_name',$request->input('domain_name'));
	Session::set('whodo',$request->input('whodo'));
	if($request->input('whodo')=='iwill'){
		Session::set('url',route('site-create'));
	}
	else
	{
		Session::set('url',route('choosetemplates',['cat_id'=>0]));
	}
	return redirect()->route('userwebsite.getstarted');
	}	
	public function getUserwebgetstarted(Request $request)
	{
	$data=array();
	//$cstep=$request->get('step');
	//dd($cstep);
	$data['builder'] = false;
	$data['page'] = 'Getstart Website';
	$data['sess'] = $request->session()->all();
	$doit = Commonsetting::where('name', 'doit_init_cost')->first();
	$data['doit_cost']=$doit->value;
	$scost = Commonsetting::where('name', 'need_setup_costs')->first();
	$data['setup_cost']=$scost->value;
		return view('userboard.step3', ['data' => $data]);	
	}
	public function getUserpayments(Request $request)
	{
	$data=array();
	$data['builder'] = false;
	$data['page'] = 'Payments';
	$data['sess'] = $request->session()->all();
	$doit = Commonsetting::where('name', 'doit_init_cost')->first();
	$data['doit_cost']=$doit->value;
	$scost = Commonsetting::where('name', 'need_setup_costs')->first();
	$data['setup_cost']=$scost->value;
	//print_r($data);exit;
	return view('userboard.payments', ['data' => $data]);


	}
	public function getUserwebsitedoit(Request $request)
	{
	$data=array();
	$data['builder'] = false;
	$data['page'] = 'User Website';
	$data['sess'] = $request->session()->all();
	$doit = Commonsetting::where('name', 'doit_init_cost')->first();
	$data['doit_cost']=$doit->value;
	$scost = Commonsetting::where('name', 'need_setup_costs')->first();
	$data['setup_cost']=$scost->value;
	//print_r($data);exit;
	return view('userboard.createdoit', ['data' => $data]);


	}
	public function getUserDoforSuccess(Request $request)
	{
	$data=array();
	//$cstep=$request->get('step');
	//dd($cstep);
	$data['builder'] = false;
	$data['page'] = 'Do IT For Me Success';
	$data['sess'] = $request->session()->all();
	$doit = Commonsetting::where('name', 'doit_init_cost')->first();
	$data['doit_cost']=$doit->value;
	$scost = Commonsetting::where('name', 'need_setup_costs')->first();
	$data['setup_cost']=$scost->value;
		return view('userboard.doforsuccess', ['data' => $data]);
	}

	public function getUsersites()
	{
	$data['builder'] = false;
	$data['page'] = 'User Website';

	if (Auth::user()->type != 'admin')
		{
			$siteData = Site::with('user')->where('user_id', Auth::user()->id)->where('site_trashed', 0)->orderBy('id', 'asc')->get()->toArray();
		}
		else
		{
			$siteData = Site::with('user')->where('site_trashed', 0)->orderBy('id', 'asc')->get()->toArray();
		}

		//dd($siteData);
		//echo $siteData[0]['user']['email'];

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
		//dd($allSites);
		$users = User::orderBy('id', 'asc')->get();

		//dd($allSites);
		return view('userboard.basicsites', array('sites' => $allSites, 'users' => $users));


	}
public function getMywebsites()
	{
	$data['builder'] = false;
	$data['page'] = 'My Websites';
	return view('userboard.mysites', ['data' => $data]);
	}
public function getUserdomain()
	{
	$data['page'] = 'User Website';
	return view('userboard.domainpurchase', ['data' => $data]);
	}
	public function getPayments()
	{
	$data['page'] = 'User Payments';
	//Subscriptions
	$data['user_sub']=DB::table('subscribeacts as po')->leftJoin('payments as br', 'br.id', ' = ', 'po.pay_id')->leftJoin('subscriptions as si', 'si.id', ' = ', 'po.sub_id')->leftJoin('users as ur', 'ur.id', ' = ', 'po.user_id')->where('po.user_id',Auth::user()->id)->select('si.name','br.stripeToken','br.amount','br.date_time','br.status')->get();
	//SItebuilder
	$data['user_sites']=DB::table('sites as po')->leftJoin('payments as br', 'br.id', ' = ', 'po.payid')->leftJoin('users as ur', 'ur.id', ' = ', 'po.user_id')->where('po.user_id',Auth::user()->id)->where('po.payid', '!=' , '')->select('po.site_name','br.stripeToken','br.amount','br.date_time','br.status')->get();
	
	return view('userboard.mypayments', ['data' => $data,'sum' => '0']);
	}
public function postUpdateDomainData(Request $request)
	{
		//print_r($_FILES);exit;
		if($_POST['whodo']=='iwill'){ 
			Session::set('new_site', $_POST['domainurl']);
			$return='iwill';
		}else{

			$return='youwill';
		}
		
		echo json_encode($return);
	}
public function postUpdateDomainDataFiles(Request $request)
	{
		//print_r($_FILES);exit;
		dd($request->all()); 
		//Get File from Request   
    $file = \Illuminate\Support\Facades\Request::file('excel');
    dd($file);
    exit;
		if($request->hasFile('excel')) {
                $file = $request->file('excel');

                //$name = $request['phone_number'].$request['phone_number'].'.'.$file->getClientOriginalExtension();
                $path = $request->photo->store('public/src/uploads');
                $image['filePath'] = $path;
                $file->move('uploads', $name);

            }
          print_r($file);  
		if($_POST['whodo']=='iwill'){ 
			Session::set('new_site', $_POST['domainurl']);
			$return='iwill';
		}else{

			$return='youwill';
		}
		
		echo json_encode($return);
	}
public function anySitedatax()
	{
		$people=DB::table('sites as po')->leftJoin('payments as br', 'br.id', ' = ', 'po.payid')->leftJoin('users as ur', 'ur.id', ' = ', 'po.user_id')->where('po.create_category', '!=' , 'youwill')->select('po.id','po.site_name','po.ftp_published','po.publish_date','po.created_at','br.status','ur.first_name','ur.last_name','ur.email');
		return Datatables::of($people)->escapeColumns()->make(true);
	}
public function anySitedoitdatax()
	{
		//$people=DB::table('sites as po')->leftJoin('payments as br', 'br.id', ' = ', 'po.payid')->leftJoin('users as ur', 'ur.id', ' = ', 'po.user_id')->where('po.create_category', 'youwill')->select('po.id','po.site_name','po.doc_name','po.ftp_published','po.publish_date','po.created_at','br.status','ur.first_name','ur.last_name','ur.email')->orderBy('po.id', 'desc');
		$people=DB::table('siteforms as po')->leftJoin('payments as br', 'br.id', ' = ', 'po.payid')->leftJoin('sites as si', 'si.id', ' = ', 'po.site_id')->leftJoin('users as ur', 'ur.id', ' = ', 'po.user_id')->leftJoin('sitecats as sc', 'sc.id', ' = ', 'po.site_category')->select('po.id','po.site_category','po.site_id','po.new_site_id','po.site_name','po.created_at','si.site_name as orgsitename','br.status','ur.first_name','ur.last_name','ur.email','sc.name');
		return Datatables::of($people)->escapeColumns()->make(true);
	}
public function anyPaymentdatax()
	{
		$people=DB::table('sites as po')->leftJoin('payments as br', 'br.id', ' = ', 'po.payid')->leftJoin('users as ur', 'ur.id', ' = ', 'po.user_id')->groupBy('br.id')->select('po.id','po.site_name','po.ftp_published','po.publish_date','po.created_at','br.stripeToken','br.amount','br.date_time','br.status','ur.first_name','ur.last_name','ur.email');
		return Datatables::of($people)->escapeColumns()->make(true);
	}
public function MyDoITformeDatax()
	{
		$people=DB::table('siteforms as po')->leftJoin('payments as br', 'br.id', ' = ', 'po.payid')->leftJoin('sites as si', 'si.id', ' = ', 'po.site_id')->leftJoin('users as ur', 'ur.id', ' = ', 'po.user_id')->where('po.user_id',Auth::user()->id)->groupBy('po.id')->select('po.id','po.site_id','po.new_site_id','po.site_name','po.created_at','si.site_name as templatename','br.stripeToken','br.amount','br.date_time','br.status');
		return Datatables::of($people)->escapeColumns()->make(true);
	}
public function getTemplates(Request $request,$cat_id) {
	// Get site data
		//dd($request->get()->all());

		$siteCat = Sitecat::where('status', 1)->orderBy('name', 'asc')->get();
		if($cat_id=='all' || $cat_id=='0'){
		$siteData = Site::with('user')->where('site_trashed', 0)->where('site_category', '!=' , '')->orderBy('id', 'asc')->get()->toArray();
		}
		else
		{
		$siteData = Site::with('user')->where('site_trashed', 0)->where('site_category', $cat_id)->orderBy('id', 'asc')->get()->toArray();	
		}

		//dd($siteData);
		//echo $siteData[0]['user']['email'];

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
		//dd($allSites);
		//$users = User::orderBy('id', 'asc')->get();
		$data['page']='Templates';
		//dd($allSites);
		return view('userboard.basicsites', array('data'=>$data,'sites' => $allSites, 'cats' => $siteCat));
	}

public function getUserSiteforminfo(Request $request)
	{
		//print_r($_FILES);exit;
		$pid=$request->input('itemid'); 
		$people=DB::table('siteforms as po')->leftJoin('payments as br', 'br.id', ' = ', 'po.payid')->leftJoin('users as ur', 'ur.id', ' = ', 'po.user_id')->leftJoin('sitecats as sc', 'sc.id', ' = ', 'po.site_category')->where('po.id',$pid)->select('po.*','br.status','ur.first_name','ur.last_name','ur.email','sc.name')->orderBy('po.id', 'desc')->get();
		echo json_encode($people);
	}

	public function getCreateSite(Request $request,$doc_id)
	{
		//dd($doc_id);
		$docData = Siteform::where('id', $doc_id)->orderBy('id', 'asc')->first();
		$site_id=$docData['site_id'];	

		
		$site = Site::where('id', $site_id)->get();
		foreach ($site as $sit)
		{
			$sdata['user_id']=$docData['user_id'];
			$sdata['site_name']=$docData['site_name'];
			$sdata['payid']=$docData['payid'];
			$sdata['create_category']='youwill';
			$sdata['domain_email']='';
			$sdata['site_category']='';
			$sdata['doc_name']='';
			$sdata['global_css']=$sit->global_css;
			$sdata['remote_url']=$sit->remote_url;
			$sdata['site_trashed']=$sit->site_trashed;

			$clone_site_id=$this->createclonesite($sdata);
		}
		
		$pages = Page::where('site_id', $site_id)->get();
		foreach ($pages as $page)
		{
			$pdata['site_id']=$clone_site_id;
			$pdata['name']=$page->name;
			$pdata['title']=$page->title;
			$pdata['meta_keywords']=$page->meta_keywords;
			$pdata['meta_description']=$page->meta_description;
			$pdata['header_includes']=$page->header_includes;
			$pdata['preview']=$page->preview;
			$pdata['template']=$page->template;
			$pdata['css']=$page->css;

			$clone_page_id=$this->createclonepages($pdata);

				$frames = Frame::where('page_id', $page->id)->where('revision', 0)->orderBy('id', 'ASC')->get();
				foreach ($frames as $frame)
				{
					$fdata['page_id']=$clone_page_id;
					$fdata['site_id']=$clone_site_id;
					$fdata['content']=$frame->content;
					$fdata['height']=$frame->height;
					$fdata['original_url']=$frame->original_url;
					$fdata['loaderfunction']=$frame->loaderfunction;
					$fdata['sandbox']=$frame->sandbox;
					$fdata['revision']=$frame->revision;

					$clone_frame_id=$this->createcloneframes($fdata);

				}
		}
		$updata=array('new_site_id'=>$clone_site_id);
        Siteform::where('id', $doc_id)->update($updata);
		return redirect()->route('site', ['site_id' => $clone_site_id]);
	}

	public function createclonesite($data){
		$site= new Site;
    	foreach ($data as $tmp => $val) {
    			$site->$tmp = $val;
    		}
    			$site->save();
    		return $site->id;
	}
	public function createclonepages($data){
	$site= new Page;
	foreach ($data as $tmp => $val) {
			$site->$tmp = $val;
		}
			$site->save();
		return $site->id;
}
	public function createcloneframes($data){
	$site= new Frame;
	foreach ($data as $tmp => $val) {
			$site->$tmp = $val;
		}
			$site->save();
		return $site->id;
}
public function getPricing(Request $request){
	$data['page'] = 'Packages';
	$data['sess'] = $request->session()->all();
	$data['packages'] = Subscription::get()->toArray();
	$data['user_sub']=DB::table('subscribeacts as po')->leftJoin('payments as br', 'br.id', ' = ', 'po.pay_id')->leftJoin('subscriptions as si', 'si.id', ' = ', 'po.sub_id')->leftJoin('users as ur', 'ur.id', ' = ', 'po.user_id')->where('po.user_id',Auth::user()->id)->where('po.exdate_time', '>=', date('Y-m-d'))->select('po.id','po.exdate_time','si.name','si.about','br.stripeToken','br.amount','br.date_time','br.status')->get();
	return view('userboard.pricing', ['data' => $data]);
	}
public function getemplatecats(Request $request){
	$data['page'] = 'Template Category';
	$data['sess'] = $request->session()->all();
	$data['packages'] = Sitecat::get()->toArray();
	return view('userboard.templatecats', ['data' => $data]);
	}	
public function postemplatecats(Request $request)
    {
        //dd($request->all());

   $isvalid=$this->validate($request, [
            'site_name' => 'required|max:180'
            ]);
    $pay = new Sitecat();
    
    $pay->name = $request->get('site_name');
    $pay->status = 1;
    $pay->save();
    return redirect()->route('templates');
    }
  public function getEmailjobs(Request $request){
	$data['page'] = 'Mail Jobs';
	$data['sess'] = $request->session()->all();
	$doit = Commonsetting::where('name', 'newuser_mailjob')->first();
	$data['newuser']=$doit->value;
	$scost = Commonsetting::where('name', 'pending_payment_mailjob')->first();
	$data['pendingpay']=$scost->value;
	
	return view('userboard.mailjobs', ['data' => $data]);
	}  	
public function postEmailjobs(Request $request)
	{
		//print_r($_FILES);exit;
		if($_POST['ref']=='1'){ 
			$today=date('Y-m-d');
        	$users = DB::table('users')
            ->leftJoin('sites', 'sites.user_id', '=', 'users.id')
            ->where('users.id', '!=', [1,7])
            ->whereNull('sites.user_id')
            ->select('users.*')
            ->get();
         dd($users);   
        foreach($users as $usr){
            //Finding difference day from user create date upto today
            $tmp = explode(' ', $usr->updated_at);
            $datediff = strtotime($today) - strtotime($tmp[0]);
            $daydiff=floor($datediff / (60 * 60 * 24));
            //Finding difference day from last send mail upto today
            if($usr->email_notify_date!=''){$umday=$usr->email_notify_date;}else{$umday=$today;}
            $emdiff = strtotime($today) - strtotime($umday);
            $mdiff=floor($emdiff / (60 * 60 * 24));
            //dd($mdiff);
            if($usr->email_notification=='on' && $mdiff > 7){
           // dd($daydiff);
            $mdata = array('from'=>'owlclick@gmail.com','aname'=>'Administrator','name'=>$usr->first_name, 'email'=>$usr->email,'site'=>$usr->site_name);
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

			$return='1';
		}else{

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
            
            $mdata = array('from'=>'owlclick@gmail.com','aname'=>'SMBDIY Team','name'=>$usr->first_name.' '.$usr->last_name, 'email'=>$usr->email,'site'=>$usr->site_name);
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

			$return='2';
		}
		
		echo json_encode($return);
	}	
}