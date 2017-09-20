<?php 
if($ip_info["status"]!="success") 
{
	$ip_info['city']="";
	$ip_info['country']="";
	$ip_info['postal']="";
	$ip_info['org']="";
	$ip_info['hostname']="";
	$ip_info['region']="";
	$ip_info['latitude']="";
	$ip_info['longitude']="";
}
?>

<div class="container-fluid">	
	<div class="row">
		<div class="col-xs-12">
			<h3><div class="well text-center"><?php echo $this->lang->line("my ip information");?></div></h3>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div class="info-box" style="border:1px solid #00C0EF;border-bottom:2px solid #00C0EF;">
				<span class="info-box-icon bg-aqua"><i class="fa fa-tag"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">IP Address</span>
					<span class="info-box-number" ><?php echo $my_ip; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>	

		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div class="info-box" style="border:1px solid #00C0EF;border-bottom:2px solid #00C0EF;">
				<span class="info-box-icon bg-aqua"><i class="fa fa-map-marker"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Latitude</span>
					<span class="info-box-number" ><?php echo $ip_info["latitude"]; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>	

		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			<div class="info-box" style="border:1px solid #00C0EF;border-bottom:2px solid #00C0EF;">
				<span class="info-box-icon bg-aqua"><i class="fa fa-map-marker"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Longitude</span>
					<span class="info-box-number" ><?php echo $ip_info["longitude"]; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>	

		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
			<div class="info-box" style="border:1px solid #00C0EF;border-bottom:2px solid #00C0EF;">
				<span class="info-box-icon bg-aqua"><i class="fa fa-building"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Organization</span>
					<span class="info-box-number" ><?php echo $ip_info["org"]; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>	

		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
			<div class="info-box" style="border:1px solid #00C0EF;border-bottom:2px solid #00C0EF;">
				<span class="info-box-icon bg-aqua"><i class="fa fa-server"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Hostname</span>
					<span class="info-box-number" ><?php echo $ip_info["hostname"]; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>	

		<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
			<div class="info-box" style="border:1px solid #00C0EF;border-bottom:2px solid #00C0EF;">
				<span class="info-box-icon bg-aqua"><i class="fa fa-globe"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Country</span>
					<span class="info-box-number" ><?php echo $ip_info["country"]; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>	

		<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
			<div class="info-box" style="border:1px solid #00C0EF;border-bottom:2px solid #00C0EF;">
				<span class="info-box-icon bg-aqua"><i class="fa fa-map"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Region</span>
					<span class="info-box-number" ><?php echo $ip_info["region"]; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>	

		<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
			<div class="info-box" style="border:1px solid #00C0EF;border-bottom:2px solid #00C0EF;">
				<span class="info-box-icon bg-aqua"><i class="fa fa-home"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">City</span>
					<span class="info-box-number" ><?php echo $ip_info["city"]; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>	

		<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3">
			<div class="info-box" style="border:1px solid #00C0EF;border-bottom:2px solid #00C0EF;">
				<span class="info-box-icon bg-aqua"><i class="fa fa-envelope"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Postal Code</span>
					<span class="info-box-number" ><?php echo $ip_info["postal"]; ?></span>
				</div><!-- /.info-box-content -->
			</div>
		</div>		
	</div>
</div>
