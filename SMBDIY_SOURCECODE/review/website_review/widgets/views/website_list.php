<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 2015.11.22
 * Time: 16:01
 *
 * @var $thumbnailStack array
 * @var $dataProvider CActiveDataProvider
 * @var $data array
 */
?>

<script type="text/javascript">
$(document).ready(function(){
    var urls = {
        <?php foreach($thumbnailStack as $id=>$url): ?>
        <?php echo $id ?>:'<?php echo $url ?>',
        <?php endforeach; ?>
    };
    dynamicThumbnail(urls);
});
</script>

<ul class="thumbnails" style="text-align:center">
    <?php foreach($data as $website):
        $url = $this->controller->createAbsoluteUrl("websitestat/generateHTML", array("domain" => $website -> domain));
        ?>
        <li class="span3">
            <div class="thumbnail">
                <h4 align="center"><?php echo Utils::cropDomain($website -> idn). '<br>'; ?></h4>
                <img class="thumbnail rating_ico" id="thumb_<?php echo $website -> id ?>" src="<?php echo Yii::app() -> getBaseUrl(true) ?>/img/loader.gif" alt="<?php echo $website -> idn ?>"	width="205px" height="154px" />
                <br/>
                <p>
                    <?php echo Yii::t("app", "The score is {Score}/100", array("{Score}" => $website -> score)) ?>
                    <div class="progress progress-striped" style="height:15px">
                        <div class="bar" style="width:<?php echo $website -> score ?>%;"></div>
                    </div>
                    <a href="<?php echo $url ?>"><?php echo Yii::t("app", "View analysis") ?></a>
                </p>
            </div>
        </li>
    <?php endforeach; ?>
</ul>

<div class="pagination pull-right">
    <?php $this -> widget('CLinkPager', array(
        'pages' => $dataProvider -> pagination,
        'cssFile' => false,
        'header' => '',
        'hiddenPageCssClass' => 'disabled',
        'selectedPageCssClass' => 'active',
    )); ?>
</div>
<?php if($dataProvider -> pagination->pageCount > 1): ?>
<div class="clearfix"></div>
<?php endif; ?>
