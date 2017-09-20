<!-- Bootstrap 3.3.4 -->
<link href="<?php echo base_url();?>bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<!-- FontAwesome 4.3.0 -->
<link href="<?php echo base_url()."assets/css/font-awesome.css" ?>" rel="stylesheet" type="text/css" />
<!-- Theme style -->
<link href="<?php echo base_url();?>css/AdminLTE.min.css" rel="stylesheet" type="text/css" />

<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<div class="box box-warning">
		<div class="box-header with-border" style="background: #3C8DBC;">
			<h3 style="color: white; word-spacing: 3px;" class="box-title"><i class="fa fa-star"></i> Top Web Pages From <span><?php echo $from_date; ?></span> To <span><?php echo $to_date; ?></span></h3>
			<div class="box-tools pull-right">
				<span class="logo-lg">
					<a href="<?php echo $this->config->item('project_url'); ?>">
						<img style="height:28px;" src="<?php echo base_url();?>assets/images/logo.png" alt="SiteSpy" class="img-responsive">
  					</a>
  				</span>
			</div>
		</div>
		<div class="box-body chart-responsive">
			<?php
				
				if(empty($content_overview_data)){
					echo '<h1 class="text-center" style="color:red;">No Data To Show</h1>';
				}else {
					$i = 0;
			        foreach($content_overview_data as $value){
			            $percentage = number_format($value['total_view']*100/$total_view, 2);
			            $i++;
			            echo $i.". ".$value['visit_url']." <span class='pull-right'><b>".$percentage." %</b></span>";
			            echo 
			            '<div class="progress">                                         
			              <div class="progress-bar progress-bar-striped " role="progressbar" aria-valuenow="'.$percentage.'" aria-valuemin="0" aria-valuemax="100" style="width:'.$percentage.'%">
			              </div>
			            </div>';
			            if($i==5) break;
			        }
				}
			?>
		</div>
	</div> <!-- end box -->			
</div>