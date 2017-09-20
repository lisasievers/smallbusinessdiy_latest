<style>
#chosensite{width:350px;height: 42px;padding: 10px;}
.act-group{margin-top: 10px;}
.sitedoc-section{border:2px solid green;}
.sitespy-section{border:2px solid orange;}
.sitereview-section{border:2px solid blue;}
.panel-heading{text-align: center;border-bottom-width: 1px;border-bottom-color: green;}
.small-box-footer{float:right;}
</style>
    <link href="<?php echo e(URL::to('src/js/plugins/morris/morris.css')); ?>" rel="stylesheet">
<?php if(isset($siteid)!=''){$sid=$siteid;}else{$sid='0';} ?>   
    <?php if( isset($sites) && count( $sites ) > 0 ): ?>
<div class="row">

  <div class="sitelist col-md-6">
  <form role="form" id="ReportSite" action="<?php echo e(route('user.reportshome')); ?>" method="post">
     <input type="hidden" name="_token" value="<?php echo e(Session::token()); ?>">
    <h2><i class="fa fa-crosshairs" aria-hidden="true"></i> Your Report Websites</h2>
<select id="chosensite" data-style="btn-info" name="sitename">
 
<?php foreach( $sites as $site ): ?>

<option value="<?php echo e($site['id']); ?>"  <?php if($sid==$site['id']): ?> selected='selected' <?php endif; ?>><?php echo e($site['site_name']); ?></option>
 <?php endforeach; ?>

  </select>
  <div class="act-group">
<button type="submit" class="btn btn-info">Go to Reports</button>
</div>
</form> 
  </div>
  <div class="col-md-6">
    </div>
</div>
<div style="margin-top: 10px;" class="col-xs-12"></div>

<?php endif; ?>
<?php if( isset($site_info) && count( $site_info ) > 0 ): ?>

<div class="row">
      
        
                
                    <div class="col-lg-12">
                        <div class="panel panel-green">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fa fa-bar-chart-o"></i> Day Wise Unique User Report From <span id="overview_from_date"><?php echo e($from_date); ?></span> to <span id="overview_to_date"><?php echo e($to_date); ?></span></h3>
                            </div>
                            <div class="panel-body chart-responsive">
                                <div class="chart" id="morris-line-chart" style="height: 300px;"></div>
                            </div>
                        </div>
                    </div>
                
        <div style="margin-top: 30px;" class="col-xs-12"></div>

        <div class="col-xs-12 col-sm-12 col-md-5 col-lg-6">
            <div class="info-box" style="border:1px solid #00A65A;border-bottom:2px solid #00A65A;">
                <span class="info-box-icon bg-green"><i class="fa fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text" style="font-size: 20px;">Total Unique Visitor</span>
                    <span class="info-box-number" id="total_unique_visitor"><?php echo e($total_unique_visitor); ?></span>
                </div><!-- /.info-box-content -->
            </div><!-- /.info-box -->
        </div>
        <div class="col-xs-12 col-sm-12 col-md-5 col-lg-6">
            <div class="info-box" style="border:1px solid #00A65A;border-bottom:2px solid #00A65A;">
                <span class="info-box-icon bg-green"><i class="fa fa-file-text-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text" style="font-size: 20px;">Total Page View</span>
                    <span class="info-box-number" id="total_page_view"><?php echo e($total_page_view); ?></span>
                </div><!-- /.info-box-content -->
            </div><!-- /.info-box -->
        </div>

        <div style="margin-top: 20px;" class="col-xs-12"></div>
<div class="col-xs-12 col-sm-12 col-md-5 col-lg-6">
            <div class="info-box" style="border:1px solid #ff851b;border-bottom:2px solid #ff851b;">
                <span class="info-box-icon bg-orange"><i class="fa fa-clock-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text" style="font-size: 20px;">Average Stay Time</span>
                    <span class="info-box-number" id="total_unique_visitor"><?php echo e($average_stay_time); ?></span>
                </div><!-- /.info-box-content -->
                <a href="<?php echo e($tools['sitespy_website']); ?>/domain_details_visitor/domain_details/<?php echo e($domain_id); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div><!-- /.info-box -->
        </div>
        <div class="col-xs-12 col-sm-12 col-md-5 col-lg-6">
            <div class="info-box" style="border:1px solid #ff851b;border-bottom:2px solid #ff851b;">
                <span class="info-box-icon bg-orange"><i class="fa fa-automobile"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text" style="font-size: 20px;">Average Visit</span>
                    <span class="info-box-number" id="total_page_view"><?php echo e($average_visit); ?></span>
                </div><!-- /.info-box-content -->
                <a href="<?php echo e($tools['sitespy_website']); ?>/domain_details_visitor/domain_details/<?php echo e($domain_id); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div><!-- /.info-box -->
        </div>
       
    
    </div>
        <div style="margin-top: 30px;" class="col-xs-12"></div>
<!-- Morris Charts -->
                
                <!-- /.row -->
