<?php
class LanguageSelector extends CWidget {
	public function run() {
		$currentLang = Yii::app() -> language;
		$languages = Yii::app() -> params['languages'];
        if(count($languages) < 2 OR Yii::app()->errorHandler->error) {
            return null;
        }
		$this -> render ("languageSelector", array(
			'currentLang' => $currentLang,
			'languages' => $languages,
		));
	}
}