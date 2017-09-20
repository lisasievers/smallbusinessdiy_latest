<?php

class ReCaptcha2Validator extends CValidator {
    /**
     * @var string verification URL
     */
    const SITE_VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * @var boolean Whether to skip this validator if the input is empty.
     */
    public $skipOnEmpty = false;

    /**
     * The private key for reCAPTCHA
     *
     * @var string
     */
    private $privateKey='';

    /**
     * Sets the private key.
     *
     * @param string $value
     * @throws CException if $value is not valid.
     */
    public function setPrivateKey($value) {
        if (empty($value)||!is_string($value)) throw new CException(Yii::t('yii','ReCaptchaValidator.privateKey must contain your reCAPTCHA private key.'));
        $this->privateKey = $value;
    }

    /**
     * Returns the reCAPTCHA private key
     *
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    public function validateAttribute($model, $attribute) {
        $response = $this->getResponse($model->$attribute);
        if (!isset($response['success'])) {
            throw new Exception('Invalid recaptcha verify response.');
        }
        if(!$response['success']) {
            $message = $this->message !== null ? $this->message : Yii::t('yii','The verification code is incorrect.');
            $this->addError($model, $attribute, $message);
        }
    }


    protected function getResponse($response) {
        $peer_key = version_compare(PHP_VERSION, '5.6.0', '<') ? 'CN_name' : 'peer_name';
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query(array(
                    "secret"=>$this->getPrivateKey(),
                    "response"=>$response,
                    "remoteip"=>Yii::app()->request->userHostAddress
                ), null, "&"),
                // Force the peer to validate (not needed in 5.6.0+, but still works
                'verify_peer' => true,
                // Force the peer validation to use www.google.com
                $peer_key => 'www.google.com',
            ),
        );
        $context = stream_context_create($options);
        return @json_decode(file_get_contents(self::SITE_VERIFY_URL, false, $context), true);
    }

}