<?php
$count=count($site_info);
$prelast=$site_info[$count-2]['speed_score'];
$last=$site_info[$count-1]['speed_score'];
if($last > $prelast){$c_speed='<i class="fa fa-thumbs-up fa-2x" aria-hidden="true"></i>';}else{$c_speed='<i class="fa fa-thumbs-down fa-2x" aria-hidden="true"></i>';}
$prelasta=$site_info[$count-2]['speed_usability_mobile'];
$lasta=$site_info[$count-1]['speed_usability_mobile'];
if($lasta > $prelasta){$c_usescore= '<i class="fa fa-thumbs-up fa-2x" aria-hidden="true"></i>';}else{$c_usescore='<i class="fa fa-thumbs-down fa-2x" aria-hidden="true"></i>';}
?>
                <div class="row">
                    
                    <div class="col-lg-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fa fa-long-arrow-right"></i> Site Speed Score <?php echo $c_usescore; ?></h3>
                            </div>
                            <div class="panel-body">
                                <div id="morris-use-bar"></div>
                                <div class="text-right">
                                    <a href="<?php echo e($tools['sitespy_website']); ?>/domain/domain_details_view/<?php echo e($domain_id); ?>">View Details <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fa fa-long-arrow-right"></i> Site Usability Score <?php echo $c_speed; ?></h3>
                            </div>
                            <div class="panel-body">
                                <div id="morris-bar-chart"></div>
                                <div class="text-right">
                                    <a href="<?php echo e($tools['sitespy_website']); ?>/domain/domain_details_view/<?php echo e($domain_id); ?>">View Details <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
<?php endif; ?>
<script src="<?php echo e(URL::to('src/js/plugins/morris/raphael.min.js')); ?>"></script>
<script src="<?php echo e(URL::to('src/js/plugins/morris/morris.min.js')); ?>"></script>
<!--<script src="<?php echo e(URL::to('src/js/plugins/morris/morris-data.js')); ?>"></script>-->
<script>
$(function() {
    // Bar Chart
    Morris.Bar({
        element: 'morris-bar-chart',
        data: [
            <?php         
            foreach($site_info as $keys=>$value)
            {
            //if($keys==''){continue;}
            //if($val['totalcount']!='0'){$percent=number_format((float)(($value*100)/$val['totalcount']), 2, '.', ''); } else { $percent=0;}
            ?> 
            { score: <?php echo $value->speed_score;?>, date: '<?php echo "$value->searched_at"; ?>' },
            <?php } ?>
        ],
        xkey: 'date',
        ykeys: ['score'],
        labels: ['Date'],
        barRatio: 0.4,
        xLabelAngle: 35,
        hideHover: 'auto',
        resize: true
    });
        Morris.Bar({
        element: 'morris-use-bar',
        data: [
            <?php         
            foreach($site_info as $keys=>$value)
            {
            //if($keys==''){continue;}
            //if($val['totalcount']!='0'){$percent=number_format((float)(($value*100)/$val['totalcount']), 2, '.', ''); } else { $percent=0;}
            ?> 
            { score: <?php echo $value->speed_usability_mobile;?>, date: '<?php echo "$value->searched_at"; ?>' },
            <?php } ?>
        ],
        xkey: 'date',
        ykeys: ['score'],
        labels: ['Date'],
        barRatio: 0.4,
        xLabelAngle: 35,
        hideHover: 'auto',
        resize: true
    });

      Morris.Line({
        element: 'morris-line-chart',
        data: [
            <?php   
            if(isset($line_chart)){      
            foreach($line_chart as $keys=>$value)
            {
            //if($keys==''){continue;}
            //if($val['totalcount']!='0'){$percent=number_format((float)(($value*100)/$val['totalcount']), 2, '.', ''); } else { $percent=0;}
            ?> 
            { user: <?php echo $value['user']; ?>, date: '<?php echo "$value[date]"; ?>' },
            <?php } } ?>
        ],
        xkey: 'date',
        ykeys: ['user'],
        labels: ['New User'],
        lineColors: ['#3c8dbc'],
        resize: true,
        hideHover: 'auto'
        
    });  
            
                            
});    
</script>
<!-- Flot Charts JavaScript -->
<!--[if lte IE 8]><script src="js/excanvas.min.js"></script><![endif]-->
<script src="<?php echo e(URL::to('src/js/plugins/flot/jquery.flot.js')); ?>"></script>
<script src="<?php echo e(URL::to('src/js/plugins/flot/jquery.flot.tooltip.min.js')); ?>"></script>
<script src="<?php echo e(URL::to('src/js/plugins/flot/jquery.flot.resize.js')); ?>"></script>
<script src="<?php echo e(URL::to('src/js/plugins/flot/jquery.flot.pie.js')); ?>"></script>
<script src="<?php echo e(URL::to('src/js/plugins/flot/flot-data.js')); ?>"></script>
<!-- End sitespy reports -->
