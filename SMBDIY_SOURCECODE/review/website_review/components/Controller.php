<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	public $title;

	public function init() {
		parent::init();
		$this->setupLanguage();
		$this->registerJsGlobalVars();
		$this->registerCookieDisclaimer();
	}

	protected function registerJsGlobalVars() {
		$baseUrl = Yii::app()->request->getBaseUrl(true);
		Yii::app()->clientScript->registerScript('globalVars', "
			var _global = {
				baseUrl: '{$baseUrl}',
				proxyImage: ". (int) Yii::app()->params['useProxyImage'] ."
			};
		", CClientScript::POS_HEAD);
	}

	protected function setupLanguage() {
		$langs = Yii::app() -> params['languages'];
		if (isset($_GET['language']) && isset($langs[$_GET['language']])) {
			$lang = $_GET['language'];
			Yii::app()->user->setState('language', $lang);
			$cookie = new CHttpCookie('language', $lang);
			$cookie->expire = time() + (60 * 60 * 24 * 365); // (1 year)
			Yii::app()->request->cookies['language'] = $cookie;
		}
		elseif (isset(Yii::app()->request->cookies['language']))
			$lang = Yii::app()->request->cookies['language']->value;
		else
			$lang = mb_substr(Yii::app()->getRequest()->getPreferredLanguage(), 0, 2);

		if(!isset($langs[$lang])) {
			$lang = Yii::app() -> language;
		}
		Yii::app() -> language = $lang;
	}

	protected function registerCookieDisclaimer() {
		if(Yii::app()->request->isAjaxRequest OR !isset(Yii::app()->params['showCookieDisclaimer']) OR (Yii::app()->params['showCookieDisclaimer'] === false)) {
			return true;
		}

		/**
		 * @var $cs CClientScript
		 */
		$cs = Yii::app()->clientScript;
		$cs->registerScriptFile(Yii::app()->request->getBaseUrl(true)."/js/cookieconsent.latest.min.js", CClientScript::POS_END);
		$cs->registerScript("cookieconsent", "
			window.cookieconsent_options = {
				learnMore: ".CJavaScript::encode(Yii::t("app", "Learn more")).",
				dismiss: ". CJavaScript::encode(Yii::t("app", "OK")).",
				message: ". CJavaScript::encode(Yii::t("app", "This website uses cookies to ensure you get the best experience on our website.")).",
				theme:'dark-top',
				link: 'http://www.google.com/intl/".Yii::app()->language."/policies/privacy/partners/'
			};
		", CClientScript::POS_HEAD);
	}

    public function jsonResponse($response) {
        header('Content-type: application/json');
        echo json_encode($response);
        Yii::app() -> end();
    }
}