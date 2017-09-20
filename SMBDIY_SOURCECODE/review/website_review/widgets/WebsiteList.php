<?php

/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 2015.11.22
 * Time: 15:55
 */
class WebsiteList extends CWidget
{
    public $config = array();
    public $template="website_list";

    public function init() {
		if(isset(Yii::app()->request->cookies['user_id']->value)){$userid=Yii::app()->request->cookies['user_id']->value;}else{$userid=0;}
		
        $config = array(
            "criteria"=>array(
				"condition"=>"t.user_id=$userid",
                "order"=>"t.added DESC",
            ),
            "countCriteria"=>array(),
            "pagination" => array(
                "pageVar"=>"page",
                "pageSize"=>Yii::app()->params['webPerPage'],
            )
        );
        $this->config = CMap::mergeArray($config, $this->config);
    }

    public function run() {
        $dataProvider=new CActiveDataProvider('Website', $this->config);
        $data=$dataProvider->getData();
        if(empty($data)) {
            return null;
        }
        $thumbnailStack=WebsiteThumbnail::thumbnailStack($data);
        $this->render($this->template, array(
            "dataProvider" => $dataProvider,
            "thumbnailStack"=>$thumbnailStack,
            "data"=>$data,
        ));
    }
}