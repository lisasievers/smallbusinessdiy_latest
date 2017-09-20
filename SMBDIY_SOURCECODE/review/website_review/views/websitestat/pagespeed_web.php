<ul class="nav nav-tabs">
    <?php $flag = true; foreach($results as $device=>$result): ?>
    <li<?php echo $flag ? ' class="active"' : null ?> style="font-size: 18px">
        <a href="#pagespeed_<?php echo $device ?>" data-toggle="tab">
            <i class="fa fa-<?php echo $device ?>"></i>&nbsp;&nbsp; <?php echo Yii::t("app", ucfirst($device)) ?>
        </a>
    </li>
    <?php $flag = false; endforeach; ?>
</ul>

<div class="tab-content" id="pagespeed_tabs">
    <?php $flag=true; foreach($results as $device=>$result): ?>
    <div class="tab-pane<?php echo $flag ? ' active' : null ?>" id="pagespeed_<?php echo $device ?>">
        <div class="row-fluid">
            <div class="span7">
                <!-- Groups (SPEED, USABILITY) -->
                <?php foreach($result['formattedResults']['ruleResults'] as $group=>$impacts): ?>
                    <?php $score = $results[$device]['ruleGroups'][$group]['score']; ?>

                    <span class="label score-label label-<?php echo PageSpeedInsights::getClassFromScore($score) ?>"><?php echo $score ?> / 100</span>
                    &nbsp;&nbsp;&nbsp;<span class="score-header"><?php echo Yii::t("app", $group) ?></span>

                    <!-- Impact groups (success, warning, important) -->
                    <?php foreach($impacts as $impactId=>$impact): if(!empty($impact)): ?>
                        <h5 class="impact-title">
                            <span class="label label-<?php echo $impactId ?>"><i class="fa fa-<?php echo PageSpeedInsights::getIconNameByImpact($impactId) ?>"></i></span>
                            &nbsp;&nbsp;<?php echo Yii::t("app", "Impact ". $impactId, array("{TotalInGroup}"=>count($impacts[$impactId]))); ?>
                        </h5>

                        <!-- Advices for group -->
                        <?php if($impactId == "success"): ?>

                            <div class="advice-container">
                                <a href="#" class="expand-advice" data-toggle="collapse" data-target="#advice-<?php echo $impactId.$group.$device ?>" data-show="<i class='fa fa-caret-right advice-caret'></i><?php echo Yii::t("app", "Show details") ?>" data-hide="<i class='fa fa-caret-up advice-caret'></i><?php echo Yii::t("app", "Hide details") ?>" onclick="return false;">
                                    <i class="fa fa-caret-right advice-caret"></i><?php echo Yii::t("app", "Show details") ?>
                                </a>
                                <div id="advice-<?php echo $impactId.$group.$device ?>" class="advice-area collapse">
                                    <?php foreach($impact as $adviceId=>$advice): ?>
                                        <h6 class="advice-title"><?php echo $advice['localizedRuleName'] ?></h6>
                                        <p style="margin-bottom: 20px"><?php echo $advice['summary']['format'] ?></p>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else: ?>
                        <?php foreach($impact as $adviceId=>$advice): if(!empty($advice['summary'])): ?>
                            <div class="advice-container">
                                <h6 class="advice-title"><?php echo $advice['localizedRuleName'] ?></h6>


                                <div id="advice-<?php echo $adviceId.$device.$group ?>" class="advice-area collapse">
                                    <div class="well">
                                        <strong><?php echo $advice['summary']['format'] ?></strong>
                                        <br><br>
                                        <?php if(!empty($advice['urlBlocks'])): foreach($advice['urlBlocks'] as $urlBlock): ?>
                                            <p><?php echo $urlBlock['format']; ?></p>

                                            <?php if(!empty($urlBlock['urls'])): ?>
                                                <ul class="url-block">
                                                <?php foreach($urlBlock['urls'] as $url) : ?>
                                                    <li><?php echo $url['format'] ?></li>
                                                <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        <?php endforeach; endif; ?>
                                    </div>
                                </div>

                                <a href="#" class="expand-advice" data-toggle="collapse" data-target="#advice-<?php echo $adviceId.$device.$group ?>" data-show="<i class='fa fa-caret-right advice-caret'></i>Show how to fix" data-hide="<i class='fa fa-caret-up advice-caret'></i><?php echo Yii::t("app", "Hide details") ?>" onclick="return false;">
                                    <i class="fa fa-caret-right advice-caret"></i><?php echo Yii::t("app", "Show how to fix") ?>
                                </a>
                            </div>
                        <?php endif; endforeach; ?>
                        <?php endif; ?><!-- End if group success -->
                        <!-- End of advices for group -->
                    <?php endif; endforeach; ?>
                    <!-- End of impact groups -->
                    <hr class="group-divider">
                <?php endforeach; ?>
                <!-- End of groups -->
            </div>
            <div class="span5">
                <div class="screenshot <?php echo $device ?>" style="-webkit-user-select: none;">
                    <div class="screenshot-img-container">
                        <img src="data:<?php echo $results[$device]['screenshot']['mime_type']?>;base64,<?php echo PageSpeedInsights::decodeData($results[$device]['screenshot']['data']) ?>">
                    </div>
                </div>
            </div>
        </div>

    </div>
    <?php $flag=false; endforeach; ?>
    <hr>
    <?php echo Yii::t("app", "Google page speed cache") ?>
</div>

<script type="text/javascript">
    $('.advice-area').on('show', function (e) {
        var $container = $(this).closest(".advice-container");
        var $collapse = $container.find(".expand-advice");
        $collapse.html($collapse.data("hide"));
    }).on("hide", function(e) {
        var $container = $(this).closest(".advice-container");
        var $collapse = $container.find(".expand-advice");
        $collapse.html($collapse.data("show"));
    });
</script>