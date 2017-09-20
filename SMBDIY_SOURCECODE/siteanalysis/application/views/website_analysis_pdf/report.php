<?php 
echo "<!DOCTYPE html><html><head>";
include("application/views/website_analysis_pdf/report_css_js.php");
echo "</head><body>";


$path = 'assets/images/logo.png';
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
echo "<div class='text-center'><img style='width:200px;' src='".$base64."' alt='".$this->config->item("institute_address1")."'></div>";
echo "<h3 class='text-center'>".$this->config->item("institute_address1")."</h3>";
if($this->config->item("institute_address2")!="")
echo "<h6 class='text-center'>Address: ".$this->config->item("institute_address2")."</h6>";
echo "<h6 class='text-center'>Contact: ".$this->config->item("institute_email");
echo " | ".$this->config->item("institute_mobile");
echo "</h6><h6 class='text-center'>Website: <a href=".site_url()." target='_BLANK'>".site_url()."</a></h6>";
echo "</h6><h6 class='text-center'>Generated At: ".$domain_info[0]['search_at']."</h6>";


?>


<!-- ******************************** general sectio *********************************** -->
<style type="text/css">
	.box-body-content{
		margin-bottom: 5px;
		padding-left: 15px;
		font-size: 16px;
		color:#2B4150;
		/*border: 1px solid gray;*/
	}
</style>

