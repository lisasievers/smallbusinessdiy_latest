<li class="dropdown">
<a class="dropdown-toggle" id="drop5" role="button" data-toggle="dropdown" href="#"><?php echo Yii::t("app", "Language") ?> <b class="caret"></b></a>
<ul id="menu3" class="dropdown-menu" role="menu" aria-labelledby="drop5">
<?php foreach($languages as $key => $language):
$params['language'] =  $key;
if(isset($_GET['page'])) {
	$params['page'] = (int)$_GET['page'];
}
if(isset($_GET['domain'])) {
	$params['domain'] = $_GET['domain'];
}
?>
<?php if($key == Yii::app() -> language) continue; ?>
<li role="presentation">
<?php echo CHtml::link($language, $this->getOwner()->createAbsoluteUrl('', $params)) ?>
<?php Yii::app() -> clientScript -> registerLinkTag(
'alternate', null, $this -> getOwner() -> createAbsoluteUrl('', $params), null, array(
	'hreflang' => $key,
)); ?>
</li>
<?php endforeach; ?>
<li role="presentation" class="divider"></li>
<li role="presentation" class="disabled"><a tabindex="-1"><?php echo $languages[$currentLang] ?></a></li>
</ul>
</li>