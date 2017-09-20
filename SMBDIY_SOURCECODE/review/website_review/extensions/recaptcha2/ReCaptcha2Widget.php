<?php
class ReCaptcha2Widget extends CInputWidget {
    /**
     * @var string ReCaptcha Public Key
     */
    public $siteKey;

    /**
     * @var string App language
     */
    public $lang;

    /**
     * @var array Options for recaptcha render function
     */
    public $widgetOpts = array();

    /**
     * @var CClientScript
     */
    private $cs;

    /**
     * @var string API url
     */
    const API_URL = "https://www.google.com/recaptcha/api.js?hl={lang}&onload={functionName}&render=explicit";

    /**
     * @var array Stack of recaptcha widgets
     */
    private static $stack = array();

    /**
     * @var string md5 unique name.
     */
    private static $uhash;

    /**
     * @var string widget id
     */
    protected $widgetId;

    public function init() {
        parent::init();
        $this->setStackHash();
        $this->widgetId = $this->generateUniqueId();
        $this->cs = Yii::app()->clientScript;
        self::$stack[] = $this;
        $this->registerAPIScript();
        $this->registerCaptchaRenderFunction();
    }

    public function run() {
        $body = '';
        if ($this->hasModel()) {
            $body = CHtml::activeHiddenField($this->model, $this->attribute, array(
                "id"=>$this->getFieldId(),
            )) . "\n";
        }
        echo $body.'<div id="'.$this->getCaptchaId().'"></div>';
    }

    protected function registerAPIScript() {
        $url = strtr(self::API_URL, array(
            "{lang}"=>$this->getLanguageSuffix(),
            "{functionName}"=>$this->getOnLoadFuncName(),
        ));
        $this->cs->registerScriptFile($url, CClientScript::POS_END, array("async"=>"async", "defer"=>"defer", "encode"=>false));
    }

    protected function registerCaptchaRenderFunction() {
        $widgetStack = "";
        $onLoadFuncName = $this->getOnLoadFuncName();

        /**
         * @var $widget $this
         */
        foreach(self::$stack as $widget) {
            $fieldId = $widget->getFieldId();

            $wOpts = CMap::mergeArray(array(
                "theme"=>"light",
                "type"=>"image",
                "size"=>"normal",
                "tabindex"=>0,
                "callback"=>"",
                "expired-callback"=>"",
            ), $widget->widgetOpts);

            if(!empty($wOpts['callback'])) {
                $callback = "{$wOpts['callback']}('".$fieldId."', response);";
            } else {
                $callback = null;
            }

            if(!empty($wOpts['expired-callback'])) {
                $expiredCallback = "'expired-callback' : function() {
                    {$wOpts['expired-callback']}('".$fieldId."');
                }";
            } else {
                $expiredCallback = null;
            }

            $opts = "{
                'sitekey' : '".$widget->siteKey."',
                'theme' : '".$wOpts['theme']."',
                'type' : '".$wOpts['type']."',
                'size' : '".$wOpts['size']."',
                'tabindex' : '".$wOpts['tabindex']."',
                'callback' : function(response) {
                    document.getElementById('".$fieldId."').value = response;
                    $callback
                },
                $expiredCallback
            }";

            $widgetStack .= "grecaptcha.render('{$widget->getCaptchaId()}', {$opts});\n";
        }
        $this->cs->registerScript("renderReCaptcha2", "
            var {$onLoadFuncName} = function() {
                {$widgetStack}
            };
        ", CClientScript::POS_HEAD);
    }

    protected function getLanguageSuffix() {
        $currentLanguage = !empty($this->lang) ? $this->lang : Yii::app()->language;
        $langsExceptions = array('zh-CN', 'zh-TW', 'zh-TW');
        if (strpos($currentLanguage, '-') === false) {
            return $currentLanguage;
        }
        if (in_array($currentLanguage, $langsExceptions)) {
            return $currentLanguage;
        } else {
            return substr($currentLanguage, 0, strpos($currentLanguage, '-'));
        }
    }

    protected static function generateUniqueId() {
        return md5(mt_rand(0, 1000).microtime(true).uniqid());
    }

    protected static function setStackHash() {
        if(empty(self::$uhash)) {
            self::$uhash = self::generateUniqueId();
        }
    }

    protected function getFieldId() {
        return "recaptcha_hidden_".$this->widgetId;
    }

    protected function getCaptchaId() {
        return "recaptcha_".$this->widgetId;
    }

    protected static function getOnLoadFuncName() {
        return "captcha_loaded_".self::$uhash;
    }
}