<div role="tabpanel" class="tab-pane fade in active" id="">
	
	<div id="hide_after_ajax">
		<div class="row">
			<div class="col-xs-12">
				<h3><div class="well text-center"><?php echo "Domain Name - ".$domain_info[0]['domain_name']; ?></div></h3>
			</div>
		</div>

		<div class="row" style="padding-top:10px;">
			<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">

				<div class="row">
					<div class="col-xs-12">						
						<div class="box bg-blue box-solid">
							<div class="box-header with-border">
								<h3 class="box-title" style="color:white;"><i class="fa fa-street-view"></i> WhoIs Information</h3>
								<div class="box-tools pull-right">
									<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
								</div><!-- /.box-tools -->
							</div><!-- /.box-header -->
							<div class="box-body table-responsive" style="background: white; color: black; padding-bottom: 28px;">
								<table class="table table-hover table-responsive table-bordered">
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
										<td>Domain Age</td>
										<td>
											<?php 
												if($domain_info[0]['whois_created_at'] != '0000-00-00'){									
													$end = date("Y-m-d");
													$start = date("Y-m-d",strtotime($domain_info[0]['whois_created_at']));
													echo calculate_date_differece($end,$start); 
												}
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
										<td><?php if($domain_info[0]['whois_created_at'] != '0000-00-00') echo date("d-M-Y",strtotime($domain_info[0]['whois_created_at'])); ?></td>
									</tr>
									<tr>
										<td>Changed At</td>
										<td><?php if($domain_info[0]['whois_changed_at'] != '0000-00-00') echo date("d-M-Y",strtotime($domain_info[0]['whois_changed_at'])); ?></td>
									</tr>
									<tr>
										<td>Expire At</td>
										<td><?php if($domain_info[0]['whois_expire_at'] != '0000-00-00') echo date("d-M-Y",strtotime($domain_info[0]['whois_expire_at'])); ?></td>
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
										<td><img style="height: 15px; width: 20px; margin-top: -3px;" alt=" " src="<?php echo base_url().'assets/images/flags/'.$domain_info[0]['whois_registrant_country'].'.png' ?>" >&nbsp;<?php echo $domain_info[0]['whois_registrant_country']; ?></td>
									</tr>
									<tr>
										<td>Admin Country</td>
										<td><img style="height: 15px; width: 20px; margin-top: -3px;" alt=" " src="<?php echo base_url().'assets/images/flags/'.$domain_info[0]['whois_admin_country'].'.png' ?>" >&nbsp;<?php echo $domain_info[0]['whois_admin_country']; ?></td>
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
				</div>

				<div class="row">
					<div class="col-xs-12">
						<div class="" style="border:1px solid #00A65A;border-bottom:2px solid #00A65A;">
							<!-- <span class="info-box-icon bg-green" style="height: 100px;"><i class="fa fa-link"></i></span> -->
							<div class="" style="padding:10px;">								
								<table class="table table-hover table-responsive table-bordered" style="margin-bottom: 0px">
									<tr>
										<td>BackLink Count</td>
										<td><?php echo $domain_info[0]['google_back_link_count']; ?></td>
									</tr>
									
									<tr>
										<td>Total Link Count</td>
										<td>
										
										<?php
										
											$total_link_count=$domain_info[0]['moz_links'];
											if($total_link_count=="")
												$total_link_count=0;
										 	echo number_format($total_link_count); 
										 
										 ?>
										 
										 </td>
									</tr>
									
									<tr>
										<td>MozRank</td>
										<td><?php echo round($domain_info[0]['moz_url_normalized'])."/10"; ?></td>
									</tr>
									
								</table>
							</div><!-- /.info-box-content -->
						</div><!-- /.info-box -->
					</div>					
				</div>

				<div class="well row" style="border-left:2px solid orange;-webkit-border-radius:5px;border-radius:5px;margin-left:0px;margin-right:0px;margin-top:7px;">
					<div class="col-xs-12">						
						DMOZ Listing :
						<?php 
							if($domain_info[0]['dmoz_listed_or_not'] == 'no') 
								echo '<span class="label label-danger">No</span>'; 
							if($domain_info[0]['dmoz_listed_or_not'] == 'yes') 
								echo '<span class="label label-success">Yes</span>';
						?> 
					</div>
				</div>

			</div>

			<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
				
				<div class="row" style="margin-bottom: 30px;">
					<div class="col-xs-12 text-center">						
						<img class="img-responsive img-thumbnail" src="http://free.pagepeeker.com/v2/thumbs.php?size=x&url=<?php echo $domain_info[0]['domain_name']; ?>" alt="">
					</div>
				</div>
				
				<div class="row">
					<div class="col-xs-12">
						<div class="box box-primary">
							<div class="box-header with-border">
								<h3 class="box-title" style="color: #028482; word-spacing: 5px;"><img style="height:22px;margin-right: 5px;" src="<?php echo base_url();?>assets/images/moz.png" alt="SiteSpy" class="pull-left img-responsive">MOZ Information</h3>
								<div class="box-tools pull-right">
									<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
								</div><!-- /.box-tools -->
							</div><!-- /.box-header -->
							<div class="box-body table-responsive">
								<table class="table table-responsive table-bordered table-striped">
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
				
			</div>

		</div>

		<div class="row" style="padding-top:10px;">
			<div class="col-xs-12 col-md-8">
				<div class="box bg-blue box-solid">
					<div class="box-header with-border">
						<h3 class="box-title" style="color:white;"><i class="fa fa-mobile"></i> Mobile Friendly Check</h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						</div><!-- /.box-tools -->
					</div><!-- /.box-header -->
					<div class="box-body" style="background: white; color: black;">	
						<div class="row">
							<div class="col-xs-12 col-md-7">
								<?php 							
								$mobile_ready_data=json_decode($domain_info[0]['mobile_ready_data'],true);

								$pass="Unknown";
								$score="Unknown";

								if(isset($mobile_ready_data["ruleGroups"]["USABILITY"]["pass"]))
								$pass=$mobile_ready_data["ruleGroups"]["USABILITY"]["pass"];
								if(isset($mobile_ready_data["ruleGroups"]["USABILITY"]["score"]))
								$score=$mobile_ready_data["ruleGroups"]["USABILITY"]["score"];
								
								if($pass=="1")  
								echo "<br><h3 style='margin-top:0px;'><span class='label label-success'>Mobile Friendly : Yes <br></span></h3> Score : ".$score;
								else if($pass=="Unknown")  
								echo "<br><h3 style='margin-top:0px;'><span class='label label-danger'>Mobile Friendly : Unknown <br></span></h3> Score : ".$score;
								else echo "<br><h3 style='margin-top:0px;'><span class='label label-danger'>Mobile Friendly : No <br></span></h3> Score : ".$score;

								?>
								<br><br>
								<div class=" table-responsive">
									<table class="table table-hover table-striped">
										<tr>
											<th>Localized Rule Name</th>
											<th>Rule Impact</th>
										</tr>
										<?php if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["localizedRuleName"]) || isset($mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"]))
										{?>		
											<tr>
												<td>
													<?php 
													if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["localizedRuleName"]))
													echo $mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["localizedRuleName"];
													?>
												</td>
												<td>
													<?php 
													if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"]))
													echo $mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"];
													?>
												</td>
											</tr>
										<?php 
										} ?>

										<?php if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["UseLegibleFontSizes"]["localizedRuleName"]) || isset($mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"]))
										{?>		
											<tr>
												<td>
													<?php 
													if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["UseLegibleFontSizes"]["localizedRuleName"]))
													echo $mobile_ready_data["formattedResults"]["ruleResults"]["UseLegibleFontSizes"]["localizedRuleName"];
													?>
												</td>
												<td>
													<?php 
													if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["UseLegibleFontSizes"]["ruleImpact"]))
													echo $mobile_ready_data["formattedResults"]["ruleResults"]["UseLegibleFontSizes"]["ruleImpact"];
													?>
												</td>
											</tr>
										<?php 
										} ?>

										<?php if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["AvoidPlugins"]["localizedRuleName"]) || isset($mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"]))
										{?>		
											<tr>
												<td>
													<?php 
													if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["AvoidPlugins"]["localizedRuleName"]))
													echo $mobile_ready_data["formattedResults"]["ruleResults"]["AvoidPlugins"]["localizedRuleName"];
													?>
												</td>
												<td>
													<?php 
													if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["AvoidPlugins"]["ruleImpact"]))
													echo $mobile_ready_data["formattedResults"]["ruleResults"]["AvoidPlugins"]["ruleImpact"];
													?>
												</td>
											</tr>
										<?php 
										} ?>

										<?php if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["SizeContentToViewport"]["localizedRuleName"]) || isset($mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"]))
										{?>		
											<tr>
												<td>
													<?php 
													if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["SizeContentToViewport"]["localizedRuleName"]))
													echo $mobile_ready_data["formattedResults"]["ruleResults"]["SizeContentToViewport"]["localizedRuleName"];
													?>
												</td>
												<td>
													<?php 
													if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["SizeContentToViewport"]["ruleImpact"]))
													echo $mobile_ready_data["formattedResults"]["ruleResults"]["SizeContentToViewport"]["ruleImpact"];
													?>
												</td>
											</tr>
										<?php 
										} ?>

										<?php if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["SizeTapTargetsAppropriately"]["localizedRuleName"]) || isset($mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"]))
										{?>		
											<tr>
												<td>
													<?php 
													if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["SizeTapTargetsAppropriately"]["localizedRuleName"]))
													echo $mobile_ready_data["formattedResults"]["ruleResults"]["SizeTapTargetsAppropriately"]["localizedRuleName"];
													?>
												</td>
												<td>
													<?php 
													if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["SizeTapTargetsAppropriately"]["ruleImpact"]))
													echo $mobile_ready_data["formattedResults"]["ruleResults"]["SizeTapTargetsAppropriately"]["ruleImpact"];
													?>
												</td>
											</tr>
										<?php 
										} ?>

										<?php if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["AvoidInterstitials"]["localizedRuleName"]) || isset($mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"]))
										{?>		
											<tr>
												<td>
													<?php 
													if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["AvoidInterstitials"]["localizedRuleName"]))
													echo $mobile_ready_data["formattedResults"]["ruleResults"]["AvoidInterstitials"]["localizedRuleName"];
													?>
												</td>
												<td>
													<?php 
													if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["AvoidInterstitials"]["ruleImpact"]))
													echo $mobile_ready_data["formattedResults"]["ruleResults"]["AvoidInterstitials"]["ruleImpact"];
													?>
												</td>
											</tr>
										<?php 
										} ?>

										<?php if(!isset($mobile_ready_data["formattedResults"]["ruleResults"])) 
										{?>		
											<tr>
												<td colspan="2" class="text-center">No data to show.</td>
											</tr>
										<?php 
										} ?>

									</table>
								</div>

								<div class="well">
									<b>CMS:</b> <?php if(isset($mobile_ready_data["pageStats"]["cms"])) echo $mobile_ready_data["pageStats"]["cms"];?>								
									<br><b>Locale:</b> <?php if(isset($mobile_ready_data["formattedResults"]["locale"])) echo $mobile_ready_data["formattedResults"]["locale"];?>								
									<br><b>Roboted Resources:</b> <?php if(isset($mobile_ready_data["pageStats"]["numberRobotedResources"])) echo $mobile_ready_data["pageStats"]["numberRobotedResources"];?>								
									<br><b>Transient Fetch Failure Resources:</b> <?php if(isset($mobile_ready_data["pageStats"]["numberTransientFetchFailureResources"])) echo $mobile_ready_data["pageStats"]["numberTransientFetchFailureResources"];?>								
									<br>
								</div>

							</div>
							<div class="col-xs-12 col-md-5" style="padding-left:12px;min-height:530px;background: url('<?php echo base_url("assets/images/mobile.png");?>') no-repeat !important;">
								<?php 
								$mobile_ready_data=json_decode($domain_info[0]["mobile_ready_data"],true);
														
								if(isset($mobile_ready_data["screenshot"]["data"]))
								{
									$src=str_replace("_", "/", $mobile_ready_data["screenshot"]["data"]);
									$src=str_replace("-", "+", $src);
									echo '<img src="data:image/jpeg;base64,'.$src.'" style="max-width:225px !important;margin-top:52px;">';
								}
								?>
							</div>
						</div>				
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div>	
			<div class="col-xs-12 col-md-4">
				<div class="box bg-blue box-solid">
					<div class="box-header with-border">
						<h3 class="box-title" style="color: white; word-spacing: 5px;"><i class="fa fa-map-marker"></i> IP Information</h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						</div><!-- /.box-tools -->
					</div><!-- /.box-header -->
					<div class="box-body table-responsive" style="background: white; color: black;">
						<table class="table table-responsive table-bordered table-striped">
							<tr>
								<th>ISP</th>
								<td><?php echo $domain_info[0]['ipinfo_isp']; ?></td>
							</tr>
							<tr>
								<th>IP</th>
								<td><?php echo $domain_info[0]['ipinfo_ip']; ?><?php $x= trim(strtoupper($domain_info[0]['ipinfo_country']));?></td>
							</tr>
							<tr>
								<th>Country</th>
								<td><img style="height: 15px; width: 20px; margin-top: -3px;" alt=" " src="<?php $s_country = array_search($x, $country_list); echo base_url().'assets/images/flags/'.$s_country.'.png'; ?>">&nbsp;<?php echo $x; ?></td>
							</tr>
							<tr>
								<th>City</th>
								<td><?php echo $domain_info[0]['ipinfo_city']; ?></td>
							</tr>
							<tr>
								<th>Region</th>
								<td><?php echo $domain_info[0]['ipinfo_region']; ?></td>
							</tr>
							<tr>
								<th>Timezone</th>
								<td><?php echo $domain_info[0]['ipinfo_time_zone']; ?></td>
							</tr>
							<tr>
								<th>Latitude</th>
								<td><?php echo $domain_info[0]['ipinfo_latitude']; ?></td>
							</tr>
							<tr>
								<th>Longitude</th>
								<td><?php echo $domain_info[0]['ipinfo_longitude']; ?></td>
							</tr>
							<tr>
							</tr>
						</table>					
					</div><!-- /.box-body -->
				</div><!-- /.box -->

				<div class="box box-warning">
					<div class="box-header with-border">
						<h3 class="box-title" style="color: #028482;"><i class="fa fa-shield"></i> Malware Scan Info</h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						</div><!-- /.box-tools -->
					</div><!-- /.box-header -->
					<div class="box-body  table-responsive">
						<table class="table table-responsive table-bordered table-striped">
							<tr>
								<th>Google Safe Browser</th>
								<th>McAfee</th>
								<th>AVG</th>
								<th>Norton</th>
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

				<div class="box box-danger">
					<div class="box-header with-border">
						<h3 class="box-title" style="color: #028482;"><i class="fa fa-sort-numeric-asc"></i> Search Engine Index Info</h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						</div><!-- /.box-tools -->
					</div><!-- /.box-header -->
					<div class="box-body">
						<table class="table table-responsive table-bordered table-striped">
							<tr>
								<th><i class="fa fa-google" style="color: orange;"></i> Google Index</th>
								<th><i class="fa fa-windows" style="color: orange;"></i> Bing Index</th>
								<th><i class="fa fa-yahoo" style="color: orange;"></i> Yahoo Index</th>
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

		<div class="row" style="padding-top: 10px;">
			<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
				<div class="row">
					<div class="col-xs-12">
						<div class="box box-info">
							<div class="box-header with-border  table-responsive">
								<h3 class="box-title" style="color: #028482;"><i class="fa fa-clone"></i> Sites in Same IP</h3>
								<div class="box-tools pull-right">
									<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
								</div><!-- /.box-tools -->
							</div><!-- /.box-header -->
							<div class="box-body">
								<table class="table table-responsive table-bordered table-striped">
									<tr>
										<?php 
											$sites_in_same_ip = json_decode($domain_info[0]["sites_in_same_ip"],true);
											if(is_array($sites_in_same_ip))
											{
												$sites_in_same_ip=array_slice($sites_in_same_ip,0,18);
												$i=0;
												echo '<tr>';
												foreach($sites_in_same_ip as $key=>$value)
												{
													$i++;
													echo '<td><i class="fa fa-circle-o" style="color:orange;"></i> '.$value.'</td>';
													if($i%2 == 0)
													echo '</tr><tr>'; 
												}
											}
										?>
									</tr>
									<?php if(count($sites_in_same_ip)==0) echo "<tr><td colspan='2' class='text-center'>No data to show</td></tr>"; ?>
								</table>
							</div><!-- /.box-body -->
						</div><!-- /.box -->
					</div>				
				</div>
			</div>

			<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
				<div class="row">
					<div class="col-xs-12">
						<div class="box box-info">
							<div class="box-header with-border  table-responsive">
								<h3 class="box-title" style="color: #028482;"><i class="fa fa-clone"></i> Related Websites</h3>
								<div class="box-tools pull-right">
									<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
								</div><!-- /.box-tools -->
							</div><!-- /.box-header -->
							<div class="box-body">
								<table class="table table-responsive table-bordered table-striped">
									<tr>
										<?php 
											$similar_sites = explode(',', $domain_info[0]['similar_site']);
											$i=0;
											echo '<tr>';
											foreach($similar_sites as $key=>$value){
												$i++;
												echo '<td><i class="fa fa-circle-o" style="color:orange;"></i> '.$value.'</td>';
												if($i%2 == 0)
												echo '</tr><tr>'; 
											}
										?>
									</tr>
									<?php if(count($similar_sites)==0) echo "<tr><td colspan='2' class='text-center'>No data to show</td></tr>"; ?>
								</table>
							</div><!-- /.box-body -->
						</div><!-- /.box -->
					</div>				
				</div>
			</div>
		</div>

	</div>
