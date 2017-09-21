<style>
#chosensite{width:350px;height: 42px;padding: 10px;}
.act-group{margin-top: 10px;}
.sitedoc-section{border:2px solid green;}
.sitespy-section{border:2px solid orange;}
.sitereview-section{border:2px solid blue;}
.panel-heading{text-align: center;border-bottom-width: 1px;border-bottom-color: green;}
.small-box-footer{float:right;}
</style>
<link href="{{ URL::to('src/js/plugins/morris/morris.css') }}" rel="stylesheet">
<?php if(isset($siteid)!=''){$sid=$siteid;}else{$sid='0';} ?>   
@if( isset($sub_state) && count( $sub_state ) > 0 )
@if( isset($sites) && count( $sites ) > 0 )
<div class="row">

  <div class="sitelist col-md-6">
  <form role="form" id="ReportSite" action="{{ route('user.reportshome') }}" method="post">
     <input type="hidden" name="_token" value="{{ Session::token() }}">
    <h2><i class="fa fa-crosshairs" aria-hidden="true"></i> Your Report Websites</h2>
<select id="chosensite" data-style="btn-info" name="sitename">
 
@foreach( $sites as $site )

<option value="{{$site['id']}}"  @if($sid==$site['id']) selected='selected' @endif>{{ $site['site_name'] }}</option>
 @endforeach

  </select>
  <div class="act-group">
<button type="submit" class="btn btn-info">Go to Reports</button>
</div>
</form> 
  </div>
  <div class="col-md-6">
    <h2><i class="fa fa-binoculars" aria-hidden="true"></i> More</h2>
    <p>To add more websites, Please click <a href="{{ route('user.addsites') }}"> here</a></p>
  </div>
</div>
<div style="margin-top: 10px;" class="col-xs-12"></div>
@else
  <div class="col-md-6">
    <h2><i class="fa fa-pie-chart" aria-hidden="true"></i> Add a website for reports</h2>
    <p>To add more websites, Please click <a href="{{ route('user.addsites') }}"> here</a></p>
  </div>
@endif

@else
<h2>You are yet to add websites! </h2><p>We have below tools for improve your website. Please active a subscription <a href="{{ route('pricing') }}"> here</a> and continue.</p>

     <h2>Our Report Tools</h2>
    <div class="col-md-4">
        <div class="tools-box">
        <a class="btn btn-primary btn-embossed btn-block">
<img src="{{ URL::to('src/images/icons/responsive.svg') }}" />
Site Responsive
        </a>
    </div>
    </div>
     <div class="col-md-4">
        <div class="tools-box">
        <a class="btn btn-primary btn-embossed btn-block">
<img src="{{ URL::to('src/images/icons/flask.svg') }}" />
Site Analysis
        </a>
     </div>
    </div>
      <div class="col-md-4">
        <div class="tools-box">
        <a class="btn btn-primary btn-embossed btn-block">
<img src="{{ URL::to('src/images/icons/search.svg') }}" />
Review Tool
        </a>
     </div>
    </div>

<div class="clearfix" style="margin:10px 0;"></div>
     
    <div class="col-md-4">
        <div class="tools-box">
        <a class="btn btn-primary btn-embossed btn-block">
<img src="{{ URL::to('src/images/icons/rocket.svg') }}" />
SEO Tool
        </a>
    </div>
    </div>
     <div class="col-md-4">
        <div class="tools-box">
        <a class="btn btn-primary btn-embossed btn-block">
<img src="{{ URL::to('src/images/icons/pig.svg') }}" />
Competitor Tool
        </a>
     </div>
     
</div>

@endif
<!-- result of website -->
    @if(isset($from_date))
    <div class="row">      
                
                    <div class="col-lg-12">
                        <div class="panel panel-green">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fa fa-bar-chart-o"></i> Day Wise Unique User Report From <span id="overview_from_date">{{$from_date}}</span> to <span id="overview_to_date">{{$to_date}}</span></h3>
                            </div>
                            <div class="panel-body chart-responsive">
                                <div class="chart" id="flotcontainerqq" style="height: 300px;"></div>
                            </div>
                        </div>
                    </div>
   <script>
/*$(function() {
      Morris.Line({
        element: 'morris-line-chartqq',
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
*/

