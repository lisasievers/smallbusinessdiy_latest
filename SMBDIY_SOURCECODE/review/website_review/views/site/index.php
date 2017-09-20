<div class="jumbotron">
<h1>Review Your Website</h1>
<!--<p class="lead"><?php //echo Yii::t("app", "Marketing speak - header", array("{Brandname}" => Yii::app() -> name)) ?></p>-->
<br/>
<?php  echo $this->renderPartial("//site/request_form", array(
	"errorClass"=>"noFloat"
)); ?>
</div>

<hr>

<div class="row-fluid marketing" style="display:none">
<div class="span6" style="display:none">
<div class="media">
<div class="media-body">
<h4 class="media-heading"><?php echo Yii::t("app", "Content analysis") ?></h4>
<img class="media-object marketing-img pull-left" src="<?php echo Yii::app() -> getBaseUrl(true) ?>/img/content.png" alt="<?php echo Yii::t("app", "Content analysis") ?>" />
<?php echo Yii::t("app", "Marketing speak - content") ?>
</div>
</div>

<br/>

<div class="media">
<div class="media-body">
<h4 class="media-heading"><?php echo Yii::t("app", "Meta Tags") ?></h4>
<img class="media-object marketing-img pull-left" src="<?php echo Yii::app() -> getBaseUrl(true) ?>/img/tags.png" alt="<?php echo Yii::t("app", "Meta Tags") ?>" />
<?php echo Yii::t("app", "Marketing speak - metatags") ?>
</div>
</div>

<br/>

<div class="media">
<div class="media-body">
<h4 class="media-heading"><?php echo Yii::t("app", "Link Extractor") ?></h4>
<img class="media-object marketing-img pull-left" src="<?php echo Yii::app() -> getBaseUrl(true) ?>/img/link.png" alt="<?php echo Yii::t("app", "Link Extractor") ?>" />
<?php echo Yii::t("app", "Marketing speak - links") ?>
</div>
</div>

</div>

<div class="span6"  style="display:none">

<div class="media">
<div class="media-body">
<h4 class="media-heading"><?php echo Yii::t("app", "Speed Test") ?></h4>
<img class="media-object marketing-img pull-left" src="<?php echo Yii::app() -> getBaseUrl(true) ?>/img/speed.png" alt="<?php echo Yii::t("app", "Speed Test") ?>" />
<?php echo Yii::t("app", "Marketing speak - speed test") ?>
</div>
</div>

<br/>

<div class="media">
<div class="media-body">
<h4 class="media-heading"><?php echo Yii::t("app", "Get Advice") ?></h4>
<img class="media-object marketing-img pull-left" src="<?php echo Yii::app() -> getBaseUrl(true) ?>/img/advice.png" alt="<?php echo Yii::t("app", "Get Advice") ?>" />
<?php echo Yii::t("app", "Marketing speak - advice") ?>
</div>
</div>

<br/>

<div class="media">
<div class="media-body">
<h4 class="media-heading"><?php echo Yii::t("app", "Website Review") ?></h4>
<img class="media-object marketing-img pull-left" src="<?php echo Yii::app() -> getBaseUrl(true) ?>/img/review.png" alt="<?php echo Yii::t("app", "Website Review") ?>" />
<?php echo Yii::t("app", "Marketing speak - review") ?>
</div>
</div>

</div>

</div>


<h3><?php echo Yii::t("app", "Your Latest Reviews") ?></h3><br>
<?php echo $widget ?>