</div>

<!-- ************************************************************************************************** -->




<!-- *************************************meta tag info *********************************************** -->
<?php
	$h1 = json_decode($domain_info[0]['h1']);
	$h2 = json_decode($domain_info[0]['h2']);
	$h3 = json_decode($domain_info[0]['h3']);
	$h4 = json_decode($domain_info[0]['h4']);
	$h5 = json_decode($domain_info[0]['h5']);
	$h6 = json_decode($domain_info[0]['h6']);
	$meta_tag_information = json_decode($domain_info[0]['meta_tag_information']);
	$blocked_by_robot_txt = $domain_info[0]['blocked_by_robot_txt'];
	$blocked_by_meta_robot = $domain_info[0]['blocked_by_meta_robot'];
	$nofollowed_by_meta_robot = $domain_info[0]['nofollowed_by_meta_robot'];
	$one_phrase = json_decode($domain_info[0]['one_phrase']);
	$two_phrase = json_decode($domain_info[0]['two_phrase']);
	$three_phrase = json_decode($domain_info[0]['three_phrase']);
	$four_phrase = json_decode($domain_info[0]['four_phrase']);
	$total_words = $domain_info[0]['total_words'];
	$domain_name = $domain_info[0]['domain_name'];

	$array_spam_keyword = array( "as seen on","buying judgments", "order status", "dig up dirt on friends",
 "additional income", "double your", "earn per week", "home based", "income from home", "money making",
 "opportunity", "while you sleep", "$$$", "beneficiary", "cash", "cents on the dollar", "claims",
 "cost", "discount", "f r e e", "hidden assets", "incredible deal", "loans", "money",
 "mortgage rates", "one hundred percent free", "price", "quote", "save big money", "subject to credit",
 "unsecured debt", "accept credit cards", "credit card offers", "investment decision",
 "no investment", "stock alert", "avoid bankruptcy", "consolidate debt and credit",
 "eliminate debt", "get paid", "lower your mortgage rate", "refinance home", "acceptance",
 "chance", "here", "leave", "maintained", "never", "remove", "satisfaction", "success", 
 "dear [email/friend/somebody]", "ad", "click", "click to remove", "email harvest", "increase sales",
 "internet market", "marketing solutions", "month trial offer", "notspam",
 "open", "removal instructions", "search engine listings", "the following form", "undisclosed recipient",
 "we hate spam", "cures baldness", "human growth hormone", "lose weight spam", "online pharmacy", 
 "stop snoring", "vicodin", "#1", "4u", "billion dollars", "million", "being a member",
 "cannot be combined with any other offer", "financial freedom", "guarantee",
 "important information regarding", "mail in order form", "nigerian", "no claim forms", "no gimmick", 
 "no obligation", "no selling", "not intended", "offer", "priority mail", "produced and sent out",
 "stuff on sale", "they’re just giving it away", "unsolicited", "warranty", "what are you waiting for?",
 "winner", "you are a winner!", "cancel at any time", "get", "print out and fax", "free", 
 "free consultation", "free grant money", "free instant", "free membership", "free preview ",
  "free sample ", "all natural", "certified", "fantastic deal", "it’s effective",  "real thing",
 "access", "apply online", "can't live without", "don't hesitate", "for you", "great offer", "instant", 
 "now", "once in lifetime", "order now", "special promotion", "time limited", "addresses on cd",
 "brand new pager", "celebrity", "legal", "phone", "buy", "clearance", "orders shipped by", 
 "meet singles", "be your own boss", "earn $", "expect to earn", "home employment", "make $",
 "online biz opportunity", "potential earnings", "work at home", "affordable",
 "best price", "cash bonus", "cheap", "collect", "credit", "earn", "fast cash",
 "hidden charges", "insurance", "lowest price", "money back", "no cost", "only '$'", "profits", 
 "refinance",  "save up to",  "they keep your money -- no refund!",  "us dollars",
 "cards accepted", "explode your business", "no credit check", "requires initial investment",
 "stock disclaimer statement ", "calling creditors", "consolidate your debt", "financially independent",
 "lower interest rate", "lowest insurance rates", "social security number", "accordingly", "dormant",
 "hidden", "lifetime", "medium", "passwords", "reverses", "solution", "teen", "friend",
 "auto email removal", "click below", "direct email", "email marketing",
 "increase traffic", "internet marketing", "mass email", "more internet traffic", "one time mailing",
 "opt in", "sale", "search engines", "this isn't junk", "unsubscribe",
 "web traffic", "diagnostics", "life insurance", "medicine", "removes wrinkles",
 "valium", "weight loss", "100% free", "50% off", "join millions",
 "one hundred percent guaranteed", "billing address", "confidentially on all orders", "gift certificate",
 "have you been turned down?", "in accordance with laws", "message contains", "no age restrictions", 
 "no disappointment", "no inventory", "no purchase necessary", "no strings attached", "obligation",
 "per day", "prize", "reserves the right", "terms and conditions", "trial", "vacation",
 "we honor all", "who really wins?", "winning", "you have been selected",
 "compare", "give it away", "see for yourself", "free access", "free dvd", "free hosting",
 "free investment", "free money", "free priority mail", "free trial",
 "all new", "congratulations", "for free", "outstanding values", "risk free",
 "act now!", "call free", "do it today", "for instant access", "get it now",
 "info you requested", "limited time", "now only", "one time", "order today",
 "supplies are limited", "urgent", "beverage", "cable converter", "copy dvds", "luxury car",
 "rolex", "buy direct", "order", "shopper", "score with babes", "compete for your business",
 "earn extra cash", "extra income", "homebased business", "make money", "online degree", 
 "university diplomas", "work from home", "bargain", "big bucks", "cashcashcash",  "check",
 "compare rates", "credit bureaus", "easy terms", 'for just "$xxx"',  "income",  "investment",
 "million dollars", "mortgage", "no fees", "pennies a day", "pure profit",  "save $",
 "serious cash", "unsecured credit", "why pay more?", "check or money order", "full refund",
 "no hidden costs", "sent in compliance", "stock pick", "collect child support",
 "eliminate bad credit", "get out of debt", "lower monthly payment", "pre-approved",
 "your income", "avoid", "freedom", "home",  "lose", "miracle", "problem", "sample",
 "stop", "wife", "hello", "bulk email", "click here", "direct marketing", "form",
 "increase your sales", "marketing", "member", "multi level marketing", "online marketing", 
 "performance", "sales", "subscribe", "this isn't spam", "visit our website", 
 "will not believe your eyes", "fast viagra delivery", "lose weight",
 "no medical exams", "reverses aging", "viagra", "xanax", "100% satisfied",  "billion", 
 "join millions of americans",  "thousands", "call", "deal", "giving away",
 "if only it were that easy", "long distance phone offer", "name brand", "no catch",
 "no experience", "no middleman", "no questions asked",  "no-obligation", "off shore", "per week", 
 "prizes", "shopping spree", "the best rates", "unlimited", "vacation offers",  "weekend getaway",
 "win", "won", "you’re a winner!", "copy accurately", "print form signature",
 "sign up free today", "free cell phone", "free gift", "free installation",
 "free leads", "free offer", "free quote", "free website",  "amazing",  "drastically reduced",
 "guaranteed", "promise you", "satisfaction guaranteed", "apply now",
 "call now", "don't delete", "for only", "get started now",  "information you requested",
 "new customers only", "offer expires", "only", "please read",
 "take action now", "while supplies last", "bonus", "casino",
 "laser printer", "new domain extensions", "stainless steel"
 );
