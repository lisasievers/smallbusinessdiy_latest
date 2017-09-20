<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Login registration section route
/*
Route::get('/', function (Request $request) {
	if($request()->cookie('emailid')){
	return redirect()->route('userdashboard');
	}
	else{ 
	return view('auth.login');
	}
})->name('home');
//Route::get('/', 'SiteController@getUserdashboard');
*/
Route::get('/', function () {
	return view('auth.login');
})->name('home');


Route::get('/signin', function () {
	if(request()->cookie('emailid')){
	return redirect()->route('userdashboard');
	}
	else{ 
	return view('auth.login');
	}
})->name('home');
Route::get('/login', function () {
	if(request()->cookie('emailid')){
	return redirect()->route('userdashboard');
	}
	else{ 
	return view('auth.login');
	}
})->name('home');

Route::get('/forgot-password', function () {
	return view('auth.forgot_password');
})->name('forgot.password');

Route::post('/recover-password', [
	'uses' => 'UserController@postRecoverPassword',
	'as' => 'recover.password'
	]);

Route::get('/user-pw-reset-email/{user_id}', [
	'uses' => 'UserController@getPasswordResetEmail',
	'as' => 'user-pw-reset-email'
	]);

Route::get('/user-pw-reset-code/{code}', [
	'uses' => 'UserController@getPasswordResetCode',
	'as' => 'user-pw-reset-code'
	]);

Route::post('/user-pw-reset', [
	'uses' => 'UserController@postPasswordReset',
	'as' => 'user-pw-reset'
	]);

Route::get('/signup',[	
	'uses' => 'UserController@getsignup',
	'as' => 'signup'
	]);

Route::post('/signup', [
	'uses' => 'UserController@postSignUp',
	'as' => 'signup'
	]);

Route::post('/signin', [
	'uses' => 'UserController@postSignIn',
	'as' => 'signin'
	]);

Route::get('/logout', [
	'uses' => 'UserController@getLogout',
	'as' => 'logout'
	]);
Route::group(array('prefix' => 'api'), function() {
    Route::resource('lisapi','APIController');
});
Route::get('/apireviewuser', [
	'uses' => 'APIController@reviewuser',
	'as' => 'apireviewuser'
	]);
// Dashboard section
Route::get('/dashboard', [
	'uses' => 'SiteController@getDashboard',
	'as' => 'dashboard',
	'middleware' => 'auth'
	]);

Route::get('/site/trash/{site_id}', [
	'uses' => 'SiteController@getTrash',
	'as' => 'site.trash',
	'middleware' => 'auth'
	]);

// Site section route
Route::get('/site-create', [
	'uses' => 'SiteController@getSiteCreate',
	'as' => 'site-create',
	'middleware' => 'auth'
	]);

Route::get('/site/{site_id}', [
	'uses' => 'SiteController@getSite',
	'as' => 'site',
	'middleware' => 'auth'
	]);

Route::post('/site/save', [
	'uses' => 'SiteController@postSave',
	'as' => 'site-save',
	'middleware' => 'auth'
	]);

Route::get('/site/getframe/{frame_id}', [
	'uses' => 'SiteController@getFrame',
	'as' => 'getframe',
	'middleware' => 'auth'
	]);

Route::get('/siteData', [
	'uses' => 'SiteController@getSiteData',
	'as' => 'siteData',
	'middleware' => 'auth'
	]);

Route::get('/siteAjax/{site_id}', [
	'uses' => 'SiteController@getSiteAjax',
	'as' => 'siteAjax',
	'middleware' => 'auth'
	]);

// Revision section route
Route::get('/site/getRevisions/{site_id}/{page}', [
	'uses' => 'SiteController@getRevisions',
	'as' => 'getRevisions',
	'middleware' => 'auth'
	]);

Route::get('/site/rpreview/{site_id}/{datetime}/{page}', [
	'uses' => 'SiteController@getRevisionPreview',
	'as' => 'revision.preview',
	'middleware' => 'auth'
	]);

Route::get('/deleterevision/{site_id}/{datetime}/{page}', [
	'uses' => 'SiteController@getRevisionDelete',
	'as' => 'revision.delete',
	'middleware' => 'auth'
	]);

Route::get('/restorerevision/{site_id}/{datetime}/{page}', [
	'uses' => 'SiteController@getRevisionRestore',
	'as' => 'revision.restore',
	'middleware' => 'auth'
	]);

