<?php
class RatingController extends Controller {

	public function actionIndex() {
		$this -> title = Yii::t("meta", "Rating page title", array(
			'{Page}' => isset($_GET['page']) ? Yii::t("app", "|page") : null,
			'{PageNr}' => isset($_GET['page']) ? (int)$_GET['page'] : null,
		));

		Yii::app() -> clientScript -> registerMetaTag(Yii::t("meta", "Rating page keywords"), "keywords");
		Yii::app() -> clientScript -> registerMetaTag(Yii::t("meta", "Rating page description"), "description");

        $widget = $this->widget('application.widgets.WebsiteList', array(
            'config'=>array(
                "totalItemCount"=>Website::model()->total(),
                'criteria'=>array(
                    'order'=>'t.score DESC',
                ),
            ),
        ), true);

		$this->render('index',array(
			'widget' => $widget,
		));
	}

}