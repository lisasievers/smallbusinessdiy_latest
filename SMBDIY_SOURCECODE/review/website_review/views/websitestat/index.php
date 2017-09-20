<style type="text/css">
    .container-narrow { max-width: 1170px; }
</style>
<script type="text/javascript">
var addthis_config = {
      ui_language: "<?php echo Yii::app()->language ?>"
}
function showPageSpeedForm() {
    var $form = $("#pagespeed_form");
    $("#pagespeed_tabs").prepend($form);
    $form.show();
}

$(document).ready(function(){
	dynamicThumbnail({<?php echo $website['id'] ?>:'<?php echo $thumbnail ?>'});
	var pie_data = [];
	pie_data[0] = {
		label: '<?php echo Yii::t("app", "External Links") ?> : <?php echo Yii::t("app", "nofollow") ?> <?php echo Utils::proportion($linkcount, $links["external_nofollow"]) ?>%',
		data: <?php echo $links["external_nofollow"] ?>,
		color: '#6A93BA'
	};
	pie_data[1] = {
		label: '<?php echo Yii::t("app", "External Links") ?> : <?php echo Yii::t("app", "dofollow") ?> <?php echo Utils::proportion($linkcount, $links["external_dofollow"]) ?>%',
		data: <?php echo $links["external_dofollow"] ?>,
		color: '#315D86'
	};
	pie_data[2] = {
		label: '<?php echo Yii::t("app", "Internal Links") ?> <?php echo Utils::proportion($linkcount, $links["internal"]) ?>%',
		data: <?php echo $links["internal"] ?>,
		color: '#ddd'
	};

	drawPie();
	window.onresize = function(event) {
		drawPie();
	};

	function drawPie() {
		 $.plot($("#links-pie"), pie_data, {
			series: {
				pie: {
					show: true
				}
			}
		});
	}

	$('.collapse-task').click(function(){
		var p = $(this).parent(".task-list");
		p.find(".over-max").hide();
		$(this).hide();
		p.find('.expand-task').show();
	});

	$('.expand-task').click(function(){
		var p = $(this).parent(".task-list");
		p.find(".over-max").show();
		$(this).hide();
		p.find('.collapse-task').show();
	});

	$('#update_stat').click(function(){
		var href = $(this).attr("href");
		if(href.indexOf("#") < 0) {
			return true;
		}
		$('#upd_help').css('display','inline-block');
		$('#domain').val('<?php echo $website['domain'] ?>');
	});

    $(".container-narrow").attr("id", "top");

	$("body").on("click", ".pdf_review", function() {
		$(this).hide();
        $(this).closest(".form-container").find(".download_form").fadeIn();
		return false;
	});

    $("body").scrollspy({
        target: '#leftCol',
        offset: 100
    });

    $("#affix-menu").affix({
        offset: {
            top: $("#affix-menu").offset().top
        }
    });

    $("#navbar-review-mobile").affix({
        offset: {
            top: $("#navbar-review-mobile").offset().top
        }
    });

    var animationTopOffset = screen.width <= 768 ? 50 : 10;
    $('#leftCol a[href*=#]').click(function() {
        if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
            if (target.length) {
                $('html,body').animate({
                    scrollTop: target.offset().top - animationTopOffset
                }, 500, function(){
                    $('.nav-collapse').collapse('hide');
                });
                return false;
            }
        }
    });

    <?php if($pageSpeed): ?>
    showPageSpeedForm();
    <?php endif; ?>

    <?php if(isset($_POST['downloadPageSpeedPdfForm'])) : ?>
    $('a[href="#page_speed"]').trigger("click");
    <?php endif; ?>

    function resizeMobileNav() {
        var $nav = $("#navbar-review-mobile");
        var $container = $(".container-narrow");
        $nav.css("width", $container.width());
    }
    if(screen.width <= 768) {
        resizeMobileNav();
        window.onresize = function() { resizeMobileNav() };
    }
});
</script>

<div class="row-fluid" style="display:none">
	<div class="span2" id="enter_domain">
		<p class="lead"><?php echo Yii::t("app", "Enter domain") ?></p>
	</div>

	<div class="span10">
		<?php echo $this->renderPartial("//site/request_form"); ?>
	</div>
</div>



