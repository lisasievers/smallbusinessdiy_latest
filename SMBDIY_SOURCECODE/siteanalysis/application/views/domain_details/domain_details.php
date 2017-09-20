<style type="text/css">
	.tabs-below > .nav-tabs,
	.tabs-right > .nav-tabs,
	.tabs-left > .nav-tabs {
	  border-top: 0;
	}

	.tab-content > .tab-pane,
	.pill-content > .pill-pane {
	  display: none;
	}

	.tab-content > .active,
	.pill-content > .active {
	  display: block;
	}

	.tabs-below > .nav-tabs {
	  border-top: 1px solid #fff;
	}

	.tabs-below > .nav-tabs > li {
	  margin-bottom: -1px;
	  margin-top: 0;
	}

	.tabs-below > .nav-tabs > li > a {
	  -webkit-border-radius: 4px 4px 0 0;
	     -moz-border-radius: 4px 4px 0 0;
	          border-radius: 4px 4px 0 0;
	}

	.tabs-below > .nav-tabs > li > a:hover,
	.tabs-below > .nav-tabs > li > a:focus {
	  border-bottom-color: transparent;
	  border-top-color: #ddd;
	  border-top:2px solid orange;
	}

	.tabs-below > .nav-tabs > .active > a,
	.tabs-below > .nav-tabs > .active > a:hover,
	.tabs-below > .nav-tabs > .active > a:focus {
	  border-color: #ddd #ddd transparent #ddd;
	  border-top:2px solid orange;
	}
</style>
<input type="hidden" id="domain_id" value="<?php echo $id; ?>"/>
<section class="content">
	<div class="row">
		<div class="col-xs-12">			
			<div class="tabs-below">
				<!-- Nav tabs -->
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active"><a href="#general" aria-controls="general" role="tab" data-toggle="tab">General</a></li>
					<li role="presentation" ><a href="#social_network" aria-controls="social_network" role="tab" data-toggle="tab">Social Network Information</a></li>
					<li role="presentation" ><a href="#meta_tag_info" aria-controls="meta_tag_info" role="tab" data-toggle="tab">Keyword & Meta Information</a></li>
					<li role="presentation" ><a href="#alexa_info" aria-controls="alexa_info" role="tab" data-toggle="tab">Alexa Information</a></li>
					<li role="presentation" ><a href="#similarweb_info" aria-controls="similarweb_info" role="tab" data-toggle="tab">SimilarWeb Information</a></li>
					<!-- <li role="presentation" data-toggle="offcanvas"><a href="#visitor_analysis" aria-controls="visitor_analysis" role="tab" data-toggle="tab">Visitor Analysis</a></li> -->
				</ul>

				<!-- Tab panes -->
				<div class="tab-content">					
					<?php $this->load->view('domain_details/general'); ?>
					<?php  $this->load->view('domain_details/alexa_info'); ?>				
					<?php  $this->load->view('domain_details/similarweb_info'); ?>				
					<?php $this->load->view("domain_details/social_network"); ?>
					<?php $this->load->view("domain_details/meta_tag_info"); ?>
					<?php //$this->load->view('domain/visitor_analysis'); ?>					
				</div>
			</div>
		</div>
	</div>
</section>