</script>
 <script type="text/javascript">
 $(document).ready(function() {
 var visits = <?php echo $flot_data_visits; ?>;
	var views = <?php echo $flot_data_views; ?>;
	$('#placeholder').css({
		height: '400px',
		width: '600px'
	});
	$.plot($('#placeholder'),[
			{ label: 'Visits', data: visits },
			{ label: 'Pageviews', data: views }
		],{
	        lines: { show: true },
	        points: { show: true },
	        grid: { backgroundColor: '#fffaff' }
	});
});
</script>
       
        <div style="margin-top: 30px;" class="col-xs-12"></div>

        <div class="col-xs-12 col-sm-12 col-md-5 col-lg-6">
            <div class="info-box" style="border:1px solid #00A65A;border-bottom:2px solid #00A65A;">
                <span class="info-box-icon bg-green"><i class="fa fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text" style="font-size: 20px;">Total Unique Visitor</span>
                    <span class="info-box-number" id="total_unique_visitor">{{$total_unique_visitor}}</span>
                </div><!-- /.info-box-content -->
            </div><!-- /.info-box -->
        </div>
        <div class="col-xs-12 col-sm-12 col-md-5 col-lg-6">
            <div class="info-box" style="border:1px solid #00A65A;border-bottom:2px solid #00A65A;">
                <span class="info-box-icon bg-green"><i class="fa fa-file-text-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text" style="font-size: 20px;">Total Page View</span>
                    <span class="info-box-number" id="total_page_view">{{$total_page_view}}</span>
                </div><!-- /.info-box-content -->
            </div><!-- /.info-box -->
        </div>

        <div style="margin-top: 20px;" class="col-xs-12"></div>
		<div class="col-xs-12 col-sm-12 col-md-5 col-lg-6">
            <div class="info-box" style="border:1px solid #ff851b;border-bottom:2px solid #ff851b;">
                <span class="info-box-icon bg-orange"><i class="fa fa-clock-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text" style="font-size: 20px;">Average Stay Time</span>
                    <span class="info-box-number" id="total_unique_visitor">{{$average_stay_time}}</span>
                </div><!-- /.info-box-content -->
                <!--<a href="{{$tools['sitespy_website']}}/domain_details_visitor/domain_details/{{$domain_id}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
            </div><!-- /.info-box -->
        </div>
        <div class="col-xs-12 col-sm-12 col-md-5 col-lg-6">
            <div class="info-box" style="border:1px solid #ff851b;border-bottom:2px solid #ff851b;">
                <span class="info-box-icon bg-orange"><i class="fa fa-automobile"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text" style="font-size: 20px;">Average Visit</span>
                    <span class="info-box-number" id="total_page_view">{{$average_visit}}</span>
                </div><!-- /.info-box-content -->
                <!--<a href="{{$tools['sitespy_website']}}/domain_details_visitor/domain_details/{{$domain_id}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
            </div><!-- /.info-box -->
        </div>
       
    
    </div>
 @else
 <div class="row">  
    <div class="col-lg-12" style="border:1px solid red;padding:50px;">
        
    Site Analysis Reports Not yet generated!. Please <a href="{{ route('user.paidreports') }}"> goto</a> Primum tools and generate it.
    
</div>
 </div>
 @endif 
