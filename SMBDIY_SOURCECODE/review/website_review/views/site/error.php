<h2><?php echo 404 == $code ? Yii::t("app", "Page not found") : Yii::t("app", "Error {ErrorNo}", array("{ErrorNo}" => $code)); ?></h2>
<div class="error">
<?php echo CHtml::encode($message); ?>
</div>