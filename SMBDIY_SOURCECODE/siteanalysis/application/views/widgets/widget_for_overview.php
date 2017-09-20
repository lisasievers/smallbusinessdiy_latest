<!-- Bootstrap 3.3.4 -->
<link href="<?php echo base_url();?>bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<!-- FontAwesome 4.3.0 -->
<link href="<?php echo base_url()."assets/css/font-awesome.css" ?>" rel="stylesheet" type="text/css" />
<!-- Theme style -->
<link href="<?php echo base_url();?>css/AdminLTE.min.css" rel="stylesheet" type="text/css" />

<div class="row">	
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div class="box box-warning">
			<div class="box-header with-border" style="background: #3C8DBC;">
				<h3 style="color: white; word-spacing: 3px;" class="box-title">Report For Last 30 Days</h3>
				<div class="box-tools pull-right">
					<span class="logo-lg">
						<a href="<?php echo base_url(); ?>">
							<img style="height:28px;" src="<?php echo base_url();?>assets/images/logo.png" alt="SiteSpy" class="img-responsive">
	  					</a>
	  				</span>
				</div>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-xs-6">
						<div class="info-box" style="border:1px solid #00A65A;border-bottom:2px solid #00A65A;">
							<span class="info-box-icon bg-green" style="padding-top: 15px;"><i class="fa fa-users"></i></span>
							<div class="info-box-content">
								<span class="info-box-text">Total Unique Visitor</span>
								<span class="info-box-number"><?php echo $total_unique_visitro; ?></span>
							</div><!-- /.info-box-content -->
						</div><!-- /.info-box -->
					</div>
					<div class="col-xs-6">
						<div class="info-box" style="border:1px solid #00A65A;border-bottom:2px solid #00A65A;">
							<span class="info-box-icon bg-green" style="padding-top: 15px;"><i class="fa fa-file-text-o"></i></span>
							<div class="info-box-content">
								<span class="info-box-text">Total Page View</span>
								<span class="info-box-number"><?php echo $total_page_view; ?></span>
							</div><!-- /.info-box-content -->
						</div><!-- /.info-box -->
					</div>
				</div>
				<div class="row">
					<div class="col-xs-4">
						<div class="info-box bg-blue">
							<span class="info-box-icon" style="padding-top: 15px; line-height: 90px; height: 90px;"><i class="fa fa-eye"></i></span>
							<div class="info-box-content">
								<!-- <span class="info-box-text">Inventory</span> -->
								<span class="info-box-number"><?php echo $average_visit; ?></span>
								<div class="progress">
									<div class="progress-bar" style="width: 70%"></div>
								</div>
								<span class="progress-description">
									<b>Average Visit</b>
								</span>
							</div><!-- /.info-box-content -->
						</div><!-- /.info-box -->
					</div>
					<div class="col-xs-4">
						<div class="info-box bg-orange">
							<span class="info-box-icon" style="padding-top: 15px; line-height: 90px; height: 90px;"><i class="fa fa-clock-o"></i></span>
							<div class="info-box-content">
								<!-- <span class="info-box-text">Inventory</span> -->
								<span class="info-box-number"><?php echo $average_stay_time; ?></span>
								<div class="progress">
									<div class="progress-bar" style="width: 70%"></div>
								</div>
								<span class="progress-description">
									<b>Average Stay Time</b>
								</span>
							</div><!-- /.info-box-content -->
						</div><!-- /.info-box -->
					</div>
					<div class="col-xs-4">
						<div class="info-box bg-red">
							<span class="info-box-icon" style="padding-top: 15px; line-height: 90px; height: 90px;"><i class="fa fa-sign-out"></i></span>
							<div class="info-box-content">
								<!-- <span class="info-box-text">Inventory</span> -->
								<span class="info-box-number"><?php echo $bounce_rate." %"; ?></span>
								<div class="progress">
									<div class="progress-bar" style="width: 70%"></div>
								</div>
								<span class="progress-description">
									<b>Bounce Rate</b>
								</span>
							</div><!-- /.info-box-content -->
						</div><!-- /.info-box -->
					</div>
				</div>
			</div>
		</div> <!-- end box -->			
	</div>
</div>