<!-- result of website -->
    @if(isset($alx) && !empty($alx))
	<?php
	$cnt=count($alx);
	if($cnt > 1){
	if($alx[0]->reach_rank == $alx[1]->reach_rank){
		$arank='<i class="fa fa-arrow-right fa-2x" aria-hidden="true"></i>';$aran=$alx[0]->reach_rank;$aran2=$alx[1]->reach_rank;$ada=$alx[0]->checked_at;$ada2=$alx[1]->checked_at;
	}
	elseif($alx[0]->reach_rank > $alx[1]->reach_rank){
		$arank='<i class="fa fa-arrow-down fa-2x" aria-hidden="true"></i>';$aran=$alx[0]->reach_rank;$aran2=$alx[1]->reach_rank;$ada=$alx[0]->checked_at;$ada2=$alx[1]->checked_at;
	}else{
		$arank='<i class="fa fa-arrow-up fa-2x" aria-hidden="true"></i>';$aran=$alx[0]->reach_rank;$aran2=$alx[1]->reach_rank;$ada=$alx[0]->checked_at;$ada2=$alx[1]->checked_at;
	}
	
	if($alx[0]->country_rank == $alx[1]->country_rank){
		$crank='<i class="fa fa-arrow-right fa-2x" aria-hidden="true"></i>';$cran=$alx[0]->country_rank;$cran2=$alx[1]->country_rank;$cda=$alx[0]->checked_at;$cda2=$alx[1]->checked_at;$cnam=$alx[0]->country;$cnam2=$alx[1]->country;
	}
	elseif($alx[0]->country_rank > $alx[1]->country_rank){
		$crank='<i class="fa fa-arrow-down fa-2x" aria-hidden="true"></i>';$cran=$alx[0]->country_rank;$cran2=$alx[1]->country_rank;$cda=$alx[0]->checked_at;$cda2=$alx[1]->checked_at;$cnam=$alx[0]->country;$cnam2=$alx[1]->country;
	}else{
		$crank='<i class="fa fa-arrow-up fa-2x" aria-hidden="true"></i>';$cran=$alx[0]->country_rank;$cran2=$alx[1]->country_rank;$cda=$alx[0]->checked_at;$cda2=$alx[1]->checked_at;$cnam=$alx[0]->country;$cnam2=$alx[1]->country;
	}
	
	if($alx[0]->traffic_rank == $alx[1]->traffic_rank){
		$trank='<i class="fa fa-arrow-right fa-2x" aria-hidden="true"></i>';$tran=$alx[0]->traffic_rank;$tran2=$alx[1]->traffic_rank;$tda=$alx[0]->checked_at;$tda2=$alx[1]->checked_at;
	}
	elseif($alx[0]->traffic_rank < $alx[1]->traffic_rank){
		$trank='<i class="fa fa-arrow-down fa-2x" aria-hidden="true"></i>';$tran=$alx[0]->traffic_rank;$tran2=$alx[1]->traffic_rank;$tda=$alx[0]->checked_at;$tda2=$alx[1]->checked_at;
	}
	else{
		$trank='<i class="fa fa-arrow-up fa-2x" aria-hidden="true"></i>';$tran=$alx[0]->traffic_rank;$tran2=$alx[1]->traffic_rank;$tda=$alx[0]->checked_at;$tda2=$alx[1]->checked_at;
	}
	
	}
	else
	{
		$arank='<i class="fa fa-arrow-right fa-2x" aria-hidden="true"></i>';
		$aran=$alx[0]->reach_rank;
		$aran2="";
		$ada=$alx[0]->checked_at;
		$ada2="";
		
		$crank='<i class="fa fa-arrow-right fa-2x" aria-hidden="true"></i>';
		$cran=$alx[0]->country_rank;
		$cran2="";
		$cda=$alx[0]->checked_at;
		$cda2="";
		$cnam=$alx[0]->country;
		$cnam2="";
		
		$trank='<i class="fa fa-arrow-right fa-2x" aria-hidden="true"></i>';
		$tran=$alx[0]->traffic_rank;
		$tran2="";
		$tda=$alx[0]->checked_at;
		$tda2="";
	}
	
    
	?>
	<style>
	.arank .col-md-6{padding:0px;}
	.arank{padding:4px 6px !important;}
	.info-box-number2{font-size:10px;}
	</style>
	<div class="row">  
  <div style="margin-top: 20px;" class="col-xs-12"></div>
		<div class="col-xs-12 col-sm-12 col-md-5 col-lg-4">
            <div class="info-box" style="border:1px solid #3d9970;border-bottom:2px solid #3d9970;">
                <span class="info-box-icon bg-olive"><?php echo $arank ?></span>
                <div class="info-box-content arank">
				 <span class="info-box-text" style="font-size: 20px;">Alexa Rank</span>
					<div class="col-md-6">
                   
                    <span class="info-box-number" id="total_unique_visitor">{{$aran}}</span>
				
					<span class="info-box-number2" id="total_unique_visitor">{{$ada}}</span>
					</div>
					@if(!empty($aran2))
					<div class="col-md-6">
                    
                    <span class="info-box-number" id="total_unique_visitor">{{$aran2}}</span>
					<span class="info-box-number2" id="total_unique_visitor">{{$ada2}}</span>
					</div>
					@endif
                </div><!-- /.info-box-content -->
				
                <!--<a href="{{$tools['sitespy_website']}}/domain_details_visitor/domain_details/{{$domain_id}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
            </div><!-- /.info-box -->
        </div>
        <div class="col-xs-12 col-sm-12 col-md-5 col-lg-4">
            <div class="info-box" style="border:1px solid #3d9970;border-bottom:2px solid #3d9970;">
                <span class="info-box-icon bg-olive"><?php echo $crank ?></span>
                <div class="info-box-content arank">
				 <span class="info-box-text" style="font-size: 20px;">Country Rank</span>
					<div class="col-md-6">
                   
                    <span class="info-box-number" id="total_unique_visitor">{{$cran}}</span>
				
					<span class="info-box-number2" id="total_unique_visitor">{{$cnam}}</span>
					</div>
					@if(!empty($cran2))
					<div class="col-md-6">
                    
                    <span class="info-box-number" id="total_unique_visitor">{{$cran2}}</span>
					<span class="info-box-number2" id="total_unique_visitor">{{$cnam2}}</span>
					</div>
					@endif
                </div><!-- /.info-box-content -->
				
                <!--<a href="{{$tools['sitespy_website']}}/domain_details_visitor/domain_details/{{$domain_id}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
            </div><!-- /.info-box -->
        </div>
		<div class="col-xs-12 col-sm-12 col-md-5 col-lg-4">
            <div class="info-box" style="border:1px solid #3d9970;border-bottom:2px solid #3d9970;">
                <span class="info-box-icon bg-olive"><?php echo $trank ?></span>
                <div class="info-box-content arank">
				 <span class="info-box-text" style="font-size: 20px;">Traffic Rank</span>
					<div class="col-md-6">
                   
                    <span class="info-box-number" id="total_unique_visitor">{{$tran}}</span>
				
					<span class="info-box-number2" id="total_unique_visitor">{{$tda}}</span>
					</div>
					@if(!empty($tran2))
					<div class="col-md-6">
                    
                    <span class="info-box-number" id="total_unique_visitor">{{$tran2}}</span>
					<span class="info-box-number2" id="total_unique_visitor">{{$tda2}}</span>
					</div>
					@endif
                </div><!-- /.info-box-content -->
				
                <!--<a href="{{$tools['sitespy_website']}}/domain_details_visitor/domain_details/{{$domain_id}}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
            </div><!-- /.info-box -->
        </div>
	</div>	
	@endif	
   <div style="margin-top: 30px;" class="col-xs-12"></div>
 @if( isset($site_info) && count( $site_info ) > 0 )       
