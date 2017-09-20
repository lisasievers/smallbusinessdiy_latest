<div class="container-fluid">
	<div class="well text-center" style="margin-top: 5px;padding:5px 0 8px 0 !important"><p style="color: #55AAFF; font-size: 28px; font-weight: bold;"><?php echo $this->lang->line("Recent Activities"); ?></p></div>
	
	<?php if($this->session->userdata("user_type")=="Member") {?>
	<div class="row">
		<div class="col-xs-12 col-md-6">
			<div class="info-box bg-aqua">
				<span class="info-box-icon"><i class="fa fa-cube"></i></span>
				<div class="info-box-content">
					<!-- <span class="info-box-text">Inventory</span> -->
					<span class="info-box-number">
					   <?php if($price=="Trial") $price=0; ?>
					   <?php echo $package_name;?> @
					   <?php echo $payment_config[0]['currency']; ?> <?php echo $price;?> /
					   <?php echo $validity;?> <?php echo $this->lang->line("days")?>	
					</span>
					<div class="progress">
						<div style="width: 70%" class="progress-bar"></div>
					</div>
					<span class="progress-description">
						<b><?php echo $this->lang->line("package name")?></b>
					</span>
				</div><!-- /.info-box-content -->
			</div>	
		</div>
		<div class="col-xs-12 col-md-6">
			<div class="info-box bg-blue">
				<span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
				<div class="info-box-content">
					<!-- <span class="info-box-text">Inventory</span> -->
					<span class="info-box-number">
					    <?php echo date("Y-m-d",strtotime($this->session->userdata("expiry_date"))); ?>
					</span>
					<div class="progress">
						<div style="width: 70%" class="progress-bar"></div>
					</div>
					<span class="progress-description">
						<b><?php echo $this->lang->line("expired date")?></b>
					</span>
				</div><!-- /.info-box-content -->
			</div>	
		</div>
	</div>
	<?php } ?>
	
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-warning box-solid">
				<div class="box-header with-border">
				<h3 class="box-title"><i class="fa fa-flag"></i> <?php echo $this->lang->line("Today's Country Wise New Visitor Report - Top Five"); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body chart-responsive">
					<input type="hidden" id="country_chart_data_1" value='<?php if(isset($country_chart_data_1)) echo $country_chart_data_1; ?>'/>
					<input type="hidden" id="country_chart_data_2" value='<?php if(isset($country_chart_data_2)) echo $country_chart_data_2; ?>'/>
					<input type="hidden" id="country_chart_data_3" value='<?php if(isset($country_chart_data_3)) echo $country_chart_data_3; ?>'/>

					<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<div class="row">
							<div class="col-md-9 col-xs-12">
								<div class="chart-responsive">
									<canvas id="country_type_pieChart_1" height="220"></canvas>
								</div><!-- ./chart-responsive -->
							</div><!-- /.col -->
							<div class="col-md-3 col-xs-12" style="padding-top:35px;" id="country_name_list_1">
								<?php if(isset($country_name_list_1)) echo $country_name_list_1; ?>
							</div><!-- /.col -->
						</div><!-- /.row -->
						<div class="well text-center" id="country_domain_name_1"></div>
					</div>

					<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<div class="row">
							<div class="col-md-9 col-xs-12">
								<div class="chart-responsive">
									<canvas id="country_type_pieChart_2" height="220"></canvas>
								</div><!-- ./chart-responsive -->
							</div><!-- /.col -->
							<div class="col-md-3 col-xs-12" style="padding-top:35px;" id="country_name_list_2">
								<?php if(isset($country_name_list_2)) echo $country_name_list_2; ?>
							</div><!-- /.col -->
						</div><!-- /.row -->
						<div class="well text-center" id="country_domain_name_2"></div>
					</div>

					<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<div class="row">
							<div class="col-md-9 col-xs-12">
								<div class="chart-responsive">
									<canvas id="country_type_pieChart_3" height="220"></canvas>
								</div><!-- ./chart-responsive -->
							</div><!-- /.col -->
							<div class="col-md-3 col-xs-12" style="padding-top:35px;" id="country_name_list_3">
								<?php if(isset($country_name_list_3)) echo $country_name_list_3; ?>
							</div><!-- /.col -->
						</div><!-- /.row -->
						<div class="well text-center" id="country_domain_name_3"></div>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>
	</div>


	<div class="row">
		<div class="col-xs-12">
			<div class="box box-danger box-solid">
				<div class="box-header with-border">
				<h3 class="box-title"><i class="fa fa-users"></i> <?php echo $this->lang->line("Today's New Vs Returning Users Report"); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body chart-responsive">
					<input type="hidden" id="pie_chart_data_1" value='<?php if(isset($pie_chart_data_1)) echo $pie_chart_data_1; ?>'/>
					<input type="hidden" id="pie_chart_data_2" value='<?php if(isset($pie_chart_data_2)) echo $pie_chart_data_2; ?>'/>
					<input type="hidden" id="pie_chart_data_3" value='<?php if(isset($pie_chart_data_3)) echo $pie_chart_data_3; ?>'/>

					<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<div class="row">
							<div class="col-md-8 col-xs-12">
								<div class="chart-responsive">
									<canvas id="visitor_type_pieChart_1" height="220"></canvas>
								</div><!-- ./chart-responsive -->
							</div><!-- /.col -->
							<div class="col-md-4 col-xs-12" style="padding-top:35px;">
								<ul class="chart-legend clearfix" id="visitor_type_color_codes">
									<li><i class="fa fa-circle-o" style="color: #C8EBE5;"></i> New User</li>
			                        <li><i class="fa fa-circle-o" style="color: #F5A196;"></i> Returning User</li>
								</ul>
							</div><!-- /.col -->
						</div><!-- /.row -->
						<div class="well text-center" id="domain_name_1"></div>
					</div>

					<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<div class="row">
							<div class="col-md-8 col-xs-12">
								<div class="chart-responsive">
									<canvas id="visitor_type_pieChart_2" height="220"></canvas>
								</div><!-- ./chart-responsive -->
							</div><!-- /.col -->
							<div class="col-md-4 col-xs-12" style="padding-top:35px;">
								<ul class="chart-legend clearfix" id="visitor_type_color_codes">
									<li><i class="fa fa-circle-o" style="color: #51A39D;"></i> New User</li>
			                        <li><i class="fa fa-circle-o" style="color: #B7695C;"></i> Returning User</li>
								</ul>
							</div><!-- /.col -->
						</div><!-- /.row -->
						<div class="well text-center" id="domain_name_2"></div>
					</div>

					<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<div class="row">
							<div class="col-md-8 col-xs-12">
								<div class="chart-responsive">
									<canvas id="visitor_type_pieChart_3" height="220"></canvas>
								</div><!-- ./chart-responsive -->
							</div><!-- /.col -->
							<div class="col-md-4 col-xs-12" style="padding-top:35px;">
								<ul class="chart-legend clearfix" id="visitor_type_color_codes">
									<li><i class="fa fa-circle-o" style="color: #C8EBE5;"></i> New User</li>
			                        <li><i class="fa fa-circle-o" style="color: #F5A196;"></i> Returning User</li>
								</ul>
							</div><!-- /.col -->
						</div><!-- /.row -->
						<div class="well text-center" id="domain_name_3"></div>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>
	</div>


	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary box-solid">
				<div class="box-header with-border">
				<h3 class="box-title"><i class="fa fa-line-chart"></i> <?php echo $this->lang->line("Visitor Analysis"); ?> (<?php echo $this->lang->line("Today's New Visitor Report"); ?>)</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body chart-responsive">
					<input type="hidden" id="bar_chart_data" value='<?php echo $bar_chart_data; ?>'/>
					<div class="chart" id="dashboard_bar_chart" style="height: 300px;"></div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">			
			<div class="box" style="border-top: 3px solid #3C8DBC;">
				<div class="box-header with-border">
					<h3 class="box-title" style="color: #198EC8"><i class="fa fa-line-chart" style="color: #E05A17;"></i> <?php echo $this->lang->line("Visitor Analysis"); ?> (<?php echo $this->lang->line("Today's New Visitor Report"); ?>)</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body table-responsive">
					<?php 
						if(empty($visitor_analysis)) echo "<h3 class='text-center'>No data to show</h3>";
						else {
					?>

					<table class="table table-hover table-striped">
						<tr>
							<th>Domain Name</th>
							<th>New Visitor</th>
							<th>Details</th>
						</tr>
						<?php $i=0; foreach($visitor_analysis as $value): ?>
							<tr>
								<td><?php echo $value['domain_name']; ?></td>
								<td><?php echo $value['new_user']; ?></td>
								<td><a target="_blank" href="<?php echo base_url('domain_details_visitor/domain_details').'/'.$value['id']; ?>"><i class="fa fa-binoculars"></i> View Details</a></td>
							</tr>
						<?php $i++; if($i==5) break; endforeach; ?>
					</table>
					<?php } ?>
					<div class="text-center" style="margin-top: 5px;"><span class="label label-primary"><a href="<?php echo base_url('domain_details_visitor/domain_list_visitor'); ?>" style="color: white" target="_blank"><i class="fa fa-arrow-circle-right"></i> More Info</a></span></div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->			
		</div>
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">			
			<div class="box" style="border-top: 3px solid #3C8DBC;">
				<div class="box-header with-border">
					<h3 class="box-title" style="color: #198EC8"><i class="fa fa-bar-chart" style="color: #E05A17;"></i> <?php echo $this->lang->line("Website Analysis"); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">					
					<?php 
						if(empty($website_analysis)) echo "<h3 class='text-center'>No data to show</h3>";
						else {
					?>

					<table class="table table-hover table-striped">
						<tr>
							<th>Name</th>
							<th>Details</th>
						</tr>
						<?php foreach($website_analysis as $value): ?>
							<tr>
								<td><?php echo $value['domain_name']; ?></td>
								<td><a target="_blank" href="<?php echo base_url('domain/domain_details_view').'/'.$value['id']; ?>"><i class="fa fa-binoculars"></i> View Details</a></td>
							</tr>
						<?php endforeach; ?>
					</table>
					<?php } ?>
					<div class="text-center" style="margin-top: 5px;"><span class="label label-primary"><a href="<?php echo base_url('domain/domain_list_for_domain_details'); ?>" style="color: white" target="_blank"><i class="fa fa-arrow-circle-right"></i> More Info</a></span></div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->			
		</div>
	</div>