<script type="text/javascript">
	$('.reservation').daterangepicker();

	var function_name;

	$j("document").ready(function(){
		$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			e.preventDefault();
			var target = $(e.target).attr("href");
			function_name = target.replace('#','');
			ajax_call(function_name);		

		}); // end of $('a[data-toggle="tab"]')


		$(document.body).on('click','.search_button',function(){
			ajax_call(function_name);			
		});	


		function ajax_call(function_name)
		{
			var domain_id = $("#domain_id").val();
			var date_range = $("#"+function_name+"_date").val();

			if(function_name == 'visitor_analysis')
				date_range = $("#overview_date").val();

			var base_url="<?php echo base_url(); ?>";
			var data_type = "JSON";
			if(function_name == 'alexa_info' || function_name == 'general' || function_name == 'browser_report' || function_name == 'similarweb_info' || function_name == 'os_report' || function_name == 'device_report' || function_name == 'meta_tag_info')
				data_type = '';
			$('#'+function_name+'_success_msg').html('<img class="center-block" style="margin-top:10px;" src="'+base_url+'assets/pre-loader/Fancy pants.gif" alt="Searching...">');

			$.ajax({
				type: "POST",
				url : "<?php echo site_url('domain/ajax_get_"+function_name+"_data/'); ?>",
				data:{domain_id:domain_id,date_range:date_range},
				dataType: data_type,
				async: false,
				success:function(response){
					$('#'+function_name+'_success_msg').html('');
					$("#"+function_name+"_name").html(response);
					var pieOptions = {
					    //Boolean - Whether we should show a stroke on each segment
					    segmentShowStroke: true,
					    //String - The colour of each segment stroke
					    segmentStrokeColor: "#fff",
					    //Number - The width of each segment stroke
					    segmentStrokeWidth: 1,
					    //Number - The percentage of the chart that we cut out of the middle
					    percentageInnerCutout: 30, // This is 0 for Pie charts
					    //Number - Amount of animation steps
					    animationSteps: 100,
					    //String - Animation easing effect
					    animationEasing: "easeOutBounce",
					    //Boolean - Whether we animate the rotation of the Doughnut
					    animateRotate: true,
					    //Boolean - Whether we animate scaling the Doughnut from the centre
					    animateScale: false,
					    //Boolean - whether to make the chart responsive to window resizing
					    responsive: true,
					    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
					    maintainAspectRatio: false,
					    //String - A tooltip template
					    tooltipTemplate: "<%=value %> <%=label%>"
					};

					/*************** for general page **********************/
					if(function_name == 'general'){
						$("#hide_after_ajax").hide();
					}
					/************** end of general page *******************/

					// for alexa information page
					// if (function_name == 'alexa_info') {

					// }
					// end of alexa information

					/********************* for social network page *************************/
					if (function_name == 'social_network') {
						var pieChartCanvas = $("#social_network_pieChart").get(0).getContext("2d");
						var pieChart = new Chart(pieChartCanvas);
						var PieData = response.social_network_pieChart;

						pieChart.Doughnut(PieData, pieOptions);

						$(".domain_name").text(response.domain_name);

						$("#color_codes").html(response.color_codes);
						$("#fb_total_like").text(response.fb_total_like);
						$("#fb_total_comment").text(response.fb_total_comment);
						$("#fb_total_share").text(response.fb_total_share);

						$("#stumbleupon_total_like").text(response.stumbleupon_total_like);
						$("#stumbleupon_total_comment").text(response.stumbleupon_total_comment);
						$("#stumbleupon_total_view").text(response.stumbleupon_total_view);
						$("#stumbleupon_total_list").text(response.stumbleupon_total_list);

						$("#reddit_score").text(response.reddit_score);
						$("#reddit_ups").text(response.reddit_ups);
						$("#reddit_downs").text(response.reddit_downs);

						$("#google_plus_share").text(response.google_plus_share);
						$("#pinterest_pin").text(response.pinterest_pin);
						$("#buffer_share").text(response.buffer_share);
						$("#xing_share").text(response.xing_share);
						$("#linkedin_share").text(response.linkedin_share);


					}
					/******************* end of social network page **********************/


					/********************* visitor analysis page ******************/
					if (function_name == 'visitor_analysis') {
						$('#overview_from_date').text(response.from_date);
						$('#overview_to_date').text(response.to_date);
						//this domain name will be placed at all the pages of visitor analysis tab
				        $(".domain_name").text(response.domain_name);

						/*** daily visitor line chart ***/
				        var line = new Morris.Line({
				          element: 'line-chart',
				          resize: true,
				          data: response.line_chart,
				          xkey: 'date',
				          ykeys: ['user'],
				          labels: ['New User'],
				          lineColors: ['#3c8dbc'],
				          hideHover: 'auto'
				        });
				        /*********************/
				        $('#total_page_view').text(response.total_page_view);
				        $('#total_unique_visitor').text(response.total_unique_visitor);
				        $('#average_stay_time').text(response.average_stay_time);
				        $('#average_visit').text(response.average_visit);
					}
					/**************** end of visitor analysis page ****************/


					/***************** overview page ******************/
					if (function_name == 'overview') {
						$('#overview_from_date').text(response.from_date);
						$('#overview_to_date').text(response.to_date);
						// LINE CHART
				        var line = new Morris.Line({
				          element: 'line-chart',
				          resize: true,
				          data: response.line_chart,
				          xkey: 'date',
				          ykeys: ['user'],
				          labels: ['New User'],
				          lineColors: ['#3c8dbc'],
				          hideHover: 'auto'
				        });
				        $('#total_page_view').text(response.total_page_view);
				        $('#total_unique_visitor').text(response.total_unique_visitor);
				        $('#average_stay_time').text(response.average_stay_time);
				        $('#average_visit').text(response.average_visit);
					}
					/********************* end of overview page *****************/


					/******************** for traffic source page *******************/ 
					if(function_name=='traffic_source') {
						
						$('#traffic_source_from_date').text(response.from_date);
						$('#traffic_source_to_date').text(response.to_date);
						/*** daily traffic line chart ***/
						var area = new Morris.Area({
						    element: 'traffic_line-chart',
						    resize: true,
						    data: response.line_chart_data,
						    xkey: 'date',
						    ykeys: ['direct_link', 'search_link', 'social_link', 'referrer_link'],
						    labels: ['Direct Link', 'Search Engine', 'Social Network', 'Referal'],
						    lineColors: ['#74828F', '#96C0CE', '#BEB9B5', '#C25B56' ],
						    hideHover: 'auto'
						  });
						/****************************/

						/*** Top referrer pie chart ***/
						var donut = new Morris.Donut({
						    element: 'top_referrer_chart',
						    resize: true,
						    colors: response.top_referrer_color,
						    data: response.top_referrer_data,
						    hideHover: 'auto'
						  });
						/******************************/


						/*** Total traffic bar chart ***/
				        //BAR CHART
				        var bar = new Morris.Bar({
				          element: 'bar-chart',
				          resize: true,
				          data: response.bar_chart_data,
				          barColors: ['#F8DEBD'],
				          xkey: 'source_name',
				          ykeys: ['value'],
				          labels: ['Visitor'],
				          hideHover: 'auto'
				        });
						/******************************/


						/**** Traffic from search engines ***/
						var pieChartCanvas = $("#search_engine_traffic_pieChart").get(0).getContext("2d");
						var pieChart = new Chart(pieChartCanvas);
						var PieData = response.search_engine_info;

						pieChart.Doughnut(PieData, pieOptions);
						$("#search_engine_traffic_color_codes").html(response.search_engine_names);
						/*****************************************/

						/**** Traffic from social networks ***/
						var pieChartCanvas = $("#social_network_traffic_pieChart").get(0).getContext("2d");
						var pieChart = new Chart(pieChartCanvas);
						var PieData = response.social_network_info;
						pieChart.Doughnut(PieData, pieOptions);
						$("#social_network_traffic_color_codes").html(response.social_network_names);

					}
					
					/****************** end of traffic source page ***********************/



					/****************** for visitor type page ****************************/
					if(function_name == 'visitor_type'){
						$('#visitor_type_from_date').text(response.from_date);
						$('#visitor_type_to_date').text(response.to_date);
						
						//BAR CHART
				        var bar = new Morris.Bar({
				          element: 'visitor_type_bar-chart',
				          resize: true,
				          data: response.daily_new_vs_returning,
				          barColors: ['#74828F', '#96C0CE'],
				          xkey: 'date',
				          ykeys: ['new_user', 'returning_user'],
				          labels: ['New User', 'Returning User'],
				          hideHover: 'auto'
				        });

				        //-------------
					  //- PIE CHART -
					  //-------------
					  // Get context with jQuery - using jQuery's .get() method.
					  var pieChartCanvas = $("#visitor_type_pieChart").get(0).getContext("2d");
					  var pieChart = new Chart(pieChartCanvas);
					  var PieData = response.total_new_returning;
					  
					  // You can switch between pie and douhnut using the method below.  
					  pieChart.Doughnut(PieData, pieOptions);
					  //-----------------
					  //- END PIE CHART -
					  //-----------------
					}
					/****************** end of visitor type page *************************/

					/************************ content over view page********************************/
					if(function_name == 'content_overview'){
						$('#content_overview_from_date').text(response.from_date);
						$('#content_overview_to_date').text(response.to_date);
						$('#content_overview_data').html(response.progress_bar_data);
					}
					/************************ end content over view page********************************/

					/**************** country wise report page ********************/
					if(function_name == 'country_wise_report'){
						$("#country_wise_visitor_from_date").text(response.from_date);
						$("#country_wise_visitor_to_date").text(response.to_date);
						$("#country_wise_table_data").html(response.country_wise_table_data);
						
						function drawMap() {
							var data = google.visualization.arrayToDataTable(response.country_graph_data);

							var options = {};
							options['dataMode'] = 'regions';

							var container = document.getElementById('regions_div');
							var geomap = new google.visualization.GeoMap(container);

							geomap.draw(data, options);
						};

						google.charts.load('current', {'packages':['geomap']});
						google.charts.setOnLoadCallback(drawMap);
					}
					/**************** end country wise report page ********************/
					
				} //end of success

			}); // end of ajax
		} //end of function ajax_call



		$(document.body).on('click','.browser_name',function(){
			var domain_id = $("#domain_id").val();
			var browser_name = $(this).attr("data");
			var date_range = $("#browser_report_date").val();
			var base_url="<?php echo base_url(); ?>";
			$("#individual_browser_data_table").html('');
			$("#id_for_browser_name").text(browser_name);
			$("#modal_for_browser_report").modal();
			$('#modal_waiting_browser_name').html('<img class="center-block" style="margin-top:10px;margin-bottom:15px;" src="'+base_url+'assets/pre-loader/Fancy pants.gif" alt="Searching...">');

			$.ajax({
				type: "POST",
				url : "<?php echo site_url('domain/ajax_get_individual_browser_data'); ?>",
				data:{domain_id:domain_id,date_range:date_range,browser_name:browser_name},
				dataType: 'JSON',
				async: false,
				success:function(response){
					$("#modal_waiting_browser_name").html('');
					$("#browser_name_from_date").text(response.from_date);
					$("#browser_name_to_date").text(response.to_date);
					$("#individual_browser_data_table").html(response.browser_version);
					var line = new Morris.Line({
					    element: 'browser_name_line_chart',
					    resize: true,
					    data: response.browser_daily_session_data,
					    xkey: 'date',
					    ykeys: ['session'],
					    labels: ['Sessions'],
					    lineColors: ['#3c8dbc'],
					    hideHover: 'auto'
					  });					
				} //end of success
			});
		}); // end of browser name click function



		$(document.body).on('click','.os_name',function(){
			var domain_id = $("#domain_id").val();
			var os_name = $(this).attr("data");
			var date_range = $("#os_report_date").val();
			var base_url="<?php echo base_url(); ?>";
			$("#id_for_os_name").text(os_name);
			$("#modal_for_os_report").modal();
			$('#modal_waiting_os_name').html('<img class="center-block" style="margin-top:10px;margin-bottom:15px;" src="'+base_url+'assets/pre-loader/Fancy pants.gif" alt="Searching...">');

			$.ajax({
				type: "POST",
				url : "<?php echo site_url('domain/ajax_get_individual_os_data'); ?>",
				data:{domain_id:domain_id,date_range:date_range,os_name:os_name},
				dataType: 'JSON',
				async: false,
				success:function(response){
					$("#modal_waiting_os_name").html('');
					$("#os_name_from_date").text(response.from_date);
					$("#os_name_to_date").text(response.to_date);
					var line = new Morris.Line({
					    element: 'os_name_line_chart',
					    resize: true,
					    data: response.os_daily_session_data,
					    xkey: 'date',
					    ykeys: ['session'],
					    labels: ['Sessions'],
					    lineColors: ['#00AAA0'],
					    hideHover: 'auto'
					  });					
				} //end of success
			});
		}); //end of os name click function



		$(document.body).on('click','.device_name',function(){
			var domain_id = $("#domain_id").val();
			var device_name = $(this).attr("data");
			var date_range = $("#device_report_date").val();
			var base_url="<?php echo base_url(); ?>";
			$("#id_for_device_name").text(device_name);
			$("#modal_for_device_report").modal();
			$('#modal_waiting_device_name').html('<img class="center-block" style="margin-top:10px;margin-bottom:15px;" src="'+base_url+'assets/pre-loader/Fancy pants.gif" alt="Searching...">');

			$.ajax({
				type: "POST",
				url : "<?php echo site_url('domain/ajax_get_individual_device_data'); ?>",
				data:{domain_id:domain_id,date_range:date_range,device_name:device_name},
				dataType: 'JSON',
				async: false,
				success:function(response){
					$("#modal_waiting_device_name").html('');
					$("#device_name_from_date").text(response.from_date);
					$("#device_name_to_date").text(response.to_date);
					var line = new Morris.Line({
					    element: 'device_name_line_chart',
					    resize: true,
					    data: response.device_daily_session_data,
					    xkey: 'date',
					    ykeys: ['session'],
					    labels: ['Sessions'],
					    lineColors: ['#FF7A5A'],
					    hideHover: 'auto'
					  });					
				} //end of success
			});
		}); //end of device name click function


		$(document.body).on('click','.country_wise_name',function(){
			var domain_id = $("#domain_id").val();
			var country_name = $(this).attr("data");
			var date_range = $("#country_wise_report_date").val();
			var base_url="<?php echo base_url(); ?>";
			$("#individual_country_data_table").html('');
			$("#id_for_country_name").text(country_name);
			$("#modal_for_country_report").modal();
			$('#modal_waiting_country_name').html('<img class="center-block" style="margin-top:10px;margin-bottom:15px;" src="'+base_url+'assets/pre-loader/Fancy pants.gif" alt="Searching...">');

			$.ajax({
				type: "POST",
				url : "<?php echo site_url('domain/ajax_get_individual_country_data'); ?>",
				data:{domain_id:domain_id,date_range:date_range,country_name:country_name},
				dataType: 'JSON',
				async: false,
				success:function(response){
					$("#modal_waiting_country_name").html('');
					$("#country_name_from_date").text(response.from_date);
					$("#country_name_to_date").text(response.to_date);
					$("#individual_country_data_table").html(response.country_city_str);
					var line = new Morris.Line({
					    element: 'country_name_line_chart',
					    resize: true,
					    data: response.country_daily_session_data,
					    xkey: 'date',
					    ykeys: ['session'],
					    labels: ['Sessions'],
					    lineColors: ['#3c8dbc'],
					    hideHover: 'auto'
					  });					
				} //end of success
			});
		}); // end of browser name click function


	});
</script>