<!-- Morris Charts -->
                
                <!-- /.row -->
<?php
$count=count($site_info);
$prelast=$site_info[$count-2]['speed_score'];
$last=$site_info[$count-1]['speed_score'];
if($last > $prelast){$c_speed='<i class="fa fa-thumbs-up fa-2x" aria-hidden="true"></i>';$bgc='green';}else{$c_speed='<i class="fa fa-thumbs-down fa-2x" aria-hidden="true"></i>';$bgc='red';}
$prelasta=$site_info[$count-2]['speed_usability_mobile'];
$lasta=$site_info[$count-1]['speed_usability_mobile'];
if($lasta > $prelasta){$c_usescore= '<i class="fa fa-thumbs-up fa-2x" aria-hidden="true"></i>';$bgcs='green';}else{$c_usescore='<i class="fa fa-thumbs-down fa-2x" aria-hidden="true"></i>';$bgcs='red';}
?>
                <div class="row">
                    
                    <div class="col-lg-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading" style="background:{{$bgcs}};">
                                <h3 class="panel-title"><i class="fa fa-long-arrow-right"></i> Site Speed Score <?php echo $c_usescore; ?></h3>
                            </div>
                            <div class="panel-body">
                                <div id="morris-use-barqq"></div>
                                <div class="text-right">
                                   <!-- <a href="{{$tools['sitespy_website']}}/domain/domain_details_view/{{$domain_id}}">View Details <i class="fa fa-arrow-circle-right"></i></a>-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading" style="background:{{$bgc}};">
                                <h3 class="panel-title"><i class="fa fa-long-arrow-right"></i> Site Usability Score <?php echo $c_speed; ?></h3>
                            </div>
                            <div class="panel-body">
                                <div id="flotcontainerww"></div>
                                <div class="text-right">
                                    <!--<a href="{{$tools['sitespy_website']}}/domain/domain_details_view/{{$domain_id}}">View Details <i class="fa fa-arrow-circle-right"></i></a>-->
                                </div>
                            </div>
                        </div>
                    </div>
            </div>        
 @else
 <div class="row">  
    <div class="col-lg-12" style="border:1px solid red;padding:50px;">
        
    Site Checkup Reports Not yet generated!. Please <a href="{{ route('user.paidreports') }}"> goto</a> Primum tools and generate it.
    