<!-- search engine index and google page rank -->
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">			
			<div class="box" style="border-top: 3px solid #8ABA45;">
				<div class="box-header with-border">
					<h3 class="box-title" style="color: #198EC8"><i class="fa fa-list-ol" style="color: #E05A17;"></i> <?php echo $this->lang->line("Search Engine Index"); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
					<?php 
						if(empty($search_engine_index)) echo "<h3 class='text-center'>No data to show</h3>";
						else {
					?>

					<table class="table table-hover table-striped">
						<tr>
							<th>Name</th>
							<th><i class="fa fa-google" style="color: orange;"></i> Google Index</th>
							<th><i class="fa fa-windows" style="color: orange;"></i> Bing Index</th>
							<th><i class="fa fa-yahoo" style="color: orange;"></i> Yahoo Index</th>
						</tr>
						<?php foreach($search_engine_index as $value): ?>
							<tr>
								<td><?php echo $value['domain_name']; ?></td>
								<td><?php echo $value['google_index']; ?></td>
								<td><?php echo $value['bing_index']; ?></td>
								<td><?php echo $value['yahoo_index']; ?></td>
								
							</tr>
						<?php endforeach; ?>
					</table>
					<?php } ?>
					<div class="text-center" style="margin-top: 5px;"><span class="label label-primary"><a href="<?php echo base_url('search_engine_index/index'); ?>" style="color: white" target="_blank"><i class="fa fa-arrow-circle-right"></i> More Info</a></span></div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->			
		</div>
		
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">			
			<div class="box" style="border-top: 3px solid #8ABA45;">
				<div class="box-header with-border">
					<h3 class="box-title" style="color: #198EC8"><i class="fa fa-link" style="color: #E05A17;"></i><?php echo $this->lang->line("Google Backlink Search"); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
					<?php 
						if(empty($backlink_search)) echo "<h3 class='text-center'>No data to show</h3>";
						else {
					?>

					<table class="table table-hover table-striped">
						<tr>
							<th>Name</th>
							<th>Backlink Count</th>
						</tr>
						<?php foreach($backlink_search as $value): ?>
							<tr>
								<td><?php echo $value['domain_name'];?></td>
								<td><?php echo $value['backlink_count'];?></td>
							</tr>
						<?php endforeach; ?>
					</table>
					<?php } ?>
					<div class="text-center" style="margin-top: 5px;"><span class="label label-primary"><a href="<?php echo base_url('backlink/backlink_search'); ?>" style="color: white" target="_blank"><i class="fa fa-arrow-circle-right"></i> More Info</a></span></div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->			
		</div>
	</div>
