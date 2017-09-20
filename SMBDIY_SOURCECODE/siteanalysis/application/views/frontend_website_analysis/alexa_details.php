<br/>
<?php 
$alexa_data_full=array();
$alexa_data_full["domain_name"]="";
$alexa_data_full["global_rank"]="";
$alexa_data_full["traffic_rank_graph"]="";
$alexa_data_full["country_rank"]="";
$alexa_data_full["country"]="";
$alexa_data_full["country_name"]=array();
$alexa_data_full["country_percent_visitor"]=array();
$alexa_data_full["country_in_rank"]=array();
$alexa_data_full["bounce_rate"]="";
$alexa_data_full["page_view_per_visitor"]="";
$alexa_data_full["daily_time_on_the_site"]="";
$alexa_data_full["visitor_percent_from_searchengine"]="";
$alexa_data_full["search_engine_percentage_graph"]="";
$alexa_data_full["keyword_name"]=array();
$alexa_data_full["keyword_percent_of_search_traffic"]=array();
$alexa_data_full["upstream_site_name"]=array();
$alexa_data_full["upstream_percent_unique_visits"]=array();
$alexa_data_full["total_site_linking_in"]="";
$alexa_data_full["linking_in_site_name"]=array();
$alexa_data_full["linking_in_site_address"]=array();
$alexa_data_full["subdomain_name"]=array();
$alexa_data_full["subdomain_percent_visitors"]=array();
$alexa_data_full["searched_at"]=array();
if(array_key_exists(0,$alexa_data))
$alexa_data_full=$alexa_data[0];



$domain =$alexa_data_full["domain_name"];
$global_rank =$alexa_data_full["global_rank"];
$country_rank =$alexa_data_full["country_rank"];
$country =$alexa_data_full["country"];
$traffic_rank_graph =$alexa_data_full["traffic_rank_graph"];

$country_name =json_decode($alexa_data_full["country_name"],true);
$country_percent_visitor =json_decode($alexa_data_full["country_percent_visitor"],true);
$country_in_rank = json_decode($alexa_data_full["country_in_rank"],true);

$bounce_rate =$alexa_data_full["bounce_rate"];
$page_view_per_visitor =$alexa_data_full["page_view_per_visitor"];
$daily_time_on_the_site =$alexa_data_full["daily_time_on_the_site"];
$visitor_percent_from_searchengine =$alexa_data_full["visitor_percent_from_searchengine"];
$search_engine_percentage_graph =$alexa_data_full["search_engine_percentage_graph"];            

$keyword_name =json_decode($alexa_data_full["keyword_name"],true);
$keyword_percent_of_search_traffic =json_decode($alexa_data_full["keyword_percent_of_search_traffic"],true);

$upstream_site_name =json_decode($alexa_data_full["upstream_site_name"],true);
$upstream_percent_unique_visits =json_decode($alexa_data_full["upstream_percent_unique_visits"],true);

$total_site_linking_in =$alexa_data_full["total_site_linking_in"];
$linking_in_site_name =json_decode($alexa_data_full["linking_in_site_name"],true);
$linking_in_site_address =json_decode($alexa_data_full["linking_in_site_address"],true);
            
$subdomain_name =json_decode($alexa_data_full["subdomain_name"],true);
$subdomain_percent_visitors=json_decode($alexa_data_full["subdomain_percent_visitors"],true);
// $searched_at=$alexa_data_full["searched_at"];