?>
<div class="row" style="margin-top: 10px;">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title" style="color: #028482; word-spacing: 3px;">TITLE & METATAGS</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div><!-- /.box-tools -->
			</div><!-- /.box-header -->
			<div class="box-body">
				<?php foreach($meta_tag_information as $key=>$value): ?>
					<div class="label label-primary" style="font-size: 12px;"><?php echo ucfirst($key); ?> :</div>
					<div style="word-spacing: 3px;margin-bottom: 5px;"><?php echo $value; ?></div>
				<?php endforeach; ?>
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>

	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
		<div class="well row" style="border-left:2px solid maroon;-webkit-border-radius:5px;border-radius:5px;margin-left:0px;margin-right:0px;">
			BLOCKED BY ROBOTS.TXT :
			 
			<?php 
			if($blocked_by_robot_txt == 'No') 
				echo '<span class="label label-success">No</span>'; 
			if($blocked_by_robot_txt == 'Yes') 
				echo '<span class="label label-danger">Yes</span>';
			?>
		</div>
		<div class="well row" style="border-left:2px solid maroon;-webkit-border-radius:5px;border-radius:5px;margin-left:0px;margin-right:0px;">
			BLOCKED BY META-ROBOTS :
			
			<?php 
			if($blocked_by_meta_robot == 'No') 
				echo '<span class="label label-success">No</span>'; 
			if($blocked_by_meta_robot == 'Yes') 
				echo '<span class="label label-danger">Yes</span>';
			?>
		</div>
		<div class="well row" style="border-left:2px solid maroon;-webkit-border-radius:5px;border-radius:5px;margin-left:0px;margin-right:0px;">
			LINKS NOFOLLOWED BY META-ROBOTS :
			
			<?php 
			if($nofollowed_by_meta_robot == 'No') 
				echo '<span class="label label-success">No</span>'; 
			if($nofollowed_by_meta_robot == 'Yes') 
				echo '<span class="label label-danger">Yes</span>';
			?>
		</div>
		<div class="well row" style="border-left:2px solid maroon;-webkit-border-radius:5px;border-radius:5px;margin-left:0px;margin-right:0px;">
			TOTAL KEYWORDS : 
			<?php 
				echo '<span class="label label-success">'.$total_words.'</span>';
			?> 
		</div>
	</div>
</div>

<div class="row" style="margin-top: 15px;">
	<div class="col-xs-12">
		<div class="box bg-blue box-solid">
			<div class="box-header with-border">
				<h3 class="box-title">HTML HEADINGS</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div><!-- /.box-tools -->
			</div><!-- /.box-header -->
			<div class="box-body" style="background: white; color: black;">
				<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
					<h3 class="highlight_header">H1(<?php echo count($h1) ?>)</h3>
					<div class="highlight_header_content">
						<ul>
						<?php foreach($h1 as $key=>$value): ?>
							<li><?php echo $value; ?></li>
						<?php endforeach; ?>
						</ul>
					</div>
				</div>					
				<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
					<h3 class="highlight_header">H2(<?php echo count($h2) ?>)</h3>
					<div class="highlight_header_content">
						<ul>
						<?php foreach($h2 as $key=>$value): ?>
							<li><?php echo $value; ?></li>
						<?php endforeach; ?>
						</ul>
					</div>
				</div>					
				<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
					<h3 class="highlight_header">H3(<?php echo count($h3) ?>)</h3>
					<div class="highlight_header_content">
						<ul>
						<?php foreach($h3 as $key=>$value): ?>
							<li><?php echo $value; ?></li>
						<?php endforeach; ?>
						</ul>
					</div>
				</div>					
				<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
					<h3 class="highlight_header">H4(<?php echo count($h4) ?>)</h3>
					<div class="highlight_header_content">
						<ul>
						<?php foreach($h4 as $key=>$value): ?>
							<li><?php echo $value; ?></li>
						<?php endforeach; ?>
						</ul>
					</div>
				</div>					
				<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
					<h3 class="highlight_header">H5(<?php echo count($h5) ?>)</h3>
					<div class="highlight_header_content">
						<ul>
						<?php foreach($h5 as $key=>$value): ?>
							<li><?php echo $value; ?></li>
						<?php endforeach; ?>
						</ul>
					</div>
				</div>					
				<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
					<h3 class="highlight_header">H6(<?php echo count($h6) ?>)</h3>
					<div class="highlight_header_content">
						<ul>
						<?php foreach($h6 as $key=>$value): ?>
							<li><?php echo $value; ?></li>
						<?php endforeach; ?>
						</ul>
					</div>
				</div>					
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>		
</div>