<!-- social network and whois search -->
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">			
			<div class="box" style="border-top: 3px solid #EA4335;">
				<div class="box-header with-border">
					<h3 class="box-title" style="color: #198EC8"><i class="fa fa-share-alt" style="color: #E05A17;"></i> <?php echo $this->lang->line("Social Network Analysis"); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
					<?php 
						if(empty($social_info)) echo "<h3 class='text-center'>No data to show</h3>";
						else {
					?>

					<table class="table table-hover table-striped">
						<tr>
							<th>Name</th>
						</tr>
						<?php foreach($social_info as $value): ?>
							<tr>
								<td><?php echo $value['domain_name']; ?></td>							
							</tr>
						<?php endforeach; ?>
					</table>
					<?php } ?>
					<div class="text-center" style="margin-top: 5px;"><span class="label label-primary"><a href="<?php echo base_url('social/social_list'); ?>" style="color: white" target="_blank"><i class="fa fa-arrow-circle-right"></i> More Info</a></span></div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->			
		</div>
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">			
			<div class="box" style="border-top: 3px solid #EA4335;">
				<div class="box-header with-border">
					<h3 class="box-title" style="color: #198EC8"><i class="fa fa-street-view" style="color: #E05A17;"></i> <?php echo $this->lang->line("Whois Search"); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
					<?php 
						if(empty($whois_search)) echo "<h3 class='text-center'>No data to show</h3>";
						else {
					?>

					<table class="table table-hover table-striped">
						<tr>
							<th>Name</th>
							<th>Is Registered</th>
						</tr>
						<?php foreach($whois_search as $value): ?>
							<tr>
								<td><?php echo $value['domain_name']; ?></td>
								<td><?php if($value['is_registered'] == 'no') echo "<span class='label laben-danger'>No</span>";
											else echo "<span class='label label-success'>Yes</span>";
								  ?>
								</td>								
							</tr>
						<?php endforeach; ?>
					</table>
					<?php } ?>
					<div class="text-center" style="margin-top: 5px;"><span class="label label-primary"><a href="<?php echo base_url('who_id/index'); ?>" style="color: white" target="_blank"><i class="fa fa-arrow-circle-right"></i> More Info</a></span></div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->			
		</div>
	</div>

	

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">			
			<div class="box" style="border-top: 3px solid #FF6EB0;">
				<div class="box-header with-border">
					<h3 class="box-title" style="color: #198EC8"><i class="fa fa-shield"></i> <?php echo $this->lang->line("Malware Scan"); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
					<?php 
						if(empty($malware_scan)) echo "<h3 class='text-center'>No data to show</h3>";
						else {
					?>

					<table class="table table-hover table-striped">
						<tr>
							<th>Name</th>
							<th>Google Status</th>
							<th>McAfee Status</th>
							<th>AVG Status</th>
							<th>Norton Status </th>
						</tr>
						<?php foreach($malware_scan as $value): ?>
							<tr>
								<td><?php echo $value['domain_name']; ?></td>
								<td><?php echo $value['google_status']; ?></td>
								<td><?php echo $value['macafee_status']; ?></td>
								<td><?php echo $value['avg_status']; ?></td>
								<td><?php echo $value['norton_status']; ?></td>
							</tr>
						<?php endforeach; ?>
					</table>
					<?php } ?>
					<div class="text-center" style="margin-top: 5px;"><span class="label label-primary"><a href="<?php echo base_url('antivirus/scan'); ?>" style="color: white" target="_blank"><i class="fa fa-arrow-circle-right"></i> More Info</a></span></div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->			
		</div>
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">			
			<div class="box" style="border-top: 3px solid #FF6EB0;">
				<div class="box-header with-border">
					<h3 class="box-title" style="color: #198EC8"><img style="height:18px;margin-right: 5px;" src="<?php echo base_url();?>assets/images/Similarweb.png" alt="SiteSpy" class="pull-left img-responsive"> <?php echo $this->lang->line("SimilarWeb Data"); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
					<?php 
						if(empty($similar_web_info)) echo "<h3 class='text-center'>No data to show</h3>";
						else {
					?>

					<table class="table table-hover table-striped">
						<tr>
							<th>Name</th>
							<th>Global Rank</th>
							<th>Details</th>
						</tr>
						<?php foreach($similar_web_info as $value): ?>
							<tr>
								<td><?php echo $value['domain_name']; ?></td>
								<td><?php echo $value['global_rank']; ?></td>
								<td><a target="_blank" href="<?php echo base_url('rank/similar_web_details').'/'.$value['id']; ?>"><i class="fa fa-binoculars"></i> View Details</a></td>
							</tr>
						<?php endforeach; ?>
					</table>
					<?php } ?>
					<div class="text-center" style="margin-top: 5px;"><span class="label label-primary"><a href="<?php echo base_url('rank/similar_web'); ?>" style="color: white" target="_blank"><i class="fa fa-arrow-circle-right"></i> More Info</a></span></div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->			
		</div>
	</div>





