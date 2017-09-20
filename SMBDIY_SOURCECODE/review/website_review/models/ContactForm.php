<?php
class ContactForm extends CFormModel {
	public $name;
	public $email;
	public $subject;
	public $body;
	public $verifyCode;

	public function rules() {
		return array(
			array('name, email, subject, body', 'required'),
			array('email', 'email'),
			array('verifyCode', 'captcha', 'allowEmpty' => !CCaptcha::checkRequirements()),
		);
	}

	public function attributeLabels() {
		return array(
			'verifyCode'=>Yii::t("app", "Verification Code"),
			'name' => Yii::t("app", "name"),
			'email' => Yii::t("app", "email"),
			'subject' => Yii::t("app", "subject"),
			'body' => Yii::t("app", "body"),
		);
	}
}