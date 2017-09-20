<?php
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	// Brand name. Your app name (Will not be translated)
	'name'=>'Website Review',
	// Default app lang
	'language'=>'en',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
	),

	'modules'=>array(),

	// application components
	'components'=>array(
		// Url Manager
        'urlManager'=>array(
            'urlFormat'=>'path',
            'showScriptName'=>false,
            'class'=>'application.components.UrlManager',
            'cacheID'=>'cache',
            'rules'=>array(
                'proxy'=>'PagePeekerProxy/index',
                '<language:\w{2}>' => 'site/index',
                '<language:\w{2}>/contact' => 'site/contact',
                '<language:\w{2}>/rating/page/<page:\w+>' => 'rating/index',
                '<language:\w{2}>/www/<domain:[\w\d\-\.]+>' => 'websitestat/generateHTML',
                '<language:\w{2}>/pdf-review/<domain:[\w\d\-\.]+>.pdf' => 'websitestat/generatePDF',
                '<language:\w{2}>/<controller:\w+>' => '<controller>/index',
            ),
		),

		// File Cache. ~/root/website_review/runtime/cache direcotry
		'cache'=>array(
			'class'=>'CFileCache',
		),

		// Databse Settings
		'db'=>array(
			// Mysql with host: localhost and databse name website_review
			'connectionString' => 'mysql:host=localhost;dbname=sitebuilder',
			// whether to turn on prepare emulation
			'emulatePrepare' => true,
			// db username
			'username' => 'root',
			// db password
			'password' => 'root#@123',
			// default cahrset
			'charset' => 'utf8',
			// table prefix
			'tablePrefix' => 'ca_',
			// cache time to dicrease SHOW CREATE TABLE * queries
			'schemaCachingDuration' => 60 * 60 * 24 * 30,
		),

		// Error handler
		'errorHandler'=>array(
			// ControllerID/ActionID custom page to handle errors
			'errorAction'=>'site/error',
		),

		// Log errors into ~/root/website_review/runtime/application.log file
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
                    'except'=>'exception.CHttpException.*',
				),
                /*array(
                    'class'=>'CWebLogRoute',
                ),*/
			),
		),
	),

	// App level params
	'params'=>array(
		// project custom variables
		'project_url'=>'https://smallbusinessdiy.com',
		// your email which will receive messages from contact form
		'adminEmail'=>'admin@my-mail.com',
		// Website count in "Rating" section
		'webPerPage'=>6,
        // Website count on Index page
        'websitesOnIndexPage'=>6,
		// Available app languages
		'languages'=>array(
			'da'=>'Dansk',
			'de'=>'Deutsch',
			'en'=>'English',
            'es'=>'Español',
			'fr'=>'Français',
			'it'=>'Italiano',
			'nl'=>'Nederlands',
            'pt'=>'Português',
			'ru'=>'Русский',
            'sv'=>'Svenska',
		),
        // Whether to communicate with pagepeeker
        'useProxyImage' => false,
        // Google api key
        'googleApiKey'=>'AIzaSyCKpDdcoCz_JopMLNO4Os6XMfK9J_jl_MI',
		// Whether to show cookie disclaimer
		'showCookieDisclaimer'=>false,
        // Whether to download all reviews in one PDF
        'partialPdf'=>true,
		// Allow instant redirect
		'instantRedir' => false,
		// Check website for badwords
		'checkForBadwords' => true,
		// Addthis.com javascript source
		'addthis'=>'',
		// Mailer extension
		'mailer' => array(
			'SMTPAuth' => true,
			// smtp server's port
			'Port' => 587,
			// server's host
			'Host' => 'smtp.gmail.com',
			// username
			'Username' => 'owlclick@gmail.com',
			// password
			'Password' => '2o11owl&^',
			// letter's charset
			'CharSet' => 'UTF-8',
            // set "tls" if Port is 587 or "ssl" if Port is 465
            'SMTPSecure'=>'',
            // SMTP cconnection options
            'SMTPOptions' => array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            ),
		),
		// Analyser extension
		'analyser' => array(
			// Cache time in seconds.
			// For example if the website was added on 2013:06:15 12:40:11, then the
			// user will be able to update information after {cacheTime} seconds
			'cacheTime' => 60 * 60 * 24,
			// Total words in tag cloud
			'tagCloud' => 10,
			// Using 5 most consistency words from tagCloud to generate consistency matrix.
			/** !WARNING **/ // This value is closely related to the score calculation. See
			// ~/root/website_review/vendors/Webmaster/Rating/rates.php file
			'consistencyCount' => 5,
		),
		'thumbs'=>array(
			0 => array(
				'{{Protocol}}://free.pagepeeker.com/v2/thumbs.php?size={{Size}}&url={{Url}}',
				'{{Size}}'=>'m',
				'{{Protocol}}'=>'http',
			),
			1 => array(
				'{{Protocol}}://free2.pagepeeker.com/v2/thumbs.php?size={{Size}}&url={{Url}}',
				'{{Size}}'=>'m',
				'{{Protocol}}'=>'http',
			),
			2 => array(
				'{{Protocol}}://free3.pagepeeker.com/v2/thumbs.php?size={{Size}}&url={{Url}}',
				'{{Size}}'=>'m',
				'{{Protocol}}'=>'http',
			),
			3 => array(
				'{{Protocol}}://free4.pagepeeker.com/v2/thumbs.php?size={{Size}}&url={{Url}}',
				'{{Size}}'=>'m',
				'{{Protocol}}'=>'http',
			),
			/*
			0=>array(
				'http://immediatenet.com/t/l?Size=1024x768&URL={{Url}}',
			),
			*/
		),
	),
);