</div>
 </div>
 
@endif
<!--
<script src="{{ URL::to('src/js/plugins/morris/morris.js') }}"></script>
<script src="{{ URL::to('src/js/plugins/morris/morris-data.js') }}"></script>
<script src="{{ URL::to('src/js/plugins/morris/raphael.min.js') }}"></script>-->
<script>
/*$(function() {
    // Bar Chart
    Morris.Bar({
        element: 'morris-bar-chartqq',
        data: [
            <?php         
            foreach($site_info as $keys=>$value)
            {
            ?> 
            { score: <?php echo $value->speed_score;?>, date: '<?php echo "$value->searched_at"; ?>' },
            <?php } ?>
        ],
        xkey: 'date',
        ykeys: ['score'],
        labels: ['Score'],
        barRatio: 0.4,
        xLabelAngle: 10,
        hideHover: 'auto',
        gridTextSize:10,
        resize: true
    });
        Morris.Bar({
        element: 'morris-use-barqq',
        data: [
            <?php         
            foreach($site_info as $keys=>$value)
            {
            ?> 
            { score: <?php echo $value->speed_usability_mobile;?>, date: '<?php echo "$value->searched_at"; ?>' },
            <?php } ?>
        ],
        xkey: 'date',
        ykeys: ['score'],
        labels: ['Score'],
        barRatio: 0.4,
        xLabelAngle: 10,
        hideHover: 'auto',
        gridTextSize:10,
        resize: true
    });
});    */
</script>
<script type="text/javascript">
var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
var data9_1 = [
    [1, 1530], [2, 6580], [3, 1980],[4, 6630], [5, 8010], [6, 10800],
    [7, 3530], [8, 7490], [9, 5230],[10, 2580], [11, 6880], [12, 3640]
];
 
$.fn.UseTooltip = function () {
    var previousPoint = null;
     
    $(this).bind("plothover", function (event, pos, item) {         
        if (item) {
            if (previousPoint != item.dataIndex) {
                previousPoint = item.dataIndex;
 
                $("#tooltip").remove();
                 
                var x = item.datapoint[0];
                var y = item.datapoint[1];                
                 
                showTooltip(item.pageX, item.pageY,
                  months[x-  1] + "<br/>" + "<strong>" + y + "</strong> (" + item.series.label + ")");
            }
        }
        else {
            $("#tooltip").remove();
            previousPoint = null;
        }
    });
};
 
function showTooltip(x, y, contents) {
    $('<div id="tooltip">' + contents + '</div>').css({
        position: 'absolute',
        display: 'none',
        top: y + 5,
        left: x + 20,
        border: '2px solid #4572A7',
        padding: '2px',     
        size: '10',   
        'background-color': '#fff',
        opacity: 0.80
    }).appendTo("body").fadeIn(200);
}
 
 
$(function () {        
    $.plot($("#flotcontainerww"),
        [{
              data: data9_1,label: "Page View",lines: { show: true},points: { show: true }
            }
        ],{            
            grid: {
                hoverable: true, 
                clickable: false, 
                mouseActiveRadius: 30,
                backgroundColor: { colors: ["#D1D1D1", "#7A7A7A"] }
            },
            xaxis:{
                   ticks: [
                            [1, "Jan"], [2, "Feb"], [3, "Mar"], [4, "Apr"], [5, "May"], [6, "Jun"],
                            [7, "Jul"], [8, "Aug"], [9, "Sep"], [10, "Oct"], [11, "Nov"], [12, "Dec"]
                   ]
            }     
        }
    );
 
    $("#flotcontainerww").UseTooltip();
});
</script>

<!-- Flot Charts JavaScript -->
<!--[if lte IE 8]><script src="js/excanvas.min.js"></script><![endif]-->
<!--<script src="{{ URL::to('src/js/plugins/flot/jquery.flot.js') }}"></script>
<script src="{{ URL::to('src/js/plugins/flot/jquery.flot.tooltip.min.js') }}"></script>
<script src="{{ URL::to('src/js/plugins/flot/jquery.flot.resize.js') }}"></script>
<script src="{{ URL::to('src/js/plugins/flot/jquery.flot.pie.js') }}"></script>
<script src="{{ URL::to('src/js/plugins/flot/flot-data.js') }}"></script>-->
<!-- End sitespy reports -->
