<?php
class Website extends CActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return Yii::app()->db->tablePrefix.'website';
	}

    public function total() {
        return $this->cache(60*60*5)->count();
    }
}