<?php
class SiteController extends Controller
{
	public function actions() {
		return array(
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
		);
	}

	public function actionIndex() {
		$this -> title = Yii::t("meta", "Index page title", array("{Brandname}" => Yii::app() -> name));

		Yii::app() -> clientScript -> registerMetaTag(Yii::t("meta", "Index page keywords"), "keywords");
		Yii::app() -> clientScript -> registerMetaTag(Yii::t("meta", "Index page description", array("{Brandname}" => Yii::app() -> name)), "description");

		Yii::app() -> clientScript -> registerMetaTag(Yii::t("meta", "Og property title", array("{Brandname}" => Yii::app() -> name)), null, null, array('property'=>'og:title'));
		Yii::app() -> clientScript -> registerMetaTag(Yii::t("meta", "Og property description", array("{Brandname}" => Yii::app() -> name)), null, null, array('property'=>'og:description'));
		Yii::app() -> clientScript -> registerMetaTag(Yii::app() -> name, null, null, array('property'=>'og:site_name'));
		Yii::app() -> clientScript -> registerMetaTag(Yii::app() -> getBaseUrl(true).'/img/logo.png', null, null, array('property'=>'og:image'));

        $widget = $this->widget('application.widgets.WebsiteList', array(
            'config'=>array(
                "totalItemCount"=>Yii::app()->params['websitesOnIndexPage'],
                "pagination"=>array(
                    "pageSize"=>Yii::app()->params['websitesOnIndexPage']
                ),
            ),
        ), true);

		$this->render('index', array(
            'widget'=>$widget,
        ));
	}

	public function actionError() {
		if($error=Yii::app()->errorHandler->error) {
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	public function actionContact()
    {
        $this->title = Yii::t("meta", "Contact page title");
        Yii::app()->clientScript->registerMetaTag(Yii::t("meta", "Contact page keywords"), "keywords");
        Yii::app()->clientScript->registerMetaTag(Yii::t("meta", "Contact page description"), "description");


        $model = new ContactForm;
        if (isset($_POST['ContactForm'])) {
            $model->attributes = $_POST['ContactForm'];
            if ($model->validate()) {
                try {
                    $mail = Yii::createComponent('application.extensions.mailer.EMailer');
                    $mail->IsSMTP();
                    $mail->Host = Yii::app()->params['mailer']['Host'];
                    //$mail->SMTPDebug = 2;
                    $mail->SMTPAuth = Yii::app()->params['mailer']['SMTPAuth'];
                    $mail->Port = Yii::app()->params['mailer']['Port'];
                    $mail->Username = Yii::app()->params['mailer']['Username'];
                    $mail->Password = Yii::app()->params['mailer']['Password'];
                    $mail->CharSet = Yii::app()->params['mailer']['CharSet'];
                    $mail->SMTPSecure = Yii::app()->params['mailer']['SMTPSecure'];
                    $mail->SMTPOptions = Yii::app()->params['mailer']['SMTPOptions'];
                    $mail->SetFrom(Yii::app()->params['mailer']['Username'], Yii::app()->name);
                    $mail->ClearReplyTos();
                    $mail->AddReplyTo($model->email, $model->name);
                    $mail->Subject = $model->subject . " : " . Yii::app()->name;
                    $mail->AddAddress(Yii::app()->params["adminEmail"]);
                    $mail->MsgHTML($model->body);
                    if ($mail->Send()) {
                        Yii::app()->user->setFlash('success', Yii::t('app', 'Thank you for your email'));
                    } else {
                        Yii::app()->user->setFlash('error', Yii::t('app', 'Server temporary unvailable'));
                    }
                } catch (phpmailerException $e) {
                    Yii::app()->user->setFlash('error', Yii::t('app', 'Server temporary unvailable'));
                    Yii::log($e->getMessage(), 'error', 'application.site.contact');
                } catch (Exception $e) {
                    Yii::app()->user->setFlash('error', Yii::t('app', 'Server temporary unvailable'));
                    Yii::log($e->getMessage(), 'error', 'application.site.contact');
                }
                $this->refresh();
            }
        }
        $this->render('contact', array('model' => $model));
    }
}