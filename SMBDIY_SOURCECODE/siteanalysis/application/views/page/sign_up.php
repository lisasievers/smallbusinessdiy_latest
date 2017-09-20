
  <link href="<?php echo site_url(); ?>sign_up_page_layout/css/custom.css" rel="stylesheet">

  	<div class="container" >
	  	<div class="row" style="margin-top:10px;margin-bottom:30px;">
	  	    <!-- <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3 text-center">
	  			<img src="<?php echo site_url(); ?>assets/images/logo.png" alt="Logo" class="img-responsive logo">
	  	    </div> -->
	  	    <div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2 container_header_radius title-container blue text-center">		
		  		<h3 class='color_white'><?php echo $this->lang->line("sign up form");?></h3>
		    </div>
	  		<div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2 container_body_radius custom-container white border_gray">
				<div>
					<?php 
						if($this->session->userdata('reg_success') == 1) {
							echo "<div class='alert alert-success'>".$this->lang->line("an activation code has been sent to your email. please check your inbox to activate your account.")."</div>";
							$this->session->unset_userdata('reg_success');
						}
					?>
				</div>
	  			<form class="form-horizontal" method="post" action="<?php echo site_url('home/sign_up_action'); ?>">
	  				<div class="form-group">
	  					<!-- <label for="sname" class="col-xs-12 col-sm-12 col-md-3 col-lg-3 control-label">First Name*</label> -->
	  					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 input-group">
	  						<div class="input-group-addon"><i class="fa fa-user group_bg"></i></div>
	  						<input type="text" class="form-control right_border_radius" value="<?php echo set_value('name');?>" id="name" name="name" placeholder="<?php echo $this->lang->line("name");?> *">	  						
	  					</div>
	  					<span style="color:red;margin-top:5px;"><?php echo form_error('name'); ?></span>
	  				</div>


					<div class="form-group">
	  					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 input-group">
	  						<div class="input-group-addon"><i class="fa fa-mobile fa-2x group_bg"></i></div>
	  						<input type="text" class="form-control right_border_radius" value="<?php echo set_value('mobile');?>" id="mobile" name="mobile" placeholder="<?php echo $this->lang->line("mobile");?>">
	  					</div>
	  					<span style="color:red;margin-top:5px;"><?php echo form_error('mobile'); ?></span>				
					</div>



	  				<div class="form-group">
	  					<!-- <label for="semail" class="col-xs-12 col-sm-12 col-md-3 col-lg-3 control-label">Email*</label> -->
	  					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 input-group">
	  						<div class="input-group-addon"><i class="fa fa-envelope group_bg"></i></div>
	  						<input type="email" class="form-control right_border_radius" value="<?php echo set_value('email');?>" id="email" name="email" placeholder="<?php echo $this->lang->line("email");?> *">	  						
	  					</div>
	  					<span style="color:red;margin-top:5px;"><?php echo form_error('email'); ?></span>
	  				</div>
					
					
	  				<div class="form-group">
	  					<!-- <label for="spassword" class="col-xs-12 col-sm-12 col-md-3 col-lg-3 control-label">Password*</label> -->
	  					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 input-group">
	  						<div class="input-group-addon"><i class="fa fa-key group_bg"></i></div>
	  						<input type="password" class="form-control right_border_radius" value="<?php echo set_value('password');?>" name="password" id="password" placeholder="<?php echo $this->lang->line("password");?> *">
	  						
	  					</div>
	  					<span style="color:red;margin-top:5px;"><?php echo form_error('password'); ?></span>
	  				</div>
	  				
										
	  				<div class="form-group">
	  					<!-- <label for="spassword2" class="col-xs-12 col-sm-12 col-md-3 col-lg-3 control-label">Retype Password*</label> -->
	  					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 input-group">
	  						<div class="input-group-addon"><i class="fa fa-key group_bg"></i></div>
	  						<input type="password" class="form-control right_border_radius" value="<?php echo set_value('confirm_password');?>" name="confirm_password" id="confirm_password" placeholder="<?php echo $this->lang->line("confirm password");?> *">	  						
	  					</div>
	  					<span style="color:red;margin-top:5px;"><?php echo form_error('confirm_password'); ?></span>
	  				</div>

	  				<?php echo "<h4 class='text-center'>".$this->lang->line("captcha").": ".$num1. "+". $num2." = ?</h4>"; ?>
	  				<div class="form-group">
	  					<!-- <label for="spassword2" class="col-xs-12 col-sm-12 col-md-3 col-lg-3 control-label">Retype Password*</label> -->
	  					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 input-group">
	  						<div class="input-group-addon"><i class="fa  fa-android group_bg"></i></div>
	  						<input type="text" class="form-control right_border_radius" value="<?php echo set_value('captcha');?>" name="captcha" id="captcha" placeholder="<?php echo $this->lang->line("put the answer of captcha");?> *">	  						
	  					</div>
	  					<span style="color:red;margin-top:5px;">
		  					<?php 
		  					if(form_error('captcha')) 
		  					echo form_error('captcha'); 
		  					else  
		  					{ 
		  						echo $this->session->userdata("sign_up_captcha_error"); 
		  						$this->session->unset_userdata("sign_up_captcha_error"); 
		  					} ?>
	  					</span>
	  				</div>
	  				<div class="form-group">
	  					<div class="col-xs-12">
	  						<center><a data-toggle="modal" href='#modal-id'><?php echo $this->lang->line("by clicking register, you agree to the terms and conditions set out by this site");?></a></center>
	  						<div class="modal fade" id="modal-id">
	  							<div class="modal-dialog">
	  								<div class="modal-content">
	  									<div class="modal-header">
	  										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	  										<h4 class="modal-title text-center"><?php echo $this->lang->line("terms and condition"); ?></h4>
	  									</div>
	  									<div class="modal-body">
	  										<ul style="max-width: 700px;margin:0 auto;">
	  											<?php echo $this->lang->line("terms and condition description"); ?>
	  										</ul>
	  									</div>
	  									<div class="modal-footer">
	  										<center>
	  											<button type="button" class="btn btn-warning" data-dismiss="modal"><?php echo $this->lang->line("i agree"); ?></button>
	  											<a class="btn btn-default" href="<?php echo site_url(); ?>"><?php echo $this->lang->line("i do not agree"); ?></a>
	  										</center>
	  									</div>
	  								</div>
	  							</div>
	  						</div>
	  					</div>
	  				</div>
	  				<div class="form-group">
	  					<!-- <div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-3 col-lg-8 col-lg-offset-3"> -->
	  						<button type="submit" class="btn btn-primary btn-lg pull-left b_radius" id="sign_up_button"><b><?php echo $this->lang->line("sign up");?></b></button>	  						
	  						<a type="button" class="btn btn-default btn-lg pull-right b_radius" href="<?php echo site_url('home/index'); ?>" ><b><?php echo $this->lang->line("cancel");?></b></a>
	  					<!-- </div> -->
	  				</div>
	  				<hr>
	  				<div class="form-group">
	  					<div class="text-center">      
					        <?php echo  str_replace("ThisIsTheLoginButtonForGoogle",$this->lang->line("login with google"), $google_login_button); ?>
					    	<?php echo  str_replace("ThisIsTheLoginButtonForFacebook",$this->lang->line("login with facebook"), $fb_login_button); ?>
					    </div>	  					
	  				</div>	
	  			</form>
	  		</div>
	  	</div>
  	</div>	