<!-- alexa data -->
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">			
			<div class="box" style="border-top: 3px solid #F5BC14;">
				<div class="box-header with-border">
					<h3 class="box-title" style="color: #198EC8"><img style="height:18px;margin-right: 5px;" src="<?php echo base_url();?>assets/images/Alexa.png" alt="SiteSpy" class="pull-left img-responsive"> <?php echo $this->lang->line("Alexa Data"); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
					<?php 
						if(empty($alexa_info_full)) echo "<h3 class='text-center'>No data to show</h3>";
						else {
					?>

					<table class="table table-hover table-striped">
						<tr>
							<th>Name</th>
							<th>Details</th>
						</tr>
						<?php foreach($alexa_info_full as $value): ?>
							<tr>
								<td><?php echo $value['domain_name']; ?></td>
								<td><a target="_blank" href="<?php echo base_url('rank/alexa_details').'/'.$value['id']; ?>"><i class="fa fa-binoculars"></i> View Details</a></td>
							</tr>
						<?php endforeach; ?>
					</table>
					<?php } ?>
					<div class="text-center" style="margin-top: 5px;"><span class="label label-primary"><a href="<?php echo base_url('rank/alexa_rank_full'); ?>" style="color: white" target="_blank"><i class="fa fa-arrow-circle-right"></i> More Info</a></span></div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->			
		</div>
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">			
			<div class="box" style="border-top: 3px solid #F5BC14;">
				<div class="box-header with-border">
					<h3 class="box-title" style="color: #198EC8"><img style="height:18px;margin-right: 5px;" src="<?php echo base_url();?>assets/images/Alexa.png" alt="SiteSpy" class="pull-left img-responsive"><?php echo $this->lang->line("Alexa Rank"); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
					<?php 
						if(empty($alexa_rank)) echo "<h3 class='text-center'>No data to show</h3>";
						else {
					?>

					<table class="table table-hover table-striped">
						<tr>
							<th>Name</th>
							<th>Global Rank</th>
						</tr>
						<?php foreach($alexa_rank as $value): ?>
							<tr>
								<td><?php echo $value['domain_name']; ?></td>
								<td><?php echo $value['global_rank']; ?></td>
							</tr>
						<?php endforeach; ?>
					</table>
					<?php } ?>
					<div class="text-center" style="margin-top: 5px;"><span class="label label-primary"><a href="<?php echo base_url('rank/alexa_rank'); ?>" style="color: white" target="_blank"><i class="fa fa-arrow-circle-right"></i> More Info</a></span></div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->			
		</div>
	</div>
