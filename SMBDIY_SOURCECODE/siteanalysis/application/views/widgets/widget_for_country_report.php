<!-- Bootstrap 3.3.4 -->
<link href="<?php echo base_url();?>bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<!-- FontAwesome 4.3.0 -->
<link href="<?php echo base_url()."assets/css/font-awesome.css" ?>" rel="stylesheet" type="text/css" />
<!-- Theme style -->
<link href="<?php echo base_url();?>css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
<!-- jQuery 2.1.4 -->
<script src="<?php echo base_url();?>plugins/jQuery/jQuery-2.1.4.min.js"></script>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div class="col-xs-12">
			<div class="box box-warning">
				<div class="box-header with-border" style="background: #3C8DBC;">
					<h3 style="color: white; word-spacing: 3px;" class="box-title"><i class="fa fa-flag"></i> Country Wise New Visitor Report For Last 30 Days</h3>
					<div class="box-tools pull-right">
						<span class="logo-lg">
							<a href="<?php echo base_url(); ?>">
								<img style="height:28px;" src="<?php echo base_url();?>assets/images/logo.png" alt="SiteSpy" class="img-responsive">
		  					</a>
		  				</span>
					</div>
				</div>
				<div class="box-body chart-responsive text-center" style="background: gray;">
					<div id="country_regions_div">
						
					</div>
				</div>
			</div> <!-- end box -->
			<input type="hidden" id="country_json_data" value='<?php echo $country_graph_data; ?>' />		
		</div>	
<script>
$("document").ready(function(){
	function drawMap() {
		var country_graph_data = $("#country_json_data").val();
		var data = google.visualization.arrayToDataTable(JSON.parse(country_graph_data));

		var options = {};
		options['dataMode'] = 'regions';

		var container = document.getElementById('country_regions_div');
		var geomap = new google.visualization.GeoMap(container);

		geomap.draw(data, options);
	};

	google.charts.load('current', {'packages':['geomap']});
	google.charts.setOnLoadCallback(drawMap);
});

</script>