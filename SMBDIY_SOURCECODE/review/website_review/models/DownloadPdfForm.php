<?php
class DownloadPdfForm extends CFormModel {
	public $validation;

	public function rules() {
		$config=Utils::loadConfig('recaptcha');
		return array(
            array('validation', 'ext.recaptcha2.ReCaptcha2Validator', 'privateKey'=>$config['private-key'], 'message'=>Yii::t("app", "Please confirm you're not a robot"))
		);
	}

	public function attributeLabels() {
		return array(
			'validation'=>Yii::t('app', "Please confirm you're not a robot"),
		);
	}
}