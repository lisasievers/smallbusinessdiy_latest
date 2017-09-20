<?php
class ParseController extends Controller {

	public function filters() {
		return !Yii::app() -> params["instantRedir"] ? array('ajaxOnly + index') : array();
	}

	public function actionIndex() {
		$model = new WebsiteForm();
		$this -> performValidation($model);
	}

	protected function performValidation($model) {
		if(isset($_GET['Website']) AND is_array($_GET['Website'])) {
			$model -> attributes = $_GET['Website'];
			if(!$model->validate()) {
				echo json_encode($model -> getErrors());
			} else {
				$url = $this->createUrl("websitestat/generateHTML", array("domain" => $model -> domain));
				if(!isset($_GET['redirect'])) {
					echo json_encode($url);
				} else {
					$this -> redirect($url);
				}
			}
		}
	}

    public function actionPagespeed() {
        Yii::import('application.vendors.Webmaster.Google.*');

        $domain = Yii::app()->request->getQuery('domain');
        $website = Yii::app()->db->createCommand()
            ->select("*")
            ->from("{{website}}")
            ->where("md5domain=:md5domain", array(
                ":md5domain"=>md5($domain)
            ))
            ->queryRow();
        if(!$website) {
            throw new CHttpException(404, Yii::t("app", "The page you are looking for doesn't exists"));
        }
        $wid = $website['id'];

        if($results = $this->getPageSpeedResults($wid)) {
            $this->jsonResponse(array(
                "content"=>$this->renderPartial("//websitestat/pagespeed_web", array(
                    "results"=>$results,
                    "website"=>$website,
                ), true)
            ));
        }

        $lang_id = Yii::app()->language;

        $p = new PageSpeedInsights($domain, Yii::app()->params['googleApiKey']);
        $p->setLocale(Yii::app()->language);
        $results = $p->getResults();

        try {
            $sql = "INSERT INTO {{pagespeed}} (wid, data, lang_id) VALUES (:wid, :data, :lang_id) ON DUPLICATE KEY UPDATE data=:data";
            $command = Yii::app()->db->createCommand($sql);
            $command->bindParam(":wid", $wid);
            $command->bindParam(":data", @json_encode($results));
            $command->bindParam(":lang_id", $lang_id);
            $command->execute();
            $this->jsonResponse(array(
                "content"=>$this->renderPartial("//websitestat/pagespeed_web", array(
                    "results"=>$results,
                    "website"=>$website,
                ), true)
            ));
        } catch(Exception $e) {
            $this->jsonResponse(array(
                "error"=>Yii::t("app", "Error Code 101")
            ));
        }
    }

    protected function getPageSpeedResults($wid) {
        $results = Yii::app()->db->createCommand()->select("data")->from("{{pagespeed}}")->where("wid=:wid AND lang_id=:lang_id", array(":wid"=>$wid, ":lang_id"=>Yii::app()->language))->queryScalar();
        return @json_decode($results, true);
    }
}