<!-- moz and dmoz -->
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">			
			<div class="box" style="border-top: 3px solid #AA7FFF;">
				<div class="box-header with-border">
					<h3 class="box-title" style="color: #198EC8"><img style="height:22px;margin-right: 5px;" src="<?php echo base_url();?>assets/images/moz.png" alt="SiteSpy" class="pull-left img-responsive"><?php echo $this->lang->line("MOZ Check"); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
					<?php 
						if(empty($moz_info)) echo "<h3 class='text-center'>No data to show</h3>";
						else {
					?>

					<table class="table table-hover table-striped">
						<tr>
							<th>Name</th>
							<th>DA</th>
							<th>PA</th>
						</tr>
						<?php foreach($moz_info as $value): ?>
							<tr>
								<td><?php echo $value['url']; ?></td>
								<td><?php echo $value['domain_authority']; ?></td>
								<td><?php echo $value['page_authority']; ?></td>
							</tr>
						<?php endforeach; ?>
					</table>
					<?php } ?>
					<div class="text-center" style="margin-top: 5px;"><span class="label label-primary"><a href="<?php echo base_url('rank/moz_rank'); ?>" style="color: white" target="_blank"><i class="fa fa-arrow-circle-right"></i> More Info</a></span></div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->			
		</div>
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">			
			<div class="box" style="border-top: 3px solid #AA7FFF;">
				<div class="box-header with-border">
					<h3 class="box-title" style="color: #198EC8"><img style="height:18px;margin-right: 5px;" src="<?php echo base_url();?>assets/images/dmoz.png" alt="SiteSpy" class="pull-left img-responsive"><?php echo $this->lang->line("DMOZ Check"); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
					<?php 
						if(empty($dmoz_info)) echo "<h3 class='text-center'>No data to show</h3>";
						else {
					?>

					<table class="table table-hover table-striped">
						<tr>
							<th>Name</th>
							<th>Is Listed</th>
						</tr>
						<?php foreach($dmoz_info as $value): ?>
							<tr>
								<td><?php echo $value['domain_name']; ?></td>
								<td><?php if($value['listed_or_not'] == 'no') echo "<span class='label label-danger'>No</span>";
											else echo "<span class='label label-success'>Yes</span>";
								  ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</table>
					<?php } ?>
					<div class="text-center" style="margin-top: 5px;"><span class="label label-primary"><a href="<?php echo base_url('rank/dmoz_rank'); ?>" style="color: white" target="_blank"><i class="fa fa-arrow-circle-right"></i> More Info</a></span></div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->			
		</div>
	</div>