// Publish and export section
Route::post('/site/export', [
	'uses' => 'SiteController@postExport',
	'as' => 'site.export',
	'middleware' => 'auth'
	]);

Route::post('/site/publish/{type?}', [
	'uses' => 'SiteController@postPublish',
	'as' => 'site.publish',
	'middleware' => 'auth'
	]);

// FTP section route
Route::post('/site/connect', [
	'uses' => 'SiteController@postFTPConnect',
	'as' => 'ftp.connect',
	'middleware' => 'auth'
	]);

Route::post('/site/ftptest', [
	'uses' => 'SiteController@postFTPTest',
	'as' => 'ftp.test',
	'middleware' => 'auth'
	]);

Route::get('/test', [
	'uses' => 'SiteController@getTest',
	'as' => 'site.test',
	'middleware' => 'auth'
	]);

// Live preview route
Route::post('/site/live/preview', [
	'uses' => 'SiteController@postLivePreview',
	'as' => 'live.preview',
	'middleware' => 'auth'
	]);

// Site and Page settings
Route::post('/siteAjaxUpdate', [
	'uses' => 'SiteController@postAjaxUpdate',
	'as' => 'siteAjaxUpdate',
	'middleware' => 'auth'
	]);

Route::post('/updatePageData', [
	'uses' => 'SiteController@postUpdatePageData',
	'as' => 'updatePageData',
	'middleware' => 'auth'
	]);

// User section route
Route::get('/users', [
	'uses' => 'UserController@getUserList',
	'as' => 'users',
	'middleware' => 'auth'
	]);

Route::post('/user-create', [
	'uses' => 'UserController@postUserCreate',
	'as' => 'user-create',
	'middleware' => 'auth'
	]);

Route::post('/user-update', [
	'uses' => 'UserController@postUserUpdate',
	'as' => 'user-update',
	'middleware' => 'auth'
	]);

Route::get('/user-delete/{user_id}', [
	'uses' => 'UserController@getUserDelete',
	'as' => 'user-delete',
	'middleware' => 'auth'
	]);

Route::get('/user-enable-disable/{user_id}', [
	'uses' => 'UserController@getUserEnableDisable',
	'as' => 'user-enable-disable',
	'middleware' => 'auth'
	]);

Route::post('/user/uaccount', [
	'uses' => 'UserController@postUAccount',
	'as' => 'user.uaccount',
	'middleware' => 'auth'
	]);

Route::post('/user/ulogin', [
	'uses' => 'UserController@postULogin',
	'as' => 'user.ulogin',
	'middleware' => 'auth'
	]);

// Settings section route
Route::get('/settings', [
	'uses' => 'SettingController@getSetting',
	'as' => 'settings',
	'middleware' => 'auth'
	]);

Route::post('/edit-settings', [
	'uses' => 'SettingController@postSetting',
	'as' => 'edit-settings',
	'middleware' => 'auth'
	]);

// Image Library section route
Route::get('/assets', [
	'uses' => 'AssetController@getAsset',
	'as' => 'assets',
	'middleware' => 'auth'
	]);

Route::post('/upload-image', [
	'uses' => 'AssetController@uploadImage',
	'as' => 'upload.image',
	'middleware' => 'auth'
	]);

Route::post('/image-upload-ajax', [
	'uses' => 'AssetController@imageUploadAjax',
	'as' => 'image.upload.ajax',
	'middleware' => 'auth'
	]);

Route::post('/assets/delImage', [
	'uses' => 'AssetController@delImage',
	'as' => 'delImage',
	'middleware' => 'auth'
	]);
Route::get('/blog', [
	'uses' => 'SiteController@getBlog',
	'as' => 'blog'
	//return 'http://localhost/blog'
	]);
Route::get('/userdashboard', [
	'uses' => 'SiteController@getUserdashboard',
	'as' => 'userdashboard',
	'middleware' => 'auth'
	]);
Route::get('/userwebsite/domain', [
	'uses' => 'SiteController@getUserwebinit',
	'as' => 'userwebsite.domain',	
	'middleware' => 'auth'
	]); 
Route::post('/userwebsite/domain', [
	'uses' => 'SiteController@getUserwebinitsubmit',
	'as' => 'userwebsite.domain.submit',	
	'middleware' => 'auth'
	]); 
Route::get('/userwebsite/builtby', [
	'uses' => 'SiteController@getUserwebchoice',
	'as' => 'userwebsite.builtby',	
	'middleware' => 'auth'
	]);