<div class="row" style="margin-top: 15px;">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div class="box box-warning">
			<div class="box-header with-border">
				<h3 class="box-title" style="color: #028482; word-spacing: 3px;">KEYWORD ANALYSIS</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div><!-- /.box-tools -->
			</div><!-- /.box-header -->
			<div class="box-body">
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6" style="height: 300px; overflow-y: auto;">
					<h3 class="box-title" style="color: #028482; word-spacing: 5px;"><i class="fa fa-map-marker"></i> Single Word Keywords</h3>
					<table class="table table-responsive table-bordered table-striped">
						<tr>
							<th>SINGLE KEYWORDS</th>
							<th>OCCURRENCES</th>
							<th>DENSITY</th>
							<th>POSSIBLE SPAM</th>
						</tr>
						<?php foreach ($one_phrase as $key => $value) : ?>
							<tr>
								<td><?php echo $key; ?></td>
								<td><?php echo $value; ?></td>
								<td><?php $occurence = ($value/$total_words)*100; echo round($occurence, 3)." %"; ?></td>
								<td><?php 
										if(in_array(strtolower($key), $array_spam_keyword)) echo "Yes";
										else echo 'No'; 
									?>
								</td>
							</tr>
						<?php endforeach; ?>
					</table>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6" style="height: 300px; overflow-y: auto;">
					<h3 class="box-title" style="color: #028482; word-spacing: 5px;"><i class="fa fa-map-marker"></i> Two Words Keywords</h3>
					<table class="table table-responsive table-bordered table-striped">
						<tr>
							<th>2 WORD PHRASES</th>
							<th>OCCURRENCES</th>
							<th>DENSITY</th>
							<th>POSSIBLE SPAM</th>
						</tr>
						<?php foreach ($two_phrase as $key => $value) : ?>
							<tr>
								<td><?php echo $key; ?></td>
								<td><?php echo $value; ?></td>
								<td><?php $occurence = $value/$total_words*100; echo round($occurence, 3)." %"; ?></td>
								<td><?php 
										if(in_array(strtolower($key), $array_spam_keyword)) echo "Yes";
										else echo 'No'; 
									?>
								</td>
							</tr>
						<?php endforeach; ?>
					</table>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6" style="height: 300px; overflow-y: auto; margin-top: 30px;">
					<h3 class="box-title" style="color: #028482; word-spacing: 5px;"><i class="fa fa-map-marker"></i> Three Words Keywords</h3>
					<table class="table table-responsive table-bordered table-striped">
						<tr>
							<th>3 WORD PHRASES</th>
							<th>OCCURRENCES</th>
							<th>DENSITY</th>
							<th>POSSIBLE SPAM</th>
						</tr>
						<?php foreach ($three_phrase as $key => $value) : ?>
							<tr>
								<td><?php echo $key; ?></td>
								<td><?php echo $value; ?></td>
								<td><?php $occurence = $value/$total_words*100; echo round($occurence, 3)." %"; ?></td>
								<td><?php 
										if(in_array(strtolower($key), $array_spam_keyword)) echo "Yes";
										else echo 'No'; 
									?>
								</td>
							</tr>
						<?php endforeach; ?>
					</table>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6" style="height: 300px; overflow-y: auto; margin-top: 30px;">
					<h3 class="box-title" style="color: #028482; word-spacing: 5px;"><i class="fa fa-map-marker"></i> Four Words Keywords</h3>
					<table class="table table-responsive table-bordered table-striped">
						<tr>
							<th>4 WORD PHRASES</th>
							<th>OCCURRENCES</th>
							<th>DENSITY</th>
							<th>POSSIBLE SPAM</th>
						</tr>
						<?php foreach ($four_phrase as $key => $value) : ?>
							<tr>
								<td><?php echo $key; ?></td>
								<td><?php echo $value; ?></td>
								<td><?php $occurence = $value/$total_words*100; echo round($occurence, 3)." %"; ?></td>
								<td><?php 
										if(in_array(strtolower($key), $array_spam_keyword)) echo "Yes";
										else echo 'No'; 
									?>
								</td>
							</tr>
						<?php endforeach; ?>
					</table>
				</div>
				
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
</div>

<!-- ************************************************************************************************************** -->





<!-- **********************************************alexa data****************************************** -->
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
if(array_key_exists(0,$domain_info))
$alexa_data_full=$domain_info[0];



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


?>
<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12">
			<h3><div class="well text-center"><?php echo "Alexa Data - ".$domain; ?></div></h3>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div style="border:1px solid #0073B7;border-bottom:2px solid #0073B7;" class="info-box">
				<span class="info-box-icon bg-blue"><i class="fa fa-bullseye"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Domain Name : </span>
					<span  class="info-box-number"><?php echo $domain; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>	

		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div style="border:1px solid #00A65A;border-bottom:2px solid #00A65A;" class="info-box">
				<span class="info-box-icon bg-green"><i class="fa fa-globe"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Global Rank : </span>
					<span  class="info-box-number"># <?php echo $global_rank; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>	

		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div style="border:1px solid #F39C12;border-bottom:2px solid #F39C12;" class="info-box">
				<span class="info-box-icon bg-yellow"><i class="fa fa-star"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Top Country Rank : </span>
					<span  class="info-box-number"><?php echo $country; ?> # <?php echo $country_rank; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>		
	</div>


	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 style="color: #028482; word-spacing: 5px;" class="box-title"><i class="fa fa-car"></i> Alexa Traffic Rank</h3>
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
					<h3 style="color: #028482; word-spacing: 5px;" class="box-title"><i class="fa fa-globe"></i> Visitors per Country</h3>
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
					                    {
					                    	echo "<tr><td>".$sl."</td>";
						                    echo "<td>".$country_name[$key]."</td>";
						                    echo "<td>".$country_percent_visitor[$key]."</td>";
						                    echo "<td>".$country_in_rank[$key]."</td></tr>";
						                }
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
				<span class="info-box-icon"><i class="fa fa-eye"></i></span>
				<div class="info-box-content">
					<!-- <span class="info-box-text">Inventory</span> -->
					<span class="info-box-number"><?php echo $page_view_per_visitor; ?></span>
					<div class="progress">
						<div style="height: 2px; width: 70%" class="progress-bar"></div>
					</div>
					<span class="progress-description">
						<b>Daily Page View per Visitor</b>
					</span>
				</div><!-- /.info-box-content -->
			</div>
		</div>

		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div class="info-box bg-green">
				<span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
				<div class="info-box-content">
					<!-- <span class="info-box-text">Inventory</span> -->
					<span class="info-box-number"><?php echo $daily_time_on_the_site; ?></span>
					<div class="progress">
						<div style="height: 2px; width: 70%" class="progress-bar"></div>
					</div>
					<span class="progress-description">
						<b>Daily Time on Site</b>
					</span>
				</div><!-- /.info-box-content -->
			</div>
		</div>

		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div class="info-box bg-yellow">
				<!-- <span class="info-box-icon"><i class="fa fa-search"></i></span> -->
				<div class="info-box-content">
					<!-- <span class="info-box-text">Inventory</span> -->
					<span class="info-box-number"><?php echo $visitor_percent_from_searchengine; ?></span>
					<div class="progress">
						<div style="height: 2px; width: 70%" class="progress-bar"></div>
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
					<h3 style="color: #028482; word-spacing: 5px;" class="box-title"><i class="fa fa-star"></i> Top Keywords from Search Engines</h3>
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
			      	             	echo $keyword_name[$key]." <span style='float:right;'><b>".$keyword_percent_of_search_traffic[$key]."</b></span>";
			      	             	$width=$keyword_percent_of_search_traffic[$key];
			      	             	$width=trim($width,"%");
			      	             	if($width<5) $width="5%";
			      	             	else $width=$keyword_percent_of_search_traffic[$key];
			      	             	echo 
			                    	'<div class="progress">					                    	
									  <div class="progress-bar" role="progressbar" aria-valuenow="'.$keyword_percent_of_search_traffic[$key].'" aria-valuemin="0" aria-valuemax="100" style="height:20px !important;width:'.$width.'">
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
					<h3 style="color: #028482; word-spacing: 5px;" class="box-title"><i class="fa fa-car"></i> Search Traffic</h3>
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
			<div class="small-box bg-green" style="padding-left:35px">
				<div class="inner">
					<h3><span id="average_stay_time"><?php echo $total_site_linking_in; ?></span></h3>
					<p>Total Linking In Site</p>
				</div>
				<div class="icon">
					<i class="fa fa-share-alt"></i>
				</div>				
			</div>
		</div>

		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
			<div class="small-box bg-red" style="padding-left:35px">
				<div class="inner">
					<h3><span id="average_stay_time"><?php echo $bounce_rate; ?></span></h3>
					<p>Bounce Rate</p>
				</div>
				<div class="icon">
					<i class="fa fa-sign-out"></i>
				</div>				
			</div>
		</div>
	</div>



	<div class="row">	

	<div class="col-xs-12">
		<div class="box box-primary">
				<div class="box-header with-border">
					<h3 style="color: #028482; word-spacing: 5px;" class="box-title"> <i class="fa fa-share-alt"></i> Linking In Statistics</h3>
					<div class="box-tools pull-right">
						<button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></button>
						<button data-widget="remove" class="btn btn-box-tool"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<table class="table table-bordered table-striped">
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
					<h3 style="color: #028482; word-spacing: 5px;" class="box-title"><i class="fa fa-level-up"></i> Upstream Sites</h3>
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
			                    	$width=$upstream_percent_unique_visits[$key];
			      	             	$width=trim($width,"%");
			      	             	if($width<5) $width="5%";
			      	             	else $width=$upstream_percent_unique_visits[$key];
			                    	echo 
			                    	'<div class="progress">					                    	
									  <div class="progress-bar" role="progressbar" aria-valuenow="'.$upstream_percent_unique_visits[$key].'" aria-valuemin="0" aria-valuemax="100" style="height:20px; width:'.$width.'">
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
					<h3 style="color: #028482; word-spacing: 5px;" class="box-title"><i class="fa fa-sitemap"></i> Subdomain Statistics</h3>
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
			                    	$width=$subdomain_percent_visitors[$key];
			      	             	$width=trim($width,"%");
			      	             	if($width<5) $width="5%";
			      	             	else $width=$subdomain_percent_visitors[$key];
			                    	echo 
			                    	'<div class="progress">					                    	
									  <div class="progress-bar" role="progressbar" aria-valuenow="'.$subdomain_percent_visitors[$key].'" aria-valuemin="0" aria-valuemax="100" style="height:20px; width:'.$width.'">
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
<!-- ************************************************************************************************** -->



