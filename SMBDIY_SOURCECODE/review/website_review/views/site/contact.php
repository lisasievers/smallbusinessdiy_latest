<h1><?php echo $this -> title ?></h1>

<?php
$flashMessages = Yii::app()->user->getFlashes();
if ($flashMessages):foreach($flashMessages as $key => $message):?>
<div class="alert alert-<?php echo $key?>">
<?php echo $message ?>
</div>
<?php endforeach; endif; ?>

<p><?php echo Yii::t("app", "Contact page text") ?></p>

<br/>

<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'stateful' => true,
)); ?>

<p class="note"><?php echo Yii::t("app", "Fields with * are required") ?></p>

<?php echo $form->errorSummary($model); ?>

<div class="control-group">
<?php echo $form->labelEx($model, 'name'); ?>
<?php echo $form->textField($model, 'name'); ?>
</div>

<div class="control-group" style="float:left !important; margin-right:20px; !important">
<?php echo $form->labelEx($model, 'email'); ?>
<?php echo $form->textField($model, 'email'); ?>
</div>

<div class="control-group">
<?php echo $form->labelEx($model, 'subject'); ?>
<?php echo $form->textField($model, 'subject', array('size'=>60,'maxlength'=>128)); ?>
</div>

<div class="control-group">
<?php echo $form->labelEx($model, 'body'); ?>
<?php echo $form->textArea($model, 'body', array('rows'=>6, 'cols'=>72)); ?>
</div>

<?php if(CCaptcha::checkRequirements()): ?>
<div class="control-group">
<?php echo $form->labelEx($model, 'verifyCode', array('class'=>'control-label')); ?>
<div class="controls">
<?php $this->widget('CCaptcha'); ?>
<span class="help-inline">
<?php echo $form->textField($model, 'verifyCode'); ?>
</span>
</div>
</div>


<div class="control-group submit">
<?php echo CHtml::submitButton(Yii::t("app", "Submit"), array(
	'class' => 'btn btn-large btn-primary',
)); ?>
</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php endif; ?>