<!-- domain ip and search in same ip -->
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">			
			<div class="box" style="border-top: 3px solid #0F9957;">
				<div class="box-header with-border">
					<h3 class="box-title" style="color: #198EC8"><i class="fa fa-map-marker" style="color: #E05A17;"></i> <?php echo $this->lang->line("Domain IP Information"); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
					<?php 
						if(empty($domain_ip_check)) echo "<h3 class='text-center'>No data to show</h3>";
						else {
					?>

					<table class="table table-hover table-striped">
						<tr>
							<th>Name</th>
							<th>IP</th>
						</tr>
						<?php foreach($domain_ip_check as $value): ?>
							<tr>
								<td><?php echo $value['domain_name']; ?></td>
								<td><?php echo $value['ip'];?></td>
							</tr>
						<?php endforeach; ?>
					</table>
					<?php } ?>
					<div class="text-center" style="margin-top: 5px;"><span class="label label-primary"><a href="<?php echo base_url('ip/domain_info'); ?>" style="color: white" target="_blank"><i class="fa fa-arrow-circle-right"></i> More Info</a></span></div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->			
		</div>
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">			
			<div class="box" style="border-top: 3px solid #0F9957;">
				<div class="box-header with-border">
					<h3 class="box-title" style="color: #198EC8"><i class="fa fa-map-marker" style="color: #E05A17;"></i> <?php echo $this->lang->line("Sites In Same IP"); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
					<?php 
						if(empty($ip_in_same_site)) echo "<h3 class='text-center'>No data to show</h3>";
						else {
					?>

					<table class="table table-hover table-striped">
						<tr>
							<th>IP</th>
						</tr>
						<?php foreach($ip_in_same_site as $value): ?>
							<tr>
								<td><?php echo $value['ip'];?></td>
							</tr>
						<?php endforeach; ?>
					</table>
					<?php } ?>
					<div class="text-center" style="margin-top: 5px;"><span class="label label-primary"><a href="<?php echo base_url('ip/site_this_ip'); ?>" style="color: white" target="_blank"><i class="fa fa-arrow-circle-right"></i> More Info</a></span></div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->			
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">			
			<div class="box" style="border-top: 3px solid #B84B9E;">
				<div class="box-header with-border">
					<h3 class="box-title" style="color: #198EC8"><i class="fa fa-tags" style="color: #E05A17;"></i> <?php echo $this->lang->line("Keyword Position Analysis"); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body table-responsive">
					<?php 
						if(empty($keyword_position)) echo "<h3 class='text-center'>No data to show</h3>";
						else {
					?>

					<table class="table table-hover table-striped">
						<tr>
							<th>Name</th>
							<th>Keyword</th>
							<th>Google Position</th>
							<th>Bing Position</th>
							<th>Yahoo Position</th>
						</tr>
						<?php foreach($keyword_position as $value): ?>
							<tr>
								<td><?php echo $value['domain_name'];?></td>
								<td><?php echo $value['keyword'];?></td>
								<td><?php echo $value['google_position'];?></td>
								<td><?php echo $value['bing_position'];?></td>
								<td><?php echo $value['yahoo_position'];?></td>
							</tr>
						<?php endforeach; ?>
					</table>
					<?php } ?>
					<div class="text-center" style="margin-top: 5px;"><span class="label label-primary"><a href="<?php echo base_url('keyword/index'); ?>" style="color: white" target="_blank"><i class="fa fa-arrow-circle-right"></i> More Info</a></span></div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->			
		</div>
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">			
			<div class="box" style="border-top: 3px solid #B84B9E;">
				<div class="box-header with-border">
					<h3 class="box-title" style="color: #198EC8"><i class="fa fa-tags" style="color: #E05A17;"></i> <?php echo $this->lang->line("Keyword Auto Suggestion"); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
					<?php 
						if(empty($keyword_suggestion)) echo "<h3 class='text-center'>No data to show</h3>";
						else {
					?>

					<table class="table table-hover table-striped">
						<tr>
							<th>Keyword</th>
						</tr>
						<?php foreach($keyword_suggestion as $value): ?>
							<tr>
								<td><?php echo $value['keyword'];?></td>
							</tr>
						<?php endforeach; ?>
					</table>
					<?php } ?>
					<div class="text-center" style="margin-top: 5px;"><span class="label label-primary"><a href="<?php echo base_url('keyword/keyword_suggestion'); ?>" style="color: white" target="_blank"><i class="fa fa-arrow-circle-right"></i> More Info</a></span></div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->			
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">			
			<div class="box" style="border-top: 3px solid #3C8DBC;">
				<div class="box-header with-border">
					<h3 class="box-title" style="color: #198EC8"><i class="fa fa-anchor" style="color: #E05A17;"></i> <?php echo $this->lang->line("Link Analyzer"); ?></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div><!-- /.box-header -->
				<div class="box-body">
					<?php 
						if(empty($link_analysis)) echo "<h3 class='text-center'>No data to show</h3>";
						else {
					?>

					<table class="table table-hover table-striped">
						<tr>
							<th>Name</th>
							<th>External Link Count</th>
							<th>Internal Link Count</th>
						</tr>
						<?php foreach($link_analysis as $value): ?>
							<tr>
								<td><?php echo $value['url'];?></td>
								<td><?php echo $value['external_link_count'];?></td>
								<td><?php echo $value['internal_link_count'];?></td>
							</tr>
						<?php endforeach; ?>
					</table>
					<?php } ?>
					<div class="text-center" style="margin-top: 5px;"><span class="label label-primary"><a href="<?php echo base_url('link_analysis/index'); ?>" style="color: white" target="_blank"><i class="fa fa-arrow-circle-right"></i> More Info</a></span></div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->			
		</div>
		
	</div>