<!-- **********************************similarweb information****************************************** -->
<?php 
$similar_web_data=array();
$similar_web_data["domain_name"]="";
$similar_web_data["similar_web_global_rank"]="";
$similar_web_data["similar_web_country_rank"]="";
$similar_web_data["similar_web_country"]="";
$similar_web_data["similar_web_category_rank"]="";
$similar_web_data["similar_web_category"]="";
$similar_web_data["similar_web_total_visit"]="";
$similar_web_data["similar_web_time_on_site"]="";
$similar_web_data["similar_web_page_views"]="";
$similar_web_data["similar_web_bounce_rate"]="";
$similar_web_data["similar_web_traffic_country"]=array();
$similar_web_data["similar_web_traffic_country_percentage"]=array();
$similar_web_data["similar_web_direct_traffic"]="";
$similar_web_data["similar_web_referral_traffic"]="";
$similar_web_data["similar_web_search_traffic"]="";
$similar_web_data["similar_web_social_traffic"]="";
$similar_web_data["similar_web_mail_traffic"]="";
$similar_web_data["similar_web_display_traffic"]="";
$similar_web_data["similar_web_top_referral_site"]=array();
$similar_web_data["similar_web_top_destination_site"]=array();
$similar_web_data["similar_web_organic_search_percentage"]="";
$similar_web_data["similar_web_paid_search_percentage"]="";
$similar_web_data["similar_web_top_organic_keyword"]=array();
$similar_web_data["similar_web_top_paid_keyword"]=array();
$similar_web_data["similar_web_social_site_name"]=array();
$similar_web_data["similar_web_social_site_percentage"]=array();

if(array_key_exists(0,$similar_web))
$similar_web_data=$similar_web[0];


$domain =$similar_web_data["domain_name"];
$global_rank =$similar_web_data["similar_web_global_rank"];
$country_rank =$similar_web_data["similar_web_country_rank"];
$country =$similar_web_data["similar_web_country"];
$category_rank=$similar_web_data["similar_web_category_rank"];
$category=$similar_web_data["similar_web_category"];
$total_visit=$similar_web_data["similar_web_total_visit"];
$time_on_site=$similar_web_data["similar_web_time_on_site"];
$page_views=$similar_web_data["similar_web_page_views"];
$bounce=$similar_web_data["similar_web_bounce_rate"];

$traffic_country=json_decode($similar_web_data["similar_web_traffic_country"],true);
$traffic_country_percentage=json_decode($similar_web_data["similar_web_traffic_country_percentage"],true);

$direct_traffic=$similar_web_data["similar_web_direct_traffic"];
$referral_traffic=$similar_web_data["similar_web_referral_traffic"];
$search_traffic=$similar_web_data["similar_web_search_traffic"];
$social_traffic=$similar_web_data["similar_web_social_traffic"];
$mail_traffic=$similar_web_data["similar_web_mail_traffic"];
$display_traffic=$similar_web_data["similar_web_display_traffic"];

if($direct_traffic=="0" 	|| $direct_traffic=="") 	$direct_traffic="0%";
if($referral_traffic=="0" 	|| $referral_traffic=="") 	$referral_traffic="0%";
if($search_traffic=="0" 	|| $search_traffic=="") 	$search_traffic="0%";
if($social_traffic=="0" 	|| $social_traffic=="") 	$social_traffic="0%";
if($mail_traffic=="0" 		|| $mail_traffic=="") 		$mail_traffic="0%";
if($display_traffic=="0" 	|| $display_traffic=="") 	$display_traffic="0%";

$top_referral_site=json_decode($similar_web_data["similar_web_top_referral_site"],true);
$top_destination_site=json_decode($similar_web_data["similar_web_top_destination_site"],true);

$organic_search_percentage=$similar_web_data["similar_web_organic_search_percentage"];
$paid_search_percentage=$similar_web_data["similar_web_paid_search_percentage"];

$top_organic_keyword=json_decode($similar_web_data["similar_web_top_organic_keyword"],true);
$top_paid_keyword=json_decode($similar_web_data["similar_web_top_paid_keyword"],true);

$social_site_name=json_decode($similar_web_data["similar_web_social_site_name"],true);
$social_site_percentage=json_decode($similar_web_data["similar_web_social_site_percentage"],true);
         