?>
<div class="container-fluid">

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div style="border:1px solid #0073B7;border-bottom:2px solid #0073B7;" class="info-box">
				<span class="info-box-icon bg-blue"><i class="fa fa-users"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Domain Name</span>
					<span id="total_unique_visitor" class="info-box-number"><?php echo $domain; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>	

		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div style="border:1px solid #00A65A;border-bottom:2px solid #00A65A;" class="info-box">
				<span class="info-box-icon bg-green"><i class="fa fa-users"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Global Rank</span>
					<span id="total_unique_visitor" class="info-box-number"><?php echo $global_rank; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>	

		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div style="border:1px solid #F39C12;border-bottom:2px solid #F39C12;" class="info-box">
				<span class="info-box-icon bg-yellow"><i class="fa fa-users"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Top Country Rank</span>
					<span id="total_unique_visitor" class="info-box-number"><?php echo $country; ?> - <?php echo $country_rank; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>		
	</div>


	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 style="color: blue; word-spacing: 5px;" class="box-title">Alexa Traffic Ranks</h3>
					<div class="box-tools pull-right">
						<button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></button>
						<button data-widget="remove" class="btn btn-box-tool"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<img src="<?php echo $traffic_rank_graph; ?>" alt="Graph not found!" class="img-responsive" style="width:100%">
				</div>
			</div> <!-- end box -->
		</div>
		<div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 style="color: blue; word-spacing: 5px;" class="box-title">Visitors per Country</h3>
					<div class="box-tools pull-right">
						<button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></button>
						<button data-widget="remove" class="btn btn-box-tool"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>SL</th>
								<th>Country</th>
								<th>Percent of Visitors</th>
								<th>Rank in Country</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								$sl=0;                  
					            if(is_array($country_name) && is_array($country_in_rank) && is_array($country_percent_visitor))
					            {
					                foreach($country_name as $key=>$val)
					                {                  
					                    $sl++;
					                    if(array_key_exists($key, $country_name) && array_key_exists($key, $country_in_rank) && array_key_exists($key, $country_percent_visitor))
					                    echo "<tr><td>".$sl."</td>";
					                    echo "<td>".$country_name[$key]."</td>";
					                    echo "<td>".$country_percent_visitor[$key]."</td>";
					                    echo "<td>".$country_in_rank[$key]."</td></tr>";
					                }
					                if(count($country_name)==0 || count($country_in_rank)==0 || count($country_percent_visitor)==0  )
					                echo "<tr><td colspan='4'>No data found!</td></tr>";
					            }
					            else
					            {
					            	echo "<tr><td colspan='4'>No data found!</td></tr>";
					            }

							?>
						</tbody>
					</table>
				</div>
			</div> <!-- end box -->			
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div class="info-box bg-blue">
				<span class="info-box-icon"><i class="fa fa-users"></i></span>
				<div class="info-box-content">
					<!-- <span class="info-box-text">Inventory</span> -->
					<span class="info-box-number"><?php echo $page_view_per_visitor; ?></span>
					<div class="progress">
						<div style="width: 70%" class="progress-bar"></div>
					</div>
					<span class="progress-description">
						<b>Daily Page View per Visitor</b>
					</span>
				</div><!-- /.info-box-content -->
			</div>
		</div>

		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div class="info-box bg-green">
				<span class="info-box-icon"><i class="fa fa-users"></i></span>
				<div class="info-box-content">
					<!-- <span class="info-box-text">Inventory</span> -->
					<span class="info-box-number"><?php echo $daily_time_on_the_site; ?></span>
					<div class="progress">
						<div style="width: 70%" class="progress-bar"></div>
					</div>
					<span class="progress-description">
						<b>Daily Time on Site</b>
					</span>
				</div><!-- /.info-box-content -->
			</div>
		</div>

		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div class="info-box bg-yellow">
				<span class="info-box-icon"><i class="fa fa-users"></i></span>
				<div class="info-box-content">
					<!-- <span class="info-box-text">Inventory</span> -->
					<span class="info-box-number"><?php echo $visitor_percent_from_searchengine; ?></span>
					<div class="progress">
						<div style="width: 70%" class="progress-bar"></div>
					</div>
					<span class="progress-description">
						<b>Visitor % from Search Engines</b>
					</span>
				</div><!-- /.info-box-content -->
			</div>
		</div>
	</div>


	<div class="row">		
		<div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 style="color: blue; word-spacing: 5px;" class="box-title">Top Keywords from Search Engines</h3>
					<div class="box-tools pull-right">
						<button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></button>
						<button data-widget="remove" class="btn btn-box-tool"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<?php                  
			            if(is_array($keyword_name) && is_array($keyword_percent_of_search_traffic))
			            {
			                foreach($keyword_name as $key=>$val)
			                {   
			                    if(array_key_exists($key, $keyword_name) && array_key_exists($key, $keyword_percent_of_search_traffic))
			                    {
			      	             	echo $keyword_name[$key]." <span class='pull-right'><b>".$keyword_percent_of_search_traffic[$key]."</b></span>";
			                    	echo 
			                    	'<div class="progress">					                    	
									  <div class="progress-bar progress-bar-striped " role="progressbar" aria-valuenow="'.$keyword_percent_of_search_traffic[$key].'" aria-valuemin="0" aria-valuemax="100" style="width:'.$keyword_percent_of_search_traffic[$key].'">
									  </div>
									</div>';
				                }
				                if(count($keyword_name)==0 || count($keyword_percent_of_search_traffic)==0 )
			                	echo "No data found!";
			                }
			            }
			            else
			            {
			            	echo "No data found!";
			            }
					?>
				</div>
			</div> <!-- end box -->			
		</div>
		<div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 style="color: blue; word-spacing: 5px;" class="box-title">Search Traffic</h3>
					<div class="box-tools pull-right">
						<button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></button>
						<button data-widget="remove" class="btn btn-box-tool"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<img src="<?php echo $search_engine_percentage_graph; ?>" alt="Graph not found!" class="img-responsive" style="width:100%">
				</div>
			</div> <!-- end box -->
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
			<div class="small-box bg-green">
				<div class="inner">
					<h3><span id="average_stay_time"><?php echo $total_site_linking_in; ?></span></h3>
					<p>Total Linking In Site</p>
				</div>
				<div class="icon">
					<i class="fa fa-clock-o"></i>
				</div>				
			</div>
		</div>

		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
			<div class="small-box bg-red">
				<div class="inner">
					<h3><span id="average_stay_time"><?php echo $bounce_rate; ?></span></h3>
					<p>Bounce Rate</p>
				</div>
				<div class="icon">
					<i class="fa fa-clock-o"></i>
				</div>				
			</div>
		</div>
	</div>



	<div class="row">	

	<div class="col-xs-12">
		<div class="box box-primary">
				<div class="box-header with-border">
					<h3 style="color: blue; word-spacing: 5px;" class="box-title"> Linking In Statistics</h3>
					<div class="box-tools pull-right">
						<button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></button>
						<button data-widget="remove" class="btn btn-box-tool"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>SL</th>
								<th>Site</th>
								<th>Page</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								$sl=0;                  
					            if(is_array($linking_in_site_name) && is_array($linking_in_site_address))
					            {
					                foreach($linking_in_site_name as $key=>$val)
					                {                   
					                    $sl++;
					                    if(array_key_exists($key, $linking_in_site_name) && array_key_exists($key, $linking_in_site_address))
					                    {
					                    	echo "<tr><td>".$sl."</td>";
						                    echo "<td>".$linking_in_site_name[$key]."</td>";
						                    echo "<td>".$linking_in_site_address[$key]."</td>";
						                }
						                if(count($linking_in_site_name)==0 || count($linking_in_site_address)==0)
					                	echo "<tr><td colspan='4'>No data found!</td></tr>";
					                }
					            }
					            else
					            {
					            	echo "<tr><td colspan='4'>No data found!</td></tr>";
					            }

							?>
						</tbody>
					</table>
				</div>
			</div> <!-- end box -->			
		</div>	
		
	</div>

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 style="color: blue; word-spacing: 5px;" class="box-title">Upstream Sites</h3>
					<div class="box-tools pull-right">
						<button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></button>
						<button data-widget="remove" class="btn btn-box-tool"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<?php                  
			            if(is_array($upstream_site_name) && is_array($upstream_percent_unique_visits))
			            {
			                foreach($upstream_site_name as $key=>$val)
			                {   
			                    if(array_key_exists($key, $upstream_site_name) && array_key_exists($key, $upstream_percent_unique_visits))
			                    {
			      	             	echo $upstream_site_name[$key]." <span class='pull-right'>Unique Visit: <b>".$upstream_percent_unique_visits[$key]."</b></span>";
			                    	echo 
			                    	'<div class="progress">					                    	
									  <div class="progress-bar progress-bar-striped " role="progressbar" aria-valuenow="'.$upstream_percent_unique_visits[$key].'" aria-valuemin="0" aria-valuemax="100" style="width:'.$upstream_percent_unique_visits[$key].'">
									  </div>
									</div>';
				                }
				                if(count($upstream_site_name)==0 || count($upstream_percent_unique_visits)==0 )
			                	echo "No data found!";
			                }
			            }
			            else
			            {
			            	echo "No data found!";
			            }
					?>
				</div>
			</div> <!-- end box -->			
		</div>
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 style="color: blue; word-spacing: 5px;" class="box-title">Subdomain Statistics</h3>
					<div class="box-tools pull-right">
						<button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></button>
						<button data-widget="remove" class="btn btn-box-tool"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<?php                  
			            if(is_array($subdomain_name) && is_array($subdomain_percent_visitors))
			            {
			                foreach($subdomain_name as $key=>$val)
			                {   
			                    if(array_key_exists($key, $subdomain_name) && array_key_exists($key, $subdomain_percent_visitors))
			                    {
			      	             	echo $subdomain_name[$key]." <span class='pull-right'>Visitor: <b>".$subdomain_percent_visitors[$key]."</b></span>";
			                    	echo 
			                    	'<div class="progress">					                    	
									  <div class="progress-bar progress-bar-striped " role="progressbar" aria-valuenow="'.$subdomain_percent_visitors[$key].'" aria-valuemin="0" aria-valuemax="100" style="width:'.$subdomain_percent_visitors[$key].'">
									  </div>
									</div>';
				                }
				                if(count($subdomain_name)==0 || count($subdomain_percent_visitors)==0 )
			                	echo "No data found!";
			                }
			            }
			            else
			            {
			            	echo "No data found!";
			            }
			        ?>
			           
				</div>
			</div> <!-- end box -->
		</div>
	</div>
	
</div>




<style type="text/css" media="screen">
	th{font-family:Arial;}
	.box-body{min-height:270px !important;}
	.progress{margin-bottom:10px;}
</style>