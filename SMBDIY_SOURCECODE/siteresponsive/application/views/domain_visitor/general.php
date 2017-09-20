<style type="text/css">
	.box-body-content{
		margin-bottom: 5px;
		padding-left: 15px;
		font-size: 16px;
		color:#2B4150;
		/*border: 1px solid gray;*/
	}
</style>

<div role="tabpanel" class="tab-pane fade in active" id="general">
	<div id="general_success_msg" class="text-center" ></div>	
	<div id="general_name"></div>
	
	<div id="hide_after_ajax">
		<div class="row">
			<div class="col-xs-12">
				<h3><div class="well text-center"><?php echo "Domain Name - ".$domain_info[0]['domain_name']; ?></div></h3>
			</div>
		</div>

		<div class="row" style="padding-top:10px;">
			<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
				<div class="box bg-blue box-solid">
					<div class="box-header with-border">
						<h3 class="box-title"><i class="fa fa-street-view"></i> &nbsp&nbsp Who Is Information</h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						</div><!-- /.box-tools -->
					</div><!-- /.box-header -->
					<div class="box-body" style="background: white; color: black; padding-bottom: 28px;">
						<table class="table table-hover table-condensed">
							<tr>
								<td>Registered</td>
								<td>
									<?php 
										if($domain_info[0]['whois_is_registered'] == 'yes')
											echo '<span class="label label-success">Yes</span>'; 
										else
											echo '<span class="label label-danger">No</span>';
									?> 
								</td>
							</tr>
							<tr>
								<td>Tech Email</td>
								<td><?php echo $domain_info[0]['whois_tech_email']; ?></td>
							</tr>
							<tr>
								<td>Name Servers</td>
								<td><?php echo $domain_info[0]['whois_name_servers']; ?></td>
							</tr>
							<tr>
								<td>Created At</td>
								<td><?php echo date("d-M-Y",strtotime($domain_info[0]['whois_created_at'])); ?></td>
							</tr>
							<tr>
								<td>Changed At</td>
								<td><?php echo date("d-M-Y",strtotime($domain_info[0]['whois_changed_at'])); ?></td>
							</tr>
							<tr>
								<td>Expire At</td>
								<td><?php echo date("d-M-Y",strtotime($domain_info[0]['whois_expire_at'])); ?></td>
							</tr>
							<tr>
								<td>Sponsor</td>
								<td><?php echo $domain_info[0]['whois_sponsor']; ?></td>
							</tr>
							<tr>
								<td>Registrant Name</td>
								<td><?php echo $domain_info[0]['whois_registrant_name']; ?></td>
							</tr>
							<tr>
								<td>Admin Name</td>
								<td><?php echo $domain_info[0]['whois_admin_name']; ?></td>
							</tr>
							<tr>
								<td>Registrant Email</td>
								<td><?php echo $domain_info[0]['whois_registrant_email']; ?></td>
							</tr>
							<tr>
								<td>Admin Email</td>
								<td><?php echo $domain_info[0]['whois_admin_email']; ?></td>
							</tr>
							<tr>
								<td>Registrant Country</td>
								<td><?php echo $domain_info[0]['whois_registrant_country']; ?></td>
							</tr>
							<tr>
								<td>Admin Country</td>
								<td><?php echo $domain_info[0]['whois_admin_country']; ?></td>
							</tr>
							<tr>
								<td>Registrant Phone</td>
								<td><?php echo $domain_info[0]['whois_registrant_phone']; ?></td>
							</tr>
							<tr>
								<td>Admin Phone</td>
								<td><?php echo $domain_info[0]['whois_admin_phone']; ?></td>
							</tr>						
						</table>					
					</div><!-- /.box-body -->
				</div><!-- /.box -->

			</div>

			<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
				
				<div class="row">
					<div class="col-xs-12">
						<div class="info-box" style="border:1px solid #00A65A;border-bottom:2px solid #00A65A;">
							<span class="info-box-icon bg-green"><i class="fa fa-google"></i></span>
							<div class="info-box-content">								
								<table class="table table-hover table-condensed" style="margin-bottom: 0px;margin-top: 10px;">
									<tr>
										<td>Google Back Link</td>
										<td><?php echo $domain_info[0]['google_back_link_count']; ?></td>
									</tr>
									<tr>
										<td>Google Page Rank</td>
										<td><?php echo $domain_info[0]['google_page_rank']."/10"; ?></td>
									</tr>
								</table>
							</div><!-- /.info-box-content -->
						</div><!-- /.info-box -->
					</div>					
				</div>


				<div class="row">
					<div class="col-xs-12">
						<div class="box box-primary">
							<div class="box-header with-border">
								<h3 class="box-title" style="color: blue; word-spacing: 5px;">MOZ Information</h3>
								<div class="box-tools pull-right">
									<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
								</div><!-- /.box-tools -->
							</div><!-- /.box-header -->
							<div class="box-body">
								<table class="table table-condensed table-striped">
									<tr class="active">
										<td>Subdomain Normalized</td>
										<td><?php echo $domain_info[0]['moz_subdomain_normalized']; ?></td>
									</tr>
									<tr class="">
										<td>Subdomain Raw</td>
										<td><?php echo $domain_info[0]['moz_subdomain_raw']; ?></td>
									</tr>
									<tr class="success">
										<td>URL Normalized</td>
										<td><?php echo $domain_info[0]['moz_url_normalized']; ?></td>
									</tr>
									<tr class="">
										<td>URL Raw</td>
										<td><?php echo $domain_info[0]['moz_url_raw']; ?></td>
									</tr>
									<tr class="info">
										<td>HTTP Status Code</td>
										<td><?php echo $domain_info[0]['moz_http_status_code']; ?></td>
									</tr>
									<tr class="">
										<td>Domain Authority</td>
										<td><?php echo $domain_info[0]['moz_domain_authority']; ?></td>
									</tr>
									<tr class="warning">
										<td>Page Authority</td>
										<td><?php echo $domain_info[0]['moz_page_authority']; ?></td>
									</tr>
									<tr class="">
										<td>External Quality Link</td>
										<td><?php echo $domain_info[0]['moz_external_equity_links']; ?></td>
									</tr>
									<tr class="active">
										<td>Links</td>
										<td><?php echo $domain_info[0]['moz_links']; ?></td>
									</tr>
								</table>
							</div><!-- /.box-body -->
						</div><!-- /.box -->
					</div>				
				</div>

				<div class="well row" style="border-left:2px solid orange;-webkit-border-radius:5px;border-radius:5px;margin-left:0px;margin-right:0px;">
					<div class="col-lg-4"><div class="box-body-content"> DMOZ Listing</div></div>
					<div class="col-lg-8">
						<div class="box-body-content">: 
							<span id="dmoz_listed_or_not"> 
							<?php 
								if($domain_info[0]['dmoz_listed_or_not'] == 'no') 
									echo '<span class="label label-danger">No</span>'; 
								if($domain_info[0]['dmoz_listed_or_not'] == 'yes') 
									echo '<span class="label label-success">Yes</span>';
							?> 
							</span>
						</div>
					</div>
				</div>
			</div>

		</div>

		<div class="row" style="padding-top:10px;">
			<div class="col-xs-12">
				<div class="box bg-blue box-solid">
					<div class="box-header with-border">
						<h3 class="box-title">IP Information</h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						</div><!-- /.box-tools -->
					</div><!-- /.box-header -->
					<div class="box-body" style="background: white; color: black;">
						<table class="table table-condensed table-striped">
							<tr>
								<th>ISP</th>
								<th>IP</th>
								<th>Country</th>
								<th>City</th>
								<th>Region</th>
								<th>Timezone</th>
								<th>Latitude</th>
								<th>Longitude</th>
							</tr>
							<tr>
								<td><?php echo $domain_info[0]['ipinfo_isp']; ?></td>
								<td><?php echo $domain_info[0]['ipinfo_ip']; ?></td>
								<td><?php echo $domain_info[0]['ipinfo_country']; ?></td>
								<td><?php echo $domain_info[0]['ipinfo_city']; ?></td>
								<td><?php echo $domain_info[0]['ipinfo_region']; ?></td>
								<td><?php echo $domain_info[0]['ipinfo_time_zone']; ?></td>
								<td><?php echo $domain_info[0]['ipinfo_latitude']; ?></td>
								<td><?php echo $domain_info[0]['ipinfo_longitude']; ?></td>
							</tr>
						</table>					
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>		
		</div>

		<div class="row" style="padding-top: 10px;">
			<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
				<div class="row">
					<div class="col-xs-12">
						<div class="box box-warning">
							<div class="box-header with-border">
								<h3 class="box-title" style="color: blue;">Antivirus Scan Info</h3>
								<div class="box-tools pull-right">
									<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
								</div><!-- /.box-tools -->
							</div><!-- /.box-header -->
							<div class="box-body">
								<table class="table table-condensed table-striped">
									<tr>
										<th>Google Status</th>
										<th>McAfee Status</th>
										<th>AVG Status</th>
										<th>Norton Status</th>
									</tr>
									<tr>
										<td><?php echo $domain_info[0]['google_safety_status']; ?></td>
										<td><?php echo $domain_info[0]['macafee_status']; ?></td>
										<td><?php echo $domain_info[0]['avg_status']; ?></td>
										<td><?php echo $domain_info[0]['norton_status']; ?></td>
									</tr>
								</table>
							</div><!-- /.box-body -->
						</div><!-- /.box -->
					</div>				
				</div>
			</div>
			<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
				<div class="row">
					<div class="col-xs-12">
						<div class="box box-danger">
							<div class="box-header with-border">
								<h3 class="box-title" style="color: blue;">Search Engine Index Info</h3>
								<div class="box-tools pull-right">
									<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
								</div><!-- /.box-tools -->
							</div><!-- /.box-header -->
							<div class="box-body">
								<table class="table table-condensed table-striped">
									<tr>
										<th>Google Index</th>
										<th>Bing Index</th>
										<th>Yahoo Index</th>
									</tr>
									<tr>
										<td><?php echo $domain_info[0]['google_index_count']; ?></td>
										<td><?php echo $domain_info[0]['bing_index_count']; ?></td>
										<td><?php echo $domain_info[0]['yahoo_index_count']; ?></td>
									</tr>
								</table>
							</div><!-- /.box-body -->
						</div><!-- /.box -->
					</div>				
				</div>
			</div>
		</div>

	</div>
</div>

