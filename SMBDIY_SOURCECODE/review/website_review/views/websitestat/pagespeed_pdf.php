<style>
    .code {
         padding: 2px 4px;
         color: #d14;
         white-space: nowrap;
         background-color: #f7f7f9;
         border: 1px solid #e1e1e8;
     }
    .url-block {
        background-color: #fff;
        max-height: 324px;
        overflow-x: hidden;
        overflow-y: auto;
        padding-left: 0;
        margin-left: 0;
    }
    .well {
        background-color: #f5f5f5;
        border: 1px solid #e3e3e3;
    }
    .label {
        font-weight: bold;
        color: #fff;
        background-color: #999;
        font-size: 16px;
        padding: 10px;
    }
    .label-warning {
        background-color: #f89406;
    }
    .label-important {
        background-color: #b94a48;
    }
    .label-success {
        background-color: #468847;
    }
    .impact-title {
        font-size: 14px;
        margin-bottom: 15px;
    }
    .score-header {
        font-size: 16px;
    }
</style>
<?php
$dimension = array(
    "desktop"=>array(
        "width"=>181,
        "height"=>136,
    ),
    "mobile"=>array(
        "width"=>150,
        "height"=>250,
    ),
);
?>
<!-- Header -->
<table cellspacing="3" cellpadding="5">
    <thead>
    <tr>
        <th align="center"><h2 class="header"><?php echo Yii::t("app", "Page speed") ?></h2><br/><br/></th>
    </tr>
    </thead>
</table>

<?php foreach($results as $device=>$result): ?>
    <table cellspacing="3" cellpadding="5">
        <thead>
        <tr>
            <th><h3 align="left"><?php echo Yii::t("app", ucfirst($device)) ?></h3><br/></th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <img src="@<?php echo PageSpeedInsights::decodeData($result['screenshot']['data']) ?>" width="<?php echo $dimension[$device]['width'] ?>px" height="<?php echo $dimension[$device]['height'] ?>px">
                </td>
            </tr>
            <tr>
                <td>
                    <!-- Impact groups (USABILITY SPEED) -->
                    <?php foreach($result['formattedResults']['ruleResults'] as $group=>$impacts): ?>
                        <?php $score = $results[$device]['ruleGroups'][$group]['score']; ?>

                        <table cellpadding="5">
                            <tr>
                                <td>
                                    <span class="label label-<?php echo PageSpeedInsights::getClassFromScore($score) ?>"><?php echo $score ?> / 100</span>
                                    &nbsp;&nbsp;&nbsp;<span class="score-header"><?php echo Yii::t("app", $group) ?></span>
                                    <!-- Impact groups (success, warning, important) -->
                                    <?php foreach($impacts as $impactId=>$impact): if(!empty($impact)): ?>
                                        <h5 class="impact-title">
                                            <img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/img/advice_<?php echo $impactId ?>.png">
                                            <?php echo Yii::t("app", "Impact ". $impactId, array("{TotalInGroup}"=>count($impacts[$impactId]))); ?>
                                        </h5>
                                        <!-- Advices for group -->
                                        <?php if($impactId == "success"): ?>
                                            <div class="well">
                                                <?php foreach($impact as $adviceId=>$advice): ?>
                                                    <p class="impact-title"><strong><?php echo $advice['localizedRuleName'] ?></strong></p>
                                                    <p><?php echo $advice['summary']['format'] ?></p>
                                                    <br><br>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <?php foreach($impact as $adviceId=>$advice): if(!empty($advice['summary'])): ?>
                                                <div class="well">
                                                    <p class="impact-title"><strong><?php echo $advice['localizedRuleName'] ?></strong></p>
                                                    <p><?php echo $advice['summary']['format'] ?></p>
                                                    <br><br>
                                                    <?php if(!empty($advice['urlBlocks'])): foreach($advice['urlBlocks'] as $urlBlock): ?>
                                                        <p><?php echo $urlBlock['format']; ?></p>

                                                        <?php if(!empty($urlBlock['urls'])): ?>
                                                            <ul class="url-block">
                                                                <?php foreach($urlBlock['urls'] as $url) : ?>
                                                                    <li><?php echo $url['format'] ?></li><br>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        <?php endif; ?>
                                                    <?php endforeach; endif; ?>
                                                </div>
                                                <br><br>
                                            <?php endif; endforeach; ?>
                                        <?php endif; ?>
                                    <?php endif; endforeach; ?>
                                    <!-- End of groups (success, warning, important) -->
                                </td>
                            </tr>
                        </table>
                    <?php endforeach; ?>
                    <!-- End of impact groups (SPEED, USABILITY) -->
                </td>
            </tr>
        </tbody>
    </table>
<?php endforeach; ?>