?>
<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12">
			<h3><div class="well text-center"><?php echo "SimilarWeb Data - ".$domain; ?></div></h3>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div style="border:1px solid #0073B7;border-bottom:2px solid #0073B7;" class="info-box">
				<span class="info-box-icon bg-blue"><i class="fa fa-tag"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Category Rank : </span>
					<span  class="info-box-number"><?php echo $category; ?> # <?php echo $category_rank; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>		

		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div style="border:1px solid #00A65A;border-bottom:2px solid #00A65A;" class="info-box">
				<span class="info-box-icon bg-green"><i class="fa fa-globe"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Global Rank : </span>
					<span  class="info-box-number"># <?php echo $global_rank; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>	

		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div style="border:1px solid #F39C12;border-bottom:2px solid #F39C12;" class="info-box">
				<span class="info-box-icon bg-yellow"><i class="fa fa-star"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Top Country Rank : </span>
					<span  class="info-box-number"><?php echo $country; ?> # <?php echo $country_rank; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>			
	</div>

	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
			<div class="info-box bg-aqua">
				<span class="info-box-icon"><i class="fa fa-chrome"></i></span>
				<div class="info-box-content">
					<!-- <span class="info-box-text">Inventory</span> -->
					<span class="info-box-number"><?php echo $total_visit; ?></span>
					<div class="progress">
						<div style="height: 2px; width: 70%" class="progress-bar"></div>
					</div>
					<span class="progress-description">
						<b>Total Visit</b>
					</span>
				</div><!-- /.info-box-content -->
			</div>
		</div>

		<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
			<div class="info-box bg-aqua">
				<span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
				<div class="info-box-content">
					<!-- <span class="info-box-text">Inventory</span> -->
					<span class="info-box-number"><?php echo $time_on_site; ?></span>
					<div class="progress">
						<div style="height: 2px; width: 70%" class="progress-bar"></div>
					</div>
					<span class="progress-description">
						<b>Time on Site</b>
					</span>
				</div><!-- /.info-box-content -->
			</div>
		</div>

		<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
			<div class="info-box bg-aqua">
				<span class="info-box-icon"><i class="fa fa-newspaper-o"></i></span>
				<div class="info-box-content">
					<!-- <span class="info-box-text">Inventory</span> -->
					<span class="info-box-number"><?php echo $page_views; ?></span>
					<div class="progress">
						<div style="height: 2px; width: 70%" class="progress-bar"></div>
					</div>
					<span class="progress-description">
						<b>Page Views</b>
					</span>
				</div><!-- /.info-box-content -->
			</div>
		</div>

		<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
			<div class="info-box bg-aqua">
				<span class="info-box-icon"><i class="fa fa-sign-out"></i></span>
				<div class="info-box-content">
					<!-- <span class="info-box-text">Inventory</span> -->
					<span class="info-box-number"><?php echo $bounce; ?></span>
					<div class="progress">
						<div style="height: 2px; width: 70%" class="progress-bar"></div>
					</div>
					<span class="progress-description">
						<b>Bounce Rate</b>
					</span>
				</div><!-- /.info-box-content -->
			</div>
		</div>
	</div>


	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 style="color: blue; word-spacing: 5px;" class="box-title"><i class="fa fa-globe"></i> Traffic by Countries</h3>
					<div class="box-tools pull-right">
						<button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></button>
						<button data-widget="remove" class="btn btn-box-tool"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive table-responsive">
					<table class="table table-condensed table-responsive table-bordered">
						<thead>
							<tr>
								<th><h4>SL</h4></th>
								<th><h4>Country</h4></th>
								<th><h4>Traffic %</h4></th>
							</tr>
						</thead>
						<tbody>
							<?php 
								$sl=0;                  
					            if(is_array($traffic_country) && is_array($traffic_country_percentage))
					            {
					                foreach($traffic_country as $key=>$val)
					                {                  
					                    $sl++;
					                    if(array_key_exists($key, $traffic_country) && array_key_exists($key, $traffic_country_percentage))
					                    {
					                    	echo "<tr><td>".$sl."</td>";
						                    echo "<td>".$traffic_country[$key]."</td>";
						                    echo "<td>".$traffic_country_percentage[$key]."</td>";
						                }
					                }
					                if(count($traffic_country)==0 || count($traffic_country_percentage)==0  )
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
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 style="color: blue; word-spacing: 5px;" class="box-title"><i class="fa fa-share-alt"></i> Social Media Traffic</h3>
					<div class="box-tools pull-right">
						<button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></button>
						<button data-widget="remove" class="btn btn-box-tool"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive">
					<?php                  
			            if(is_array($social_site_name) && is_array($social_site_percentage))
			            {
			                foreach($social_site_name as $key=>$val)
			                {   
			                    if(array_key_exists($key, $social_site_name) && array_key_exists($key, $social_site_percentage))
			                    {
			      	             	echo $social_site_name[$key]." <span class='pull-right'><b>".$social_site_percentage[$key]."</b></span>";
			                    	$width=$social_site_percentage[$key];
			      	             	$width=trim($width,"%");
			      	             	if($width<5) $width="5%";
			      	             	else $width=$social_site_percentage[$key];
			                    	echo 
			                    	'<div class="progress">					                    	
									  <div class="progress-bar" role="progressbar" aria-valuenow="'.$social_site_percentage[$key].'" aria-valuemin="0" aria-valuemax="100" style="height:20px;width:'.$width.'">
									  </div>
									</div>';
				                }
				                if(count($social_site_name)==0 || count($social_site_percentage)==0 )
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

	<div class="row">
		<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
			<div style="border:1px solid #00C0EF;border-bottom:2px solid #00C0EF;" class="info-box">
				<span class="info-box-icon bg-aqua"><i class="fa fa-arrow-right"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Direct Traffic : </span>
					<span  class="info-box-number"><?php echo $direct_traffic; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>	
		<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
			<div style="border:1px solid #00C0EF;border-bottom:2px solid #00C0EF;" class="info-box">
				<span class="info-box-icon bg-aqua"><i class="fa fa-arrow-down"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Referral Traffic : </span>
					<span  class="info-box-number"><?php echo $referral_traffic; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
			<div style="border:1px solid #00C0EF;border-bottom:2px solid #00C0EF;" class="info-box">
				<span class="info-box-icon bg-aqua"><i class="fa fa-search"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Search Traffic : </span>
					<span  class="info-box-number"><?php echo $search_traffic; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>	
		<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
			<div style="border:1px solid #00C0EF;border-bottom:2px solid #00C0EF;" class="info-box">
				<span class="info-box-icon bg-aqua"><i class="fa fa-share-alt"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Social Traffic : </span>
					<span  class="info-box-number"><?php echo $social_traffic; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>	
		<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
			<div style="border:1px solid #00C0EF;border-bottom:2px solid #00C0EF;" class="info-box">
				<span class="info-box-icon bg-aqua"><i class="fa fa-envelope"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Mail Traffic : </span>
					<span  class="info-box-number"><?php echo $mail_traffic; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>	
		<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
			<div style="border:1px solid #00C0EF;border-bottom:2px solid #00C0EF;" class="info-box">
				<span class="info-box-icon bg-aqua"><i class="fa fa-laptop"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Display Traffic : </span>
					<span  class="info-box-number"><?php echo $display_traffic; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>	
	

	</div>



	


	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 style="color: blue; word-spacing: 5px;" class="box-title"><i class="fa fa-arrow-down"></i> Top Referral Sites</h3>
					<div class="box-tools pull-right">
						<button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></button>
						<button data-widget="remove" class="btn btn-box-tool"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive table-responsive">
					<table class="table table-condensed table-responsive table-bordered">
						<thead>
							<tr>
								<th><h4>SL</h4></th>
								<th><h4>Top Referral Site</h4></th>
							</tr>
						</thead>
						<tbody>
							<?php 
								$sl=0;                  
					            if(is_array($top_referral_site) && count($top_referral_site)>0)
					            {
					                foreach($top_referral_site as $key=>$val)
					                {                  
					                    $sl++;
					                    echo "<tr><td>".$sl."</td>";
						                echo "<td>".$top_referral_site[$key]."</td>";					                
					                }
					            }
					            else
					            {
					            	echo "<tr><td colspan='2'>No data found!</td></tr>";
					            }

							?>
						</tbody>
					</table>
				</div>
			</div> <!-- end box -->	
		</div>
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">	
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 style="color: blue; word-spacing: 5px;" class="box-title"><i class="fa fa-arrow-up"></i> Top Destination Sites</h3>
					<div class="box-tools pull-right">
						<button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></button>
						<button data-widget="remove" class="btn btn-box-tool"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive table-responsive">
					<table class="table table-condensed table-responsive table-bordered">
						<thead>
							<tr>
								<th><h4>SL</h4></th>
								<th><h4>Top Destination Site</h4></th>
							</tr>
						</thead>
						<tbody>
							<?php 
								$sl=0;                  
					            if(is_array($top_destination_site) && count($top_destination_site)>0)
					            {
					                foreach($top_destination_site as $key=>$val)
					                {                  
					                    $sl++;
					                    echo "<tr><td>".$sl."</td>";
						                echo "<td>".$top_destination_site[$key]."</td>";					                
					                }
					            }
					            else
					            {
					            	echo "<tr><td colspan='2'>No data found!</td></tr>";
					            }

							?>
						</tbody>
					</table>
				</div>
			</div> <!-- end box -->		
		</div>
	</div>


	<div class="row">
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
			<div class="info-box bg-blue">
				<span class="info-box-icon"><i class="fa fa-search"></i></span>
				<div class="info-box-content">
					<!-- <span class="info-box-text">Inventory</span> -->
					<span class="info-box-number"><?php echo $organic_search_percentage; ?></span>
					<div class="progress">
						<div style="height: 2px; width: 70%" class="progress-bar"></div>
					</div>
					<span class="progress-description">
						<b>Organic Search</b>
					</span>
				</div><!-- /.info-box-content -->
			</div>
		</div>

		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
			<div class="info-box bg-blue">
				<span class="info-box-icon"><i class="fa fa-usd"></i></span>
				<div class="info-box-content">
					<!-- <span class="info-box-text">Inventory</span> -->
					<span class="info-box-number"><?php echo $paid_search_percentage; ?></span>
					<div class="progress">
						<div style="height: 2px; width: 70%" class="progress-bar"></div>
					</div>
					<span class="progress-description">
						<b>Paid Search</b>
					</span>
				</div><!-- /.info-box-content -->
			</div>
		</div>
	</div>



	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 style="color: blue; word-spacing: 5px;" class="box-title"><i class="fa fa-tags"></i> Top Organic Keywords</h3>
					<div class="box-tools pull-right">
						<button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></button>
						<button data-widget="remove" class="btn btn-box-tool"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive table-responsive">
					<table class="table table-condensed table-responsive table-bordered">
						<thead>
							<tr>
								<th><h4>SL</h4></th>
								<th><h4>Keyword</h4></th>
							</tr>
						</thead>
						<tbody>
							<?php 
								$sl=0;                  
					            if(is_array($top_organic_keyword) && count($top_organic_keyword)>0)
					            {
					                foreach($top_organic_keyword as $key=>$val)
					                {                  
					                    $sl++;
					                    echo "<tr><td>".$sl."</td>";
						                echo "<td>".$top_organic_keyword[$key]."</td>";					                
					                }
					            }
					            else
					            {
					            	echo "<tr><td colspan='2'>No data found!</td></tr>";
					            }

							?>
						</tbody>
					</table>
				</div>
			</div> <!-- end box -->	
		</div>
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">	
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 style="color: blue; word-spacing: 5px;" class="box-title"><i class="fa fa-tags"></i> Top Paid Keywords</h3>
					<div class="box-tools pull-right">
						<button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i></button>
						<button data-widget="remove" class="btn btn-box-tool"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body chart-responsive table-responsive">
					<table class="table table-condensed table-responsive table-bordered">
						<thead>
							<tr>
								<th><h4>SL</h4></th>
								<th><h4>Keyword</h4></th>
							</tr>
						</thead>
						<tbody>
							<?php 
								$sl=0;                  
					            if(is_array($top_paid_keyword) && count($top_paid_keyword)>0)
					            {
					                foreach($top_paid_keyword as $key=>$val)
					                {                  
					                    $sl++;
					                    echo "<tr><td>".$sl."</td>";
						                echo "<td>".$top_paid_keyword[$key]."</td>";					                
					                }
					            }
					            else
					            {
					            	echo "<tr><td colspan='2'>No data found!</td></tr>";
					            }

							?>
						</tbody>
					</table>
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

</style>
<!-- ************************************************************************************************** -->




<!-- **************************************social network info***************************************** -->
<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12">
			<h3><div class="well text-center">Facebook Information</div></h3>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div style="border:1px solid #0073B7;border-bottom:2px solid #0073B7;" class="info-box">
				<span class="info-box-icon bg-blue"><i class="fa fa-users"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Facebook Share : </span>
					<span id="total_unique_visitor" class="info-box-number"><?php echo $info['fb_total_share']; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<h3><div class="well text-center">Stumbleupon Information</div></h3>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div style="border:1px solid #CC6600;border-bottom:2px solid #CC6600;" class="info-box">
				<span class="info-box-icon bg-blue"><i class="fa fa-users"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Stumbleupon Like : </span>
					<span id="total_unique_visitor" class="info-box-number"><?php echo $info['stumbleupon_total_like']; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div style="border:1px solid #CC6600;border-bottom:2px solid #CC6600;" class="info-box">
				<span class="info-box-icon bg-blue"><i class="fa fa-users"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Stumbleupon View : </span>
					<span id="total_unique_visitor" class="info-box-number"><?php echo $info['stumbleupon_total_view']; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div style="border:1px solid #CC6600;border-bottom:2px solid #CC6600;" class="info-box">
				<span class="info-box-icon bg-blue"><i class="fa fa-users"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Stumbleupon Comment : </span>
					<span id="total_unique_visitor" class="info-box-number"><?php echo $info['stumbleupon_total_comment']; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div style="border:1px solid #CC6600;border-bottom:2px solid #CC6600;" class="info-box">
				<span class="info-box-icon bg-blue"><i class="fa fa-users"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Stumbleupon List : </span>
					<span id="total_unique_visitor" class="info-box-number"><?php echo $info['stumbleupon_total_list']; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<h3><div class="well text-center">Reddit Information</div></h3>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div style="border:1px solid #3A5F0B;border-bottom:2px solid #3A5F0B;" class="info-box">
				<span class="info-box-icon bg-blue"><i class="fa fa-users"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Reddit Score : </span>
					<span id="total_unique_visitor" class="info-box-number"><?php echo $info['reddit_score']; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div style="border:1px solid #3A5F0B;border-bottom:2px solid #3A5F0B;" class="info-box">
				<span class="info-box-icon bg-blue"><i class="fa fa-users"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Reddit Ups : </span>
					<span id="total_unique_visitor" class="info-box-number"><?php echo $info['reddit_ups']; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div style="border:1px solid #3A5F0B;border-bottom:2px solid #3A5F0B;" class="info-box">
				<span class="info-box-icon bg-blue"><i class="fa fa-users"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Reddit Downs : </span>
					<span id="total_unique_visitor" class="info-box-number"><?php echo $info['reddit_downs']; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<h3><div class="well text-center">Other Social Network Information</div></h3>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div style="border:1px solid #E8D0A9;border-bottom:2px solid #E8D0A9;" class="info-box">
				<span class="info-box-icon bg-blue"><i class="fa fa-users"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Google Plus Share : </span>
					<span id="total_unique_visitor" class="info-box-number"><?php echo $info['google_plus_share']; ?></span>
				</div><!-- /.info-box-content -->
			</div>
			<div style="border:1px solid #B7AFA3;border-bottom:2px solid #B7AFA3;" class="info-box">
				<span class="info-box-icon bg-blue"><i class="fa fa-users"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Pinterest Pin : </span>
					<span id="total_unique_visitor" class="info-box-number"><?php echo $info['pinterest_pin']; ?></span>
				</div><!-- /.info-box-content -->
			</div>
			<div style="border:1px solid #C1DAD6;border-bottom:2px solid #C1DAD6;" class="info-box">
				<span class="info-box-icon bg-blue"><i class="fa fa-users"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Buffer Share : </span>
					<span id="total_unique_visitor" class="info-box-number"><?php echo $info['buffer_share']; ?></span>
				</div><!-- /.info-box-content -->
			</div>
			<div style="border:1px solid #F5FAFA;border-bottom:2px solid #F5FAFA;" class="info-box">
				<span class="info-box-icon bg-blue"><i class="fa fa-users"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Xing Share : </span>
					<span id="total_unique_visitor" class="info-box-number"><?php echo $info['xing_share']; ?></span>
				</div><!-- /.info-box-content -->
			</div>
			<div style="border:1px solid #6D929B;border-bottom:2px solid #6D929B;" class="info-box">
				<span class="info-box-icon bg-blue"><i class="fa fa-users"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Linkedin Share : </span>
					<span id="total_unique_visitor" class="info-box-number"><?php echo $info['linkedin_share']; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>
	</div>
</div>
<!-- ************************************************************************************************** -->




<?php echo "</body></html>"; ?>