Route::post('/userwebsite/builtby', [
	'uses' => 'SiteController@getUserwebchoicesubmit',
	'as' => 'userwebsite.builtby.submit',	
	'middleware' => 'auth'
	]);
Route::get('/userwebsite/getstarted', [
	'uses' => 'SiteController@getUserwebgetstarted',
	'as' => 'userwebsite.getstarted',	
	'middleware' => 'auth'
	]); 
Route::get('/choosetemplates/{cat_id}', [
	'uses' => 'SiteController@getTemplates',
	'as' => 'choosetemplates',
	'middleware' => 'auth'
	]);
Route::get('/webdataform', [
'uses' => 'AssetController@getuploadDoitforme',
	'as' => 'webdataform',
	'middleware' => 'auth'
	]);
Route::post('/webdataform', [
'uses' => 'AssetController@uploadDoitforme',
	'as' => 'webdataform',
	'middleware' => 'auth'
	]);
Route::get('/getwebdataform/{cat_id}', [
'uses' => 'AssetController@getDoitformeForm',
	'as' => 'getwebdataform',
	'middleware' => 'auth'
	]);
Route::post('/postwebdataform', [
'uses' => 'AssetController@postDoitformeForm',
	'as' => 'postwebdataform',
	'middleware' => 'auth'
	]);
Route::get('/viewwebdata/{cat_id}', [
'uses' => 'AssetController@viewDoitformeForm',
	'as' => 'viewwebdata',
	'middleware' => 'auth'
	]);
Route::get('/mysites', [
'uses' => 'SiteController@getMywebsites',
	'as' => 'mysites',
	'middleware' => 'auth'
	]);
Route::get('/usersites', [
	'uses' => 'SiteController@getUsersites',
	'as' => 'usersites',
	'middleware' => 'auth'
	]);
Route::get('/pricing', [
	'uses' => 'SiteController@getPricing',
	'as' => 'pricing',
	'middleware' => 'auth'
	]);
Route::get('/userwebsitedoit', [
	'uses' => 'SiteController@getUserwebsitedoit',
	'as' => 'userwebsitedoit',
	'middleware' => 'auth'
	]);

Route::get('/iwilldoituser', [
	'uses' => 'SiteController@getUseriwilldoit',
	'as' => 'iwilldoituser',
	'middleware' => 'auth'
	]);
Route::get('/doitforuser', [
	'uses' => 'SiteController@getUserdoitforme',
	'as' => 'doitforuser',
	'middleware' => 'auth'
	]);
Route::get('/payments', [
	'uses' => 'SiteController@getUserpayments',
	'as' => 'payments',
	'middleware' => 'auth'
	]);
Route::get('/domainpurchase', [
	'uses' => 'SiteController@getUserdomain',
	'as' => 'domainpurchase'
	]);
Route::post('/updateDomainData', [
	'uses' => 'SiteController@postUpdateDomainData',
	'as' => 'updateDomainData',
	'middleware' => 'auth'
	]);

Route::post('/updateDomainDataFiles', [
	'uses' => 'SiteController@postUpdateDomainDataFiles',
	'as' => 'updateDomainDataFiles',
	'middleware' => 'auth'
	]);
Route::get('/user/reports', [
	'uses' => 'ReportController@getUsereportsHome',
	'as' => 'user.reportshome',	
	'middleware' => 'auth'
	]);
Route::post('/user/reports', [
	'uses' => 'ReportController@postUserShowreports',
	'as' => 'user.reportshome',	
	'middleware' => 'auth'
	]);
Route::get('/user/addsites', [
	'uses' => 'ReportController@getAddsites',
	'as' => 'user.addsites',	
	'middleware' => 'auth'
	]);
Route::post('/user/addsites', [
	'uses' => 'ReportController@postAddsites',
	'as' => 'user.addsites',	
	'middleware' => 'auth'
	]);
Route::get('/user/payments', [
	'uses' => 'SiteController@getPayments',
	'as' => 'user.payments',	
	'middleware' => 'auth'
	]);
/*
Route::get('/user/showreports', [
	'uses' => 'ReportController@getUserShowreports',
	'as' => 'user.showreports',	
	'middleware' => 'auth'
	]);*/
Route::get('/user/freereports', [
	'uses' => 'ReportController@getUserFreereports',
	'as' => 'user.freereports',	
	'middleware' => 'auth'
	]);