</div>

<input type="hidden" id="website_name_1" value="<?php if(isset($website_name_1)) echo $website_name_1; ?>">
<input type="hidden" id="website_name_2" value="<?php if(isset($website_name_2)) echo $website_name_2; ?>">
<input type="hidden" id="website_name_3" value="<?php if(isset($website_name_3)) echo $website_name_3; ?>">

<script type="text/javascript">
	$j("document").ready(function(){
		var domain_name_1 = $("#website_name_1").val();
		$("#domain_name_1").text(domain_name_1);
		$("#country_domain_name_1").text(domain_name_1);
		var domain_name_2 = $("#website_name_2").val();
		$("#domain_name_2").text(domain_name_2);
		$("#country_domain_name_2").text(domain_name_2);
		var domain_name_3 = $("#website_name_3").val();
		$("#domain_name_3").text(domain_name_3);
		$("#country_domain_name_3").text(domain_name_3);

		var bar_chart_data = $("#bar_chart_data").val();
		var bar = new Morris.Bar({
          element: 'dashboard_bar_chart',
          resize: true,
          data: JSON.parse(bar_chart_data),
          barColors: ['#624E84'],
          xkey: 'source_name',
          ykeys: ['value'],
          labels: ['New Visitor'],
          hideHover: 'auto'
        });

        var pieOptions = {
          //Boolean - Whether we should show a stroke on each segment
          segmentShowStroke: true,
          //String - The colour of each segment stroke
          segmentStrokeColor: "#fff",
          //Number - The width of each segment stroke
          segmentStrokeWidth: 2,
          //Number - The percentage of the chart that we cut out of the middle
          percentageInnerCutout: 10, // This is 0 for Pie charts
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
          maintainAspectRatio: false
        };

        //-------------
		  //- PIE CHART -
		  //-------------
		  // Get context with jQuery - using jQuery's .get() method.
		  var pieChartCanvas = $("#visitor_type_pieChart_1").get(0).getContext("2d");
		  var pieChart = new Chart(pieChartCanvas);
		  var pie_chart_data_1 = $("#pie_chart_data_1").val();
		  if(pie_chart_data_1 != "" && pie_chart_data_1 != "undefined"){
			  var PieData1 = JSON.parse(pie_chart_data_1);			  
			  // You can switch between pie and douhnut using the method below.  
			  pieChart.Doughnut(PieData1, pieOptions);
			  //-----------------
			  //- END PIE CHART -
			  //-----------------
		  }


		  //-------------
		  //- PIE CHART -
		  //-------------
		  // Get context with jQuery - using jQuery's .get() method.
		  var pieChartCanvas = $("#country_type_pieChart_1").get(0).getContext("2d");
		  var pieChart = new Chart(pieChartCanvas);
		  var country_chart_data_1 = $("#country_chart_data_1").val();
		  if(country_chart_data_1 != "" && country_chart_data_1 != "undefined"){
			  var countryData1 = JSON.parse(country_chart_data_1);			  
			  // You can switch between pie and douhnut using the method below.  
			  pieChart.Doughnut(countryData1, pieOptions);
			  //-----------------
			  //- END PIE CHART -
			  //-----------------
		  }



		  //-------------
		  //- PIE CHART -
		  //-------------
		  // Get context with jQuery - using jQuery's .get() method.
		  var pieChartCanvas = $("#visitor_type_pieChart_2").get(0).getContext("2d");
		  var pieChart = new Chart(pieChartCanvas);
		  var pie_chart_data_2 = $("#pie_chart_data_2").val();
		  if(pie_chart_data_2 != "" && pie_chart_data_2 != "undefined"){

			  var PieData2 = JSON.parse(pie_chart_data_2);
			  
			  // You can switch between pie and douhnut using the method below.  
			  pieChart.Doughnut(PieData2, pieOptions);
			  //-----------------
			  //- END PIE CHART -
			  //-----------------
		  }


		  //-------------
		  //- PIE CHART -
		  //-------------
		  // Get context with jQuery - using jQuery's .get() method.
		  var pieChartCanvas = $("#country_type_pieChart_2").get(0).getContext("2d");
		  var pieChart = new Chart(pieChartCanvas);
		  var country_chart_data_2 = $("#country_chart_data_2").val();
		  if(country_chart_data_2 != "" && country_chart_data_2 != "undefined"){
			  var countryData2 = JSON.parse(country_chart_data_2);			  
			  // You can switch between pie and douhnut using the method below.  
			  pieChart.Doughnut(countryData2, pieOptions);
			  //-----------------
			  //- END PIE CHART -
			  //-----------------
		  }



		  //-------------
		  //- PIE CHART -
		  //-------------
		  // Get context with jQuery - using jQuery's .get() method.
		  var pieChartCanvas = $("#visitor_type_pieChart_3").get(0).getContext("2d");
		  var pieChart = new Chart(pieChartCanvas);
		  var pie_chart_data_3 = $("#pie_chart_data_3").val();
		  if(pie_chart_data_3 != "" && pie_chart_data_3 != "undefined"){		  	
			  var PieData3 = JSON.parse(pie_chart_data_3);			  
			  // You can switch between pie and douhnut using the method below.  
			  pieChart.Doughnut(PieData3, pieOptions);
			  //-----------------
			  //- END PIE CHART -
			  //-----------------
		  }



		  //-------------
		  //- PIE CHART -
		  //-------------
		  // Get context with jQuery - using jQuery's .get() method.
		  var pieChartCanvas = $("#country_type_pieChart_3").get(0).getContext("2d");
		  var pieChart = new Chart(pieChartCanvas);
		  var country_chart_data_3 = $("#country_chart_data_3").val();
		  if(country_chart_data_3 != "" && country_chart_data_3 != "undefined"){
			  var countryData3 = JSON.parse(country_chart_data_3);			  
			  // You can switch between pie and douhnut using the method below.  
			  pieChart.Doughnut(countryData3, pieOptions);
			  //-----------------
			  //- END PIE CHART -
			  //-----------------
		  }





	});
</script>