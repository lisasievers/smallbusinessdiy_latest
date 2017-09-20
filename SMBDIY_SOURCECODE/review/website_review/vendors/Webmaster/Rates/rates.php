<?php
define("_RATE_OK", "success");
define("_RATE_WARNING", "warning");
define("_RATE_ERROR", "error");
define("_RATE_OK_IDEAL", "success ideal_ratio");
define("_RATE_ERROR_LESSTHAN", "error less_than");
define("_RATE_ERROR_MORETHAN", "error more_than");

define("_RATE_CSS_COUNT", 4);
define("_RATE_JS_COUNT", 6);

define("_RATE_TITLE_BAD", 0);
define("_RATE_TITLE_GOOD", 10);
define("_RATE_TITLE_BEST", 70);

define("_RATE_DESC_BAD", 0);
define("_RATE_DESC_GOOD", 70);
define("_RATE_DESC_BEST", 160);

define("_RATE_HRATIO_BAD", 15);
define("_RATE_HRATIO_GOOD", 25);
define("_RATE_HRATIO_BEST", 70);

/*
The Website Review is a dynamic grade on a 100-point scale.
This mean that the sum of shown bellow points can't be more than 100.

So, how points are added? Let's take a look on the first key=>value pair
'noFlash' => 2,
This mean, that if website do not have flash content then he will get +2 points to current score and etc.
===========================
Let's analyse pairs containing arrays. For example: 'title' => array(),
if the $title length == 0, then website receives 0 points,
if length > 0 and < 10 -> 2 points
and etc
===========================
At the bottom of this config file you will see 'wordConsistency' key.
'wordConsistency' => array(
	'keywords' => 0.5,
	'description' => 1,
	'title' => 1,
	'headings' => 1,
),
To calculate the total sum of this checkpoint you need to multiply each value by {N} and sum them.
Where {N} -> is 'consistencyCount' => value in main cnofig (config/main.php)
By default {N} equals 5, so
(0.5 * 5) + (1 * 5) + (1 * 5) + (1 * 5) = 17.5
17.5 - the maximum points, which website can be get at this checkpoint.

Advice. Be careful if you want to change the rates.
*/
return array(
	'noFlash' => 2,
	'noIframe' => 2,
	'issetHeadings' => 1,
	'noNestedtables' => 1.5,
	'noInlineCSS' => 2,
	'noEmail' => 1.5,
	'issetFavicon' => 1.5,
	'imgHasAlt' => 2,
	'isFriendlyUrl' => 3.5,
	'noUnderScore' => 3.5,
	'issetInternalLinks' => 1.5,
    'hasRobotsTxt'=>1.5,
    'hasSitemap'=>2,
    'hasGzip'=>1.5,
    'hasAnalytics'=>1,
    // 28

	'title' => array(
		'$value == _RATE_TITLE_BAD' => array(
			'score' => 0,
			'advice' => _RATE_ERROR,
		),
		'$value > _RATE_TITLE_BAD and $value < _RATE_TITLE_GOOD' => array(
			'score' => 2,
			'advice' => _RATE_WARNING,
		),
		'$value >= _RATE_TITLE_GOOD and $value <= _RATE_TITLE_BEST' => array(
			'score' => 4,
			'advice' => _RATE_OK,
		),
		'$value > _RATE_TITLE_BEST' => array(
			'score' => 2,
			'advice' => _RATE_WARNING,
		),
	),

	'keywords' => 2,
	'description' => array(
		'$value == _RATE_DESC_BAD' => array(
			'score' => 0,
			'advice' => _RATE_ERROR,
		),
		'$value > _RATE_DESC_BAD and $value < _RATE_DESC_GOOD' => array(
			'score' => 2,
			'advice' => _RATE_WARNING,
		),
		'$value >= _RATE_DESC_GOOD and $value <= _RATE_DESC_BEST' => array(
			'score' => 4,
			'advice' => _RATE_OK,
		),
		'$value > _RATE_DESC_BEST' => array(
			'score' => 2,
			'advice' => _RATE_WARNING,
		),
	),

	'charset' => 2.5,
	'viewport' => 1.5,
	'dublincore' => 1,
	'ogmetaproperties' => 2.5,
    // 45,5


	'htmlratio' => array(
		'$value < _RATE_HRATIO_BAD' => array(
			'score' => 2,
			'advice' => _RATE_ERROR_LESSTHAN,
		),
		'$value >= _RATE_HRATIO_BAD and $value < _RATE_HRATIO_GOOD' => array(
			'score' => 7.5,
			'advice' => _RATE_OK,
		),
		'$value >= _RATE_HRATIO_GOOD and $value <= _RATE_HRATIO_BEST' => array(
			'score' => 9,
			'advice' => _RATE_OK_IDEAL,
		),
		'$value > _RATE_HRATIO_BEST' => array(
			'score' => 0,
			'advice' => _RATE_ERROR_MORETHAN,
		),
	),

	'w3c' => 11,
	'doctype' => 2,
	'isPrintable' => 1.5,
	'issetAppleIcons' => 2,
	'noDeprecated' => 1.5,
	'lang' => 2,
    // 74,5

	'cssCount' => array(
		'$value <= _RATE_CSS_COUNT' => array(
			'score' => 4,
			'advice' => _RATE_OK,
		),
		'$value > _RATE_CSS_COUNT' => array(
			'score' => 1.5,
			'advice' => _RATE_ERROR,
		),
	),

	'jsCount' => array(
		'$value <= _RATE_JS_COUNT' => array(
			'score' => 4,
			'advice' => _RATE_OK,
		),
		'$value > _RATE_JS_COUNT' => array(
			'score' => 1.5,
			'advice' => _RATE_ERROR,
		),
	),
    // 82,5

	'wordConsistency' => array(
		'keywords' => 0.5,
		'description' => 1,
		'title' => 1,
		'headings' => 1,
	),
    // 100
);