<div class="row-fluid" style="margin-top:10px">
    <div class="span3" id="leftCol" style="display:none">
        <ul class="nav nav-tabs nav-stacked bs-docs-sidenav hidden-phone" id="affix-menu">
            <li>
                <a href="#top">
                    <i class="fa fa-chevron-up"></i>&nbsp;&nbsp; <?php echo Yii::t("app", "Back to top"); ?>
                </a>
            </li>
            <li>
                <a href="#content">
                    <i class="fa fa-list-alt"></i>&nbsp;&nbsp; <?php echo Yii::t("app", "Content"); ?>
                </a>
            </li>
            <li>
                <a href="#links">
                    <i class="fa fa-link"></i>&nbsp;&nbsp; <?php echo Yii::t("app", "Links"); ?>
                </a>
            </li>
            <li>
                <a href="#keywords">
                    <i class="fa fa-tags"></i>&nbsp;&nbsp; <?php echo Yii::t("app", "Keywords"); ?>
                </a>
            </li>
            <li>
                <a href="#usability">
                    <i class="fa fa-hand-o-up"></i>&nbsp;&nbsp; <?php echo Yii::t("app", "Usability"); ?>
                </a>
            </li>
            <li>
                <a href="#document">
                    <i class="fa fa-file-text-o"></i>&nbsp;&nbsp; <?php echo Yii::t("app", "Document"); ?>
                </a>
            </li>
            <li>
                <a href="#mobile">
                    <i class="fa fa-tablet"></i>&nbsp;&nbsp; <?php echo Yii::t("app", "Mobile"); ?>
                </a>
            </li>
            <?php if($misc): ?>
            <li>
                <a href="#optimization">
                    <i class="fa fa-wrench"></i>&nbsp;&nbsp; <?php echo Yii::t("app", "Optimization"); ?>
                </a>
            </li>
            <?php endif; ?>
            <li>
                <a href="#page_speed">
                    <i class="fa fa-tachometer"></i>&nbsp;&nbsp; <?php echo Yii::t("app", "Page speed"); ?>
                </a>
            </li>
        </ul>


        <div class="navbar visible-phone container-fullwidth" id="navbar-review-mobile">
            <div class="navbar-inner">
                <div class="container">

                    <!-- .btn-navbar is used as the toggle for collapsed navbar content -->
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>

                    <!-- Be sure to leave the brand out there if you want it shown -->
                    <a class="brand" href="#"><?php echo Yii::t("app", "Navigation") ?></a>

                    <!-- Everything you want hidden at 940px or less, place within here -->
                    <div class="nav-collapse collapse">
                        <ul class="nav nav-tabs nav-stacked bs-docs-sidenav">
                            <li>
                                <a href="#top">
                                    <i class="fa fa-chevron-up"></i>&nbsp;&nbsp; <?php echo Yii::t("app", "Back to top"); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#content">
                                    <i class="fa fa-list-alt"></i>&nbsp;&nbsp; <?php echo Yii::t("app", "Content"); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#links">
                                    <i class="fa fa-link"></i>&nbsp;&nbsp; <?php echo Yii::t("app", "Links"); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#keywords">
                                    <i class="fa fa-tags"></i>&nbsp;&nbsp; <?php echo Yii::t("app", "Keywords"); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#usability">
                                    <i class="fa fa-hand-o-up"></i>&nbsp;&nbsp; <?php echo Yii::t("app", "Usability"); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#document">
                                    <i class="fa fa-file-text-o"></i>&nbsp;&nbsp; <?php echo Yii::t("app", "Document"); ?>
                                </a>
                            </li>
                            <li>
                                <a href="#mobile">
                                    <i class="fa fa-tablet"></i>&nbsp;&nbsp; <?php echo Yii::t("app", "Mobile"); ?>
                                </a>
                            </li>
                            <?php if($misc): ?>
                                <li>
                                    <a href="#optimization">
                                        <i class="fa fa-wrench"></i>&nbsp;&nbsp; <?php echo Yii::t("app", "Optimization"); ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li>
                                <a href="#page_speed">
                                    <i class="fa fa-tachometer"></i>&nbsp;&nbsp; <?php echo Yii::t("app", "Page speed"); ?>
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>

    </div>
    <div class="span9">
        <div class="row-fluid">
            <div class="span6">
                <img class="thumbnail" id="thumb_<?php echo $website['id'] ?>" src="<?php echo Yii::app() -> getBaseUrl(true) ?>/img/loader.gif" alt="<?php echo $website['idn'] ?>" />
            </div>
            <div class="span6">
                <h1 class="h-review"><?php echo Yii::t("app", "Analyse of {Domain}", array("{Domain}" => $website['idn'])) ?></h1>
                <i class="icon-time"></i>&nbsp;<small><?php echo Yii::t("app", "Generated on") ?> <?php echo Yii::t("app", "Generated format", array(
                        '{Month}' => Yii::t("app", $generated['M']),
                        '{Day}' => $generated['d'],
                        '{Year}' => $generated['Y'],
                        '{Hour}' => $generated['H'],
                        '{Minute}' => $generated['i'],
                        '{Ante}' => $generated['A'],
                    )); ?>
                </small><br/><br/>

                <?php if($diff > Yii::app() -> params["analyser"]["cacheTime"]) : ?>
                    <?php echo Yii::t("app", "Old statistics? UPDATE!", array(
                        '{UPDATE}' => '<a href="'. $updUrl .'" class="btn btn-success" id="update_stat">'. Yii::t("app", "UPDATE") . '</a>',
                    )) ?>
                    <br/><br/>
                <?php endif; ?>

                <br/>
                <strong><?php echo Yii::t("app", "The score is {Score}/100", array("{Score}" => $website['score'])) ?></strong>
                <br />
                <div class="progress progress-striped" style="margin-top:10px; width:200px;">
                    <div class="bar" style="width:<?php echo $website['score'] ?>%;"></div>
                </div>

                <br/>

                <?php if(!$captchaEnabled): ?>
                    <a href="<?php echo $this->createUrl("websitestat/generatePDF", array("domain"=>$website['domain'])) ?>" class="btn btn-primary">
                        <?php echo Yii::t("app", "Download PDF version"); ?>
                    </a>
                <?php else: ?>
                    <div class="form-container">
                        <?php if(!isset($_POST['DownloadPdfForm'])) { ?>
                            <button class="btn btn-primary pdf_review">
                                <?php echo Yii::t("app", "Download PDF version"); ?>
                            </button>
                        <?php } ?>
                        <form method="post" class="form-horizontal <?php echo !isset($_POST['DownloadPdfForm']) ? " download_form" : null ?>">
                            <div class="control-group">
                                <?php $this->widget("ext.recaptcha2.ReCaptcha2Widget", array(
                                    "siteKey"=>$config['public-key'],
                                    'model'=>$downloadForm,
                                    'attribute'=>'validation',
                                    "widgetOpts"=>array(),
                                )) ?>
                                <?php echo CHtml::error($downloadForm, 'validation', array('class'=>'alert alert-error')); ?>
                            </div>
                            <div class="control-group">
                                <input type="submit" value="<?php echo Yii::t("app", "Download PDF version"); ?>" class="btn btn-primary">
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>


        <!-- SEO Content -->
        <div class="row-fluid">
            <table class="table table-striped table-fluid">
                <thead>
                <tr>
                    <th colspan="3"><h4 id="content"><?php echo Yii::t("app", "SEO Content") ?></h4></th>
                </tr>
                </thead>
                <tbody>

                <!-- Title -->
                <?php $advice = $rateprovider -> addCompareArray("title", mb_strlen(html_entity_decode($meta["title"], ENT_QUOTES, 'UTF-8'))); ?>
                <tr class="<?php echo $advice ?>">
                    <td>
                        <div class="adv-icon adv-icon-<?php echo $advice ?>"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "Title") ?>
                    </td>
                    <td width="70%">
                        <p style="word-break: break-all;">
                            <?php echo CHtml::encode(html_entity_decode($meta['title'], ENT_QUOTES, "UTF-8")) ?>
                        </p>
                        <br/><br/>
                        <strong><?php echo Yii::t("app", "Length") ?> : <?php echo mb_strlen(html_entity_decode($meta['title'], ENT_QUOTES, 'UTF-8')); ?></strong>
                        <br/><br/>
                        <?php echo Yii::t("advice", "Title advice - $advice", array('{1}' => _RATE_TITLE_GOOD, '{2}' => _RATE_TITLE_BEST)); ?>
                    </td>
                </tr>

                <!-- Description -->
                <?php $advice = $rateprovider -> addCompareArray("description", mb_strlen($meta["description"])); ?>
                <tr class="<?php echo $advice ?>">
                    <td>
                        <div class="adv-icon adv-icon-<?php echo $advice ?>"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "Description") ?>
                    </td>
                    <td>
                        <p style="word-break: break-all;">
                            <?php echo CHtml::encode($meta["description"]) ?>
                        </p>
                        <br/><br/>
                        <strong><?php echo Yii::t("app", "Length") ?> : <?php echo mb_strlen($meta['description']); ?></strong>
                        <br/><br/>
                        <?php echo Yii::t("advice", "Description advice - $advice", array('{1}' => _RATE_DESC_GOOD, '{2}' => _RATE_DESC_BEST)); ?>
                    </td>
                </tr>

                <!-- Keywords -->
                <?php $advice = $rateprovider -> addCompare("keywords", mb_strlen($meta["keyword"])); ?>
                <tr class="<?php echo $advice ?>">
                    <td>
                        <div class="adv-icon adv-icon-<?php echo $advice ?>"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "Keywords") ?>
                    </td>
                    <td>
                        <p style="word-break: break-all;">
                            <?php echo CHtml::encode($meta["keyword"]) ?>
                        </p>
                        <br/><br/>
                        <?php echo Yii::t("advice", "Keywords advice - $advice"); ?>
                    </td>
                </tr>

                <!-- Og properties -->
                <?php $advice = $rateprovider -> addCompare("ogmetaproperties", !empty($meta["ogproperties"])); ?>
                <tr class="<?php echo $advice ?>">

                    <td>
                        <div class="adv-icon adv-icon-<?php echo $advice ?>"></div>
                    </td>

                    <td class="compare">
                        <?php echo Yii::t("app", "Og Meta Properties") ?>
                    </td>

                    <td>
                        <?php echo Yii::t("advice", "Og Meta Properties advice - $advice"); ?>
                        <div class="task-list">
                            <?php if(!empty($meta["ogproperties"])):?>
                                <table class="table table-striped table-fluid">
                                    <thead>
                                    <th><?php echo Yii::t("app", "Property") ?></th>
                                    <th><?php echo Yii::t("app", "Content") ?></th>
                                    </thead>
                                    <tbody>
                                    <?php foreach($meta["ogproperties"] as $property => $c): ?>
                                        <tr class="over-max">
                                            <td><?php echo CHtml::encode($property) ?></td>
                                            <td style="white-space:pre-wrap"><?php echo CHtml::encode($c) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <button class="expand-task btn btn-primary pull-right"><?php echo Yii::t("app", "Expand") ?></button>
                                <button class="collapse-task btn btn-primary pull-right"><?php echo Yii::t("app", "Collapse") ?></button>
                            <?php endif; ?>
                        </div>
                    </td>

                </tr>

                <!-- Headings -->
                <tr>
                    <td>
                        <div class="adv-icon adv-icon-neutral"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "Headings") ?>
                    </td>
                    <td>

                        <table class="table table-striped table-fluid">
                            <tbody>
                            <tr class="no-top-line">
                                <?php foreach($content['headings'] as $heading => $headings): ?>
                                    <td><strong><?php echo strtoupper($heading) ?></strong></td>
                                <?php endforeach; ?>
                            </tr>
                            <tr>
                                <?php foreach($content['headings'] as $headings): ?>
                                    <td><?php echo count($headings) ?></td>
                                <?php endforeach; ?>
                            </tr>
                            </tbody>
                        </table>

                        <div class="task-list">
                            <?php if($content['isset_headings']): $i = 0;?>
                            <ul id="headings">
                                <?php
                                foreach($content['headings'] as $heading => $headings):
                                    if(!empty($headings)):
                                        foreach($headings as $h) : $i++;?>
                                            <li <?php echo $i > $over_max ? 'class="over-max"': null; ?>>[<?php echo mb_strtoupper($heading) ?>] <?php echo CHtml::encode($h) ?></li>
                                        <?php
                                        endforeach; endif; endforeach;
                                ?>
                            </ul>
                            <?php if($i > $over_max) : ?>
                                <button class="expand-task btn btn-primary pull-right"><?php echo Yii::t("app", "Expand") ?></button>
                                <button class="collapse-task btn btn-primary pull-right"><?php echo Yii::t("app", "Collapse") ?></button>
                            <?php endif; ?>
                        </div>

                        <?php endif; ?>

                    </td>
                </tr>

                <!-- Images -->
                <?php $advice = $rateprovider -> addCompare("imgHasAlt", $content["total_img"] == $content["total_alt"]); ?>
                <tr class="<?php echo $advice ?>">
                    <td>
                        <div class="adv-icon adv-icon-<?php echo $advice ?>"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "Images") ?>
                    </td>
                    <td>
                        <?php echo Yii::t("app", "We found {Count} images on this web page.", array("{Count}" =>  $content["total_img"])) ?>
                        <br/>
                        <br/>
                        <?php echo Yii::t("advice", "Image advice - $advice", array('{Number}'=> $content["total_img"] - $content["total_alt"])); ?>
                    </td>
                </tr>

                <!-- Text/HTML Ratio -->
                <?php $advice = $rateprovider -> addCompareArray("htmlratio", $document['htmlratio']); ?>
                <tr class="<?php echo $advice ?>">
                    <td>
                        <div class="adv-icon adv-icon-<?php echo $advice ?>"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "Text/HTML Ratio") ?>
                    </td>
                    <td>
                        <?php echo Yii::t("app", "Ratio") ?> : <strong><?php echo $document['htmlratio'] ?>%</strong>
                        <br/>
                        <br/>
                        <?php echo Yii::t("advice", "HTML ratio advice - $advice", array(
                            '{GoodNr}' => _RATE_HRATIO_GOOD,
                            '{BestNr}' => _RATE_HRATIO_BEST,
                            '{BadNr}'  => _RATE_HRATIO_BAD,
                        )); ?>
                    </td>
                </tr>

                <!-- Flash -->
                <?php $advice = $rateprovider ->  addCompare("noFlash", !$isseter["flash"]); ?>
                <tr class="<?php echo $advice ?>">
                    <td>
                        <div class="adv-icon adv-icon-<?php echo $advice ?>"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "Flash") ?>
                    </td>
                    <td>
                        <?php echo Yii::t("advice", "Flash advice - $advice"); ?>
                    </td>
                </tr>

                <!-- Iframe -->
                <?php $advice = $rateprovider ->  addCompare("noIframe", !$isseter["iframe"]); ?>
                <tr class="<?php echo $advice ?>">
                    <td>
                        <div class="adv-icon adv-icon-<?php echo $advice ?>"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "Iframe") ?>
                    </td>
                    <td>
                        <?php echo Yii::t("advice", "Iframe advice - $advice"); ?>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>

        <br/>

        <!-- SEO Links -->
        <div class="row-fluid">
            <table class="table table-striped table-fluid">
                <thead>
                <tr>
                    <th colspan="3"><h4 id="links"><?php echo Yii::t("app", "SEO Links") ?></h4></th>
                </tr>
                </thead>
                <tbody>
                <!-- Friendly url -->
                <?php $advice = $rateprovider -> addCompare("isFriendlyUrl", $links['friendly']); ?>
                <tr class="<?php echo $advice ?>">
                    <td>
                        <div class="adv-icon adv-icon-<?php echo $advice ?>"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "URL Rewrite") ?>
                    </td>
                    <td width="70%">
                        <?php echo Yii::t("advice", "Friendly url advice - $advice"); ?>
                    </td>
                </tr>

                <!-- Underscore -->
                <?php $advice = $rateprovider -> addCompare('noUnderScore', !$links['isset_underscore']); ?>
                <tr class="<?php echo $advice ?>">
                    <td>
                        <div class="adv-icon adv-icon-<?php echo $advice ?>"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "Underscores in the URLs") ?>
                    </td>
                    <td>
                        <?php echo Yii::t("advice", "Underscore advice - $advice"); ?>
                    </td>
                </tr>

                <!-- In-page links -->
                <?php $advice = $rateprovider -> addCompare("issetInternalLinks", $links["internal"] > 0); ?>
                <tr>
                    <td>
                        <div class="adv-icon adv-icon-<?php echo $advice ?>"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "In-page links") ?>
                    </td>
                    <td style="max-width:600px">
                        <?php echo Yii::t("app", "We found a total of {Links} links including {Files} link(s) to files", array(
                            '{Links}' => $linkcount,
                            '{Files}' => $links['files_count'],
                        )); ?>

                        <br/>
                        <br/>

                        <div id="links-pie" class="row-fluid" style="max-width:600px;height:200px;"></div>

                        <br/>
                        <br/>


                        <div class="task-list row-fluid">
                            <table class="table table-striped table-fluid">
                                <thead>
                                <th width="60%"><?php echo Yii::t("app", "Anchor") ?></th>
                                <th width="20%"><?php echo Yii::t("app", "Type") ?></th>
                                <th width="20%"><?php echo Yii::t("app", "Juice") ?></th>
                                </thead>
                                <tbody>
                                <?php $i = 0; foreach($links['links'] as $link): $i++;?>
                                    <tr <?php echo $i > $over_max ? 'class="over-max"': null; ?>>
                                        <td>
                                            <a href="<?php echo Yii::app() -> getBaseUrl(true) ?>/redirect.php?url=<?php echo rawurlencode($link["Link"]) ?>" target="_blank">
                                                <?php echo !empty($link["Name"]) ? CHtml::encode(html_entity_decode($link["Name"], ENT_QUOTES, 'UTF-8')) : Yii::t("app", "-") ?>
                                            </a>
                                        </td>
                                        <td><?php echo Yii::t("app", $link["Type"]) ?></td>
                                        <td><?php echo Yii::t("app", $link["Juice"]) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>

                            <?php if($i > $over_max) : ?>
                                <button class="expand-task btn btn-primary pull-right"><?php echo Yii::t("app", "Expand") ?></button>
                                <button class="collapse-task btn btn-primary pull-right"><?php echo Yii::t("app", "Collapse") ?></button>
                            <?php endif; ?>
                        </div>

                    </td>
                </tr>

                </tbody>
            </table>
        </div>


        <!-- SEO Keywords -->

        <div class="row-fluid">
            <table class="table table-striped table-fluid">
                <thead>
                <tr>
                    <th colspan="3"><h4 id="keywords"><?php echo Yii::t("app", "SEO Keywords") ?></h4></th>
                </tr>
                </thead>
                <tbody>
                <!-- Tag cloud -->
                <tr>
                    <td class="adv-icon">
                        <div class="adv-icon adv-icon-neutral"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "Keywords Cloud") ?>
                    </td>
                    <td class="cloud-container">
                        <?php foreach($cloud['words'] as $word => $stat): ?>
                            <span class="grade-<?php echo $stat['grade'] ?>"><?php echo CHtml::encode($word) ?></span>
                        <?php endforeach; ?>
                    </td>
                </tr>

                <!-- Keywords Consistency -->
                <?php $advice = $rateprovider -> addCompare('noUnderScore', !$links['isset_underscore']); ?>
                <tr>
                    <td>
                        <div class="adv-icon adv-icon-neutral"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "Keywords Consistency") ?>
                    </td>
                    <td>

                        <table class="table table-striped table-fluid">
                            <thead>
                            <th><?php echo Yii::t("app", "Keyword") ?></th>
                            <th><?php echo Yii::t("app", "Content") ?></th>
                            <th><?php echo Yii::t("app", "Title") ?></th>
                            <th><?php echo Yii::t("app", "Keywords") ?></th>
                            <th><?php echo Yii::t("app", "Description") ?></th>
                            <th><?php echo Yii::t("app", "Headings") ?></th>
                            </thead>
                            <tbody>
                            <?php foreach($cloud['matrix'] as $word => $object): ?>
                                <tr>
                                    <td><?php echo CHtml::encode($word) ?></td>
                                    <td><?php echo (int) $cloud['words'][$word]['count'] ?></td>
                                    <td><img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/img/isset_<?php echo (int) $object['title'] ?>.png" /></td>
                                    <td><img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/img/isset_<?php echo (int) $object['keywords'] ?>.png" /></td>
                                    <td><img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/img/isset_<?php echo (int) $object['description'] ?>.png" /></td>
                                    <td><img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/img/isset_<?php echo (int) $object['headings'] ?>.png" /></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

                    </td>
                </tr>

                </tbody>
            </table>
        </div>


        <div class="row-fluid">
            <!-- Usability -->
            <table class="table table-striped table-fluid">
                <thead>
                <tr>
                    <th colspan="3"><h4 id="usability"><?php echo Yii::t("app", "Usability") ?></h4></th>
                </tr>
                </thead>
                <tbody>

                <!-- Url -->
                <tr>
                    <td class="adv-icon">
                        <div class="adv-icon adv-icon-neutral"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "Url") ?>
                    </td>
                    <td width="70%">
                        <?php echo Yii::t("app", "Domain") ?> : <?php echo $website['idn'] ?>
                        <br />
                        <?php echo Yii::t("app", "Length") ?> : <?php echo mb_strlen($website['idn']) ?>
                    </td>
                </tr>

                <!-- Favicon -->
                <?php $advice = $rateprovider -> addCompare('issetFavicon', !empty($document['favicon'])); ?>
                <tr class="<?php echo $advice ?>">
                    <td>
                        <div class="adv-icon adv-icon-neutral"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "Favicon") ?>
                    </td>
                    <td>
                        <?php echo Yii::t("advice", "Favicon advice - $advice"); ?>
                    </td>
                </tr>

                <!-- Printability -->
                <?php $advice = $rateprovider -> addCompare('isPrintable', $isseter['printable']); ?>
                <tr class="<?php echo $advice ?>">
                    <td>
                        <div class="adv-icon adv-icon-<?php echo $advice ?>"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "Printability") ?>
                    </td>
                    <td>
                        <?php echo Yii::t("advice", "Printability advice - $advice"); ?>
                    </td>
                </tr>

                <!-- Language -->
                <?php $advice = $rateprovider -> addCompare('lang', $document['lang']); ?>
                <tr class="<?php echo $advice ?>">
                    <td>
                        <div class="adv-icon adv-icon-<?php echo $advice ?>"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "Language") ?>
                    </td>
                    <td>
                        <?php echo Yii::t("advice", "Language advice - $advice", array('{Language}' => $document['lang'])); ?>
                    </td>
                </tr>

                <!-- Dublin Core -->
                <?php $advice = $rateprovider -> addCompare('lang', $isseter['dublincore']); ?>
                <tr class="<?php echo $advice ?>">
                    <td>
                        <div class="adv-icon adv-icon-<?php echo $advice ?>"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "Dublin Core") ?>
                    </td>
                    <td>
                        <?php echo Yii::t("advice", "Dublin Core advice - $advice"); ?>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>


        <div class="row-fluid">
            <!-- Document -->
            <table class="table table-striped table-fluid">
                <thead>
                <tr>
                    <th colspan="3"><h4 id="document"><?php echo Yii::t("app", "Document") ?></h4></th>
                </tr>
                </thead>
                <tbody>

                <!-- Doctype -->
                <?php $advice = $rateprovider -> addCompare('doctype', $document['doctype']); ?>
                <tr class="<?php echo $advice ?>">
                    <td class="adv-icon">
                        <div class="adv-icon adv-icon-<?php echo $advice ?>"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "Doctype") ?>
                    </td>
                    <td>
                        <?php if($document['doctype']):
                            echo $document['doctype'];
                        else:
                            echo Yii::t("app", "Missing doctype");
                        endif; ?>
                    </td>
                </tr>

                <!-- Encoding -->
                <?php $advice = $rateprovider -> addCompare('charset', $document['charset']); ?>
                <tr class="<?php echo $advice ?>">
                    <td>
                        <div class="adv-icon adv-icon-<?php echo $advice ?>"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "Encoding") ?>
                    </td>
                    <td>
                        <?php echo Yii::t("advice", "Encoding advice - $advice", array("{Charset}" => $document['charset'])); ?>
                    </td>
                </tr>

                <!-- W3C Validity -->
                <?php $advice = $rateprovider -> addCompare('w3c', $w3c['valid']); ?>
                <tr class="<?php echo $advice ?>">
                    <td>
                        <div class="adv-icon adv-icon-<?php echo $advice ?>"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "W3C Validity") ?>
                    </td>
                    <td>
                        <?php echo Yii::t("app", "Errors") ?> : <?php echo (int) $w3c['errors'] ?>
                        <br/>
                        <?php echo Yii::t("app", "Warnings") ?> : <?php echo (int) $w3c['warnings'] ?>
                    </td>
                </tr>

                <!-- Email Privacy -->
                <?php $advice = $rateprovider -> addCompare('noEmail', !$isseter['email']); ?>
                <tr class="<?php echo $advice ?>">
                    <td>
                        <div class="adv-icon adv-icon-<?php echo $advice ?>"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "Email Privacy") ?>
                    </td>
                    <td>
                        <?php echo Yii::t("advice", "Email Privacy advice - $advice"); ?>
                    </td>
                </tr>

                <!-- Deprecated -->
                <?php $advice = $rateprovider -> addCompare('noDeprecated', empty($content['deprecated'])); ?>
                <tr class="<?php echo $advice ?>">
                    <td>
                        <div class="adv-icon adv-icon-<?php echo $advice ?>"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "Deprecated HTML") ?>
                    </td>
                    <td width="70%">
                        <?php if(!empty($content['deprecated'])) : ?>
                            <table class="table table-striped table-fluid" width="20%">
                                <thead>
                                <th><?php echo Yii::t("app", "Deprecated tags") ?></th>
                                <th><?php echo Yii::t("app", "Occurrences") ?></th>
                                </thead>
                                <tbody>
                                <?php foreach($content['deprecated'] as $tag => $count): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars('<'.$tag.'>') ?></td>
                                        <td><?php echo $count ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                        <?php echo Yii::t("advice", "Deprecated advice - $advice"); ?>
                    </td>
                </tr>

                <!-- Speed Tips -->
                <tr>
                    <td>
                        <div class="adv-icon adv-icon-neutral"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "Speed Tips") ?>
                    </td>
                    <td>

                        <table>
                            <tbody>

                            <tr class="no-top-line">
                                <?php $advice = $rateprovider -> addCompare('noNestedtables', !$isseter['nestedtables']); ?>
                                <td><img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/img/isset_<?php echo (int)!$isseter['nestedtables'] ?>.png" /></td>
                                <td><?php echo Yii::t("advice", "Nested tables advice - $advice"); ?></td>
                            </tr>

                            <tr>
                                <?php $advice = $rateprovider -> addCompare('noInlineCSS', !$isseter['inlinecss']); ?>
                                <td><img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/img/isset_<?php echo (int)!$isseter['inlinecss'] ?>.png" /></td>
                                <td><?php echo Yii::t("advice", "Inline CSS advice - $advice"); ?></td>
                            </tr>

                            <tr>
                                <?php $advice = $rateprovider -> addCompareArray('cssCount', $document['css']); ?>
                                <td><img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/img/isset_<?php echo $advice == 'success' ? '1' : '0' ?>.png" /></td>
                                <td><?php echo Yii::t("advice", "CSS count advice - $advice", array("{MoreNr}" => _RATE_CSS_COUNT)); ?></td>
                            </tr>

                            <tr>
                                <?php $advice = $rateprovider -> addCompareArray('jsCount', $document['js']); ?>
                                <td><img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/img/isset_<?php echo $advice == 'success' ? '1' : '0' ?>.png" /></td>
                                <td><?php echo Yii::t("advice", "JS count advice - $advice", array("{MoreNr}" => _RATE_JS_COUNT)); ?></td>
                            </tr>

                            <tr>
                                <?php $advice = $rateprovider -> addCompare('hasGzip', $isseter['gzip']); ?>
                                <td><img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/img/isset_<?php echo $advice == 'success' ? '1' : '0' ?>.png" /></td>
                                <td><?php echo Yii::t("advice", "Gzip - $advice"); ?></td>
                            </tr>

                            </tbody>
                        </table>

                    </td>
                </tr>

                </tbody>
            </table>
        </div>

        <!-- Mobile Optimization -->
        <div class="row-fluid">
            <!-- Document -->
            <table class="table table-striped table-fluid">
                <thead>
                <tr>
                    <th colspan="3"><h4 id="mobile"><?php echo Yii::t("app", "Mobile") ?></h4></th>
                </tr>
                </thead>
                <tbody>
                <!-- Mobile Optimization -->
                <tr>
                    <td class="adv-icon">
                        <div class="adv-icon adv-icon-neutral"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "Mobile Optimization") ?>
                    </td>
                    <td width="70%">

                        <table>
                            <tbody>

                            <tr class="no-top-line">
                                <td><img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/img/isset_<?php echo (int)$isseter['appleicons'] ?>.png" /></td>
                                <td><?php echo Yii::t("app", "Apple Icon"); ?></td>
                            </tr>

                            <tr>
                                <td><img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/img/isset_<?php echo (int)$isseter['viewport'] ?>.png" /></td>
                                <td><?php echo Yii::t("app", "Meta Viewport Tag"); ?></td>
                            </tr>

                            <tr>
                                <td><img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/img/isset_<?php echo (int)!$isseter['flash'] ?>.png" /></td>
                                <td><?php echo Yii::t("app", "Flash content"); ?></td>
                            </tr>

                            </tbody>
                        </table>

                    </td>
                </tr>

                </tbody>
            </table>
        </div>


        <?php if($misc): ?>
        <!-- Optimization -->
        <div class="row-fluid">
            <table class="table table-striped table-fluid">
                <thead>
                <tr>
                    <th colspan="3"><h4 id="optimization"><?php echo Yii::t("app", "Optimization") ?></h4></th>
                </tr>
                </thead>
                <tbody>

                <!-- Sitemap -->
                <?php $advice = $rateprovider -> addCompare('hasSitemap', !empty($misc['sitemap'])); ?>
                <tr class="<?php echo $advice ?>">
                    <td class="adv-icon">
                        <div class="adv-icon adv-icon-<?php echo $advice ?>"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "XML Sitemap") ?>
                    </td>
                    <td>
                        <?php if(!empty($misc['sitemap'])): ?>
                            <?php echo Yii::t("advice", "XML Sitemap - $advice"); ?><br><br>
                            <table class="table table-striped table-fluid">
                                <tbody>
                                <?php foreach($misc['sitemap'] as $sitemap): ?>
                                    <tr>
                                        <td>
                                            <?php echo CHtml::encode($sitemap); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <strong><?php echo Yii::t("app", "Missing"); ?></strong>
                            <br><br>
                            <?php echo Yii::t("advice", "XML Sitemap - $advice"); ?>
                        <?php endif; ?>
                    </td>
                </tr>

                <!-- Robots -->
                <?php $advice = $rateprovider -> addCompare('hasRobotsTxt', $isseter['robotstxt']); ?>
                <tr class="<?php echo $advice ?>">
                    <td>
                        <div class="adv-icon adv-icon-<?php echo $advice ?>"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "Robots.txt") ?>
                    </td>
                    <td>
                        <?php if($isseter['robotstxt']): ?>
                            <?php echo "http://".$website['domain']."/robots.txt"; ?>
                            <br><br>
                            <?php echo Yii::t("advice", "Robots txt - $advice"); ?>
                        <?php else: ?>
                            <strong><?php echo Yii::t("app", "Missing"); ?></strong>
                            <br><br>
                            <?php echo Yii::t("advice", "Robots txt - $advice"); ?>
                        <?php endif; ?>
                    </td>
                </tr>

                <!-- Analytics support -->
                <?php $advice = $rateprovider -> addCompare('hasAnalytics', !empty($misc['analytics'])); ?>
                <tr class="<?php echo $advice ?>">
                    <td>
                        <div class="adv-icon adv-icon-<?php echo $advice ?>"></div>
                    </td>
                    <td class="compare">
                        <?php echo Yii::t("app", "Analytics") ?>
                    </td>
                    <td>
                        <?php if(!empty($misc['analytics'])): ?>
                            <?php echo Yii::t("advice", "Analytics - $advice"); ?><br><br>
                            <table class="table table-striped table-fluid">
                                <tbody>
                                <?php foreach($misc['analytics'] as $analytics): ?>
                                    <tr>
                                        <td>
                                            <img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/img/analytics/<?php echo $analytics ?>.png" />
                                            &nbsp;&nbsp;
                                            <?php echo CHtml::encode(AnalyticsFinder::getProviderName($analytics)); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <strong><?php echo Yii::t("app", "Missing"); ?></strong>
                            <br><br>
                            <?php echo Yii::t("advice", "Analytics - $advice"); ?>
                        <?php endif; ?>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <h4 id="page_speed" style="display:none"><?php echo Yii::t("app", "Page speed") ?></h4>
        <hr>

        <?php if(Yii::app()->params['partialPdf']): ?>
            <div class="form-container" id="pagespeed_form" style="display: none">
                <?php if(!$captchaEnabled): ?>
                    <a href="<?php echo $this->createUrl("websitestat/GeneratePageSpeed", array("domain"=>$website['domain'])) ?>" class="btn btn-primary pull-right">
                        <?php echo Yii::t("app", "Download PDF version"); ?>
                    </a>
                <?php else: ?>
                    <?php if(!isset($_POST['downloadPageSpeedPdfForm'])) { ?>
                        <button class="btn btn-primary pdf_review pull-right">
                            <?php echo Yii::t("app", "Download PDF version"); ?>
                        </button>
                    <?php } ?>
                    <form method="post" class="form-horizontal<?php echo !isset($_POST['downloadPageSpeedPdfForm']) ? " download_form" : null ?>">
                        <div class="control-group pull-right">
                            <?php $this->widget("ext.recaptcha2.ReCaptcha2Widget", array(
                                "siteKey"=>$config['public-key'],
                                'model'=>$downloadPageSpeedPdfForm,
                                'attribute'=>'validation',
                                "widgetOpts"=>array(),
                            )) ?>
                            <?php echo CHtml::error($downloadPageSpeedPdfForm, 'validation', array('class'=>'alert alert-error')); ?>
                        </div>
                        <div class="clearfix"></div>
                        <div class="control-group">
                            <input type="submit" value="<?php echo Yii::t("app", "Download PDF version"); ?>" class="btn btn-primary pull-right">
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if($pageSpeed): ?>
            <?php $this->renderPartial("//websitestat/pagespeed_web", array(
                "results"=>$pageSpeed,
                "website"=>$website,
            )) ?>
        <?php else: ?>
            <div class="error alert alert-error" id="pagespeedErrors" style="display:none"></div>
            <div id="page-speed-result" style="display:none">
                <h5><?php echo Yii::t("app", "Analyzing...") ?></h5>
                <div class="progress progress-striped active">
                    <div class="bar" style="width: 100%;"></div>
                </div>
            </div>
            <script type="text/javascript">
                (function() {
                    function papulateErrors (obj, errors) {
                        for(var e in errors) {
                            if(typeof(errors[e]) == 'object')
                                papulateErrors(obj, errors[e])
                            else
                                obj.append(errors[e] + '<br/>');
                        }
                    }

                    $.getJSON("<?php echo $this->createUrl("parse/pagespeed", array("domain"=>$website['domain'])) ?>", function(data){
                        var $errorObj = $("#pagespeedErrors");
                        var $result = $("#page-speed-result");

                        if(data.error) {
                            papulateErrors($errorObj, data.error);
                            $errorObj.show();
                            $result.empty();
                            return false;
                        }

                        $result.empty();
                        $result.html(data.content);
                        showPageSpeedForm();
                    });
                })();
            </script>
        <?php endif; ?>
    </div>
</div>