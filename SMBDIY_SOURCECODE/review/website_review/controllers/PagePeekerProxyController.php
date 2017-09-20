<?php
class PagePeekerProxyController extends Controller {
    public function actionIndex() {
        if(!Yii::app()->params['useProxyImage']) {
            throw new CHttpException(404, Yii::t("app", "The page you are looking for doesn't exists"));
        }
        if(!$url = Yii::app()->request->getQuery('url')) {
            throw new CHttpException(404, Yii::t("app", "The page you are looking for doesn't exists"));
        }
        $response = Utils::curl($url);
        $this->jsonResponse(@json_decode($response));
        /*$this->jsonResponse(array(
            'IsReady'=>0,
        ));*/
    }
} 