Route::get('/user/paidreports', [
	'uses' => 'ReportController@getUserPaidreports',
	'as' => 'user.paidreports',	
	'middleware' => 'auth'
	]);

Route::get('/user/qrcodegeneration', [
	'uses' => 'ReportController@getQrcode',
	'as' => 'user.qrcode.generation',	
	'middleware' => 'auth'
	]);

Route::get('/user/gmobiletest', [
	'uses' => 'ReportController@getMobiletest',
	'as' => 'user.gmobiletest',	
	'middleware' => 'auth'
	]);
Route::post('/user/gmobiletest', [
	'uses' => 'ReportController@postMobiletest',
	'as' => 'user.gmobiletest',	
	'middleware' => 'auth'
	]);
Route::get('/user/settings', [
	'uses' => 'UserController@getUserSettings',
	'as' => 'user.settings',	
	'middleware' => 'auth'
	]);
Route::post('/user/settings', [
	'uses' => 'UserController@postUserSettings',
	'as' => 'user.settings',	
	'middleware' => 'auth'
	]);
Route::get('/user/addwebsite/{sub_id}', [
	'uses' => 'AddMoneyController@getUserAddwebsite',
	'as' => 'user-reports-addition',	
	'middleware' => 'auth'
	]);
Route::post('/user/addwebsite', [
	'uses' => 'AddMoneyController@postUserAddwebsiteSubmit',
	'as' => 'user-package-addition',	
	'middleware' => 'auth'
	]);
Route::get('/facebook', 'SocialAuthController@redirectToFacebook');
Route::get('/facebook/callback', 'SocialAuthController@handleFacebookCallback');

Route::get('/addmoney/stripe/{site_id}', array('as' => 'addmoney.paywithstripe','uses' => 'AddMoneyController@payWithStripe',));
Route::post('/addmoney/stripe', array('as' => 'addmoney.stripe','uses' => 'AddMoneyController@postPaymentWithStripe',));

Route::get('/paymentodo/{site_id}', array('as' => 'paymentodo','uses' => 'AddMoneyController@payWithDoitforme',));
Route::post('/paymentodo', array('as' => 'paymentodo.raise','uses' => 'AddMoneyController@postPaymentWithDoitforme',));
Route::get('/user/success', [
	'uses' => 'SiteController@getUserDoforSuccess',
	'as' => 'success_site',	
	'middleware' => 'auth'
	]);
Route::post('/siteforminfo', [
	'uses' => 'SiteController@getUserSiteforminfo',
	'as' => 'siteforminfo',	
	'middleware' => 'auth'
	]);
Route::any('sitedatax', [
	'uses' => 'SiteController@anySitedatax',
	'as' => 'sitedatax',
	'middleware' => 'auth'
	]);
Route::any('doitsitedatax', [
	'uses' => 'SiteController@anySitedoitdatax',
	'as' => 'doitsitedatax',
	'middleware' => 'auth'
	]);
Route::any('paymentdatax', [
	'uses' => 'SiteController@anyPaymentdatax',
	'as' => 'paymentdatax',
	'middleware' => 'auth'
	]);
Route::any('MyDoITformeDatax', [
	'uses' => 'SiteController@MyDoITformeDatax',
	'as' => 'MyDoITformeDatax',
	'middleware' => 'auth'
	]);
Route::any('MyPaymentDatax', [
	'uses' => 'SiteController@MyPaymentDatax',
	'as' => 'MyPaymentDatax',
	'middleware' => 'auth'
	]);
/*
Route::post('/upload-document', [
	'uses' => 'AssetController@uploadDoitforme',
	'as' => 'upload.document',
	'middleware' => 'auth'
	]);
*/
Route::get('/createsite/{site_id}', [
	'uses' => 'SiteController@getCreateSite',
	'as' => 'createsite',
	'middleware' => 'auth'
	]);
Route::get('/mailjob', [
	'uses' => 'SiteController@getEmailjobs',
	'as' => 'mailjob',
	'middleware' => 'auth'
	]);
Route::post('/mailjob', [
	'uses' => 'SiteController@postEmailjobs',
	'as' => 'mailjob',
	'middleware' => 'auth'
	]);
Route::get('/templates', [
	'uses' => 'SiteController@getemplatecats',
	'as' => 'templates',
	'middleware' => 'auth'
	]);
Route::post('/templates', [
	'uses' => 'SiteController@postemplatecats',
	'as' => 'templates',
	'middleware' => 'auth'
	]);