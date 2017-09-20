<section class="content-header">
	<section class="content">
	<?php 
	if($this->session->userdata("success_message") == 1){
		echo "<div class='alert alert-success text-center'><h4 style='margin:0;'><i class='fa fa-check-circle'></i> ".$this->lang->line("your data has been successfully stored into the database.")."</h4></div>";
		$this->session->unset_userdata("success_message");
	}
	if($this->session->userdata("error_message") == 1){
		echo "<div class='alert alert-danger text-center'><h4 style='margin:0;'><i class='fa fa-remove'></i> ".$this->lang->line("your data has been failed to stored into the database.")."</h4></div>";
		$this->session->unset_userdata("error_message");
	}
	if($this->session->userdata("limit_exceeded") == 2){
		echo "<div class='alert alert-danger text-center'><h4 style='margin:0;'><i class='fa fa-remove'></i> ".$this->lang->line("sorry, your bulk limit is exceeded for this this module.")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a></h4></div>";
		$this->session->unset_userdata("limit_exceeded");
	}
	if($this->session->userdata("limit_exceeded") == 3){
		echo "<div class='alert alert-danger text-center'><h4 style='margin:0;'><i class='fa fa-remove'></i> ".$this->lang->line("sorry, your monthly limit is exceeded for this this module.")."<a href='".site_url('payment/usage_history')."'>".$this->lang->line("click here to see usage log")."</a></h4></div>";
		$this->session->unset_userdata("limit_exceeded");
	}
	?>
		<div class="box box-info custom_box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("add")." - ".$this->lang->line("keyword"); ?></h3>
			</div><!-- /.box-header -->
			<!-- form start -->
			<form class="form-horizontal" action="<?php echo site_url().'keyword_position_tracking/keyword_tracking_settings_action';?>" method="POST">
				<div class="box-body">

					<!-- <div class="form-group">
						<label class="col-sm-3 control-label" >Name 
						</label>
						<div class="col-sm-9 col-md-6 col-lg-6">
							<input name="name" id="name" value="<?php echo set_value('name');?>"  class="form-control" type="text" required />		          
							<span class="red"><?php echo form_error('name'); ?></span>
						</div>
					</div> -->

					<div class="form-group">
						<label class="col-sm-3 control-label" ><?php echo $this->lang->line("keyword"); ?>
						</label>
						<div class="col-sm-9 col-md-6 col-lg-6">
							<input name="keyword" id="keyword" value="<?php echo set_value('keyword');?>"  class="form-control" type="text" required />		          
							<span class="red"><?php echo form_error('keyword'); ?></span>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label" ><?php echo $this->lang->line("website"); ?>
						</label>
						<div class="col-sm-9 col-md-6 col-lg-6">
							<input name="website" id="website" value="<?php echo set_value('website');?>"  class="form-control" type="text" required />		          
							<span class="red"><?php echo form_error('website'); ?></span>
						</div>
					</div>


					<div class="form-group">
						<label class="col-sm-3 control-label" ><?php echo $this->lang->line("country"); ?>
						</label>
						<div class="col-sm-9 col-md-6 col-lg-6">
							<?php 
							$country_name['']=$this->lang->line("Select Country");
							echo form_dropdown('country',$country_name,set_value('country'),' style="width:100%" class="form-control" id="country" required');  
							?>	          
							<span class="red"><?php echo form_error('country'); ?></span>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label" ><?php echo $this->lang->line("language"); ?>
						</label>
						<div class="col-sm-9 col-md-6 col-lg-6">
							<?php 
							$language_name['']=$this->lang->line("Select Language");
							echo form_dropdown('language',$language_name,set_value('language'),' style="width:100%" class="form-control" id="language" required');  
							?>	          
							<span class="red"><?php echo form_error('language'); ?></span>
						</div>
					</div>

				</div> <!-- /.box-body --> 
				<div class="box-footer">
					<div class="form-group">
						<div class="col-sm-12 text-center">							
							<?php if($number_of_keyword < 3) { ?>
							<input name="submit" id="submit" type="submit" class="btn btn-warning btn-lg" value="<?php echo $this->lang->line("save");?>"/> 
							<?php } ?>
							<input type="button" class="btn btn-default btn-lg" value="<?php echo $this->lang->line("cancel");?>" onclick='goBack("keyword_position_tracking/keyword_list")'/>  
						</div>
					</div>
				</div><!-- /.box-footer -->         
			</div><!-- /.box-info -->       
		</form>     
	</div>
</section>
</section>