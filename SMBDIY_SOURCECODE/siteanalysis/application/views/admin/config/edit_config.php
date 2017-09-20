<?php $this->load->view('admin/theme/message'); ?>
<section class="content-header">
   <section class="content">
     	<div class="box box-info custom_box">
		    	<div class="box-header">
		         <h3 class="box-title"><i class="fa fa-cogs"></i> <?php echo $this->lang->line("general settings");?></h3>
		        </div><!-- /.box-header -->
		       		<!-- form start -->
		    <form class="form-horizontal" enctype="multipart/form-data" action="<?php echo site_url().'admin_config/edit_config';?>" method="POST">
		        <div class="box-body">
		           	<div class="form-group">
		              	<label class="col-sm-3 control-label" for=""><?php echo $this->lang->line("company name");?>
		              	</label>
		                	<div class="col-sm-9 col-md-6 col-lg-6">
		               			<input name="institute_name" value="<?php echo $this->config->item('institute_address1');?>"  class="form-control" type="text">		               
		             			<span class="red"><?php echo form_error('institute_name'); ?></span>
		             		</div>
		            </div>
		           <div class="form-group">
		             	<label class="col-sm-3 control-label" for=""><?php echo $this->lang->line("company address");?>
		             	</label>
	             		<div class="col-sm-9 col-md-6 col-lg-6">
	               			<input name="institute_address" value="<?php echo $this->config->item('institute_address2');?>"  class="form-control" type="text">		          
	             			<span class="red"><?php echo form_error('institute_address'); ?></span>
	             		</div>
		           </div> 

		           <div class="form-group">
		             	<label class="col-sm-3 control-label" for=""><?php echo $this->lang->line("company email");?> *
		             	</label>
	             		<div class="col-sm-9 col-md-6 col-lg-6">
	               			<input name="institute_email" value="<?php echo $this->config->item('institute_email');?>"  class="form-control" type="email">		          
	             			<span class="red"><?php echo form_error('institute_email'); ?></span>
	             		</div>
		           </div>  


		           <div class="form-group">
		             	<label class="col-sm-3 control-label" for=""><?php echo $this->lang->line("company phone/ mobile");?>
		             	</label>
	             		<div class="col-sm-9 col-md-6 col-lg-6">
	               			<input name="institute_mobile" value="<?php echo $this->config->item('institute_mobile');?>"  class="form-control" type="text">		          
	             			<span class="red"><?php echo form_error('institute_mobile'); ?></span>
	             		</div>
		           </div>

		           <div class="form-group">
		             	<label class="col-sm-3 control-label" for=""><?php echo $this->lang->line("Page_title");?> 
		             	</label>
	             		<div class="col-sm-9 col-md-6 col-lg-6">
	               			<input name="slogan" value="<?php echo $this->config->item('slogan');?>"  class="form-control" type="text">		          
	             			<span class="red"><?php echo form_error('slogan'); ?></span>
	             		</div>
		           </div>

		           <div class="form-group">
		             	<label class="col-sm-3 control-label" for=""><?php echo $this->lang->line("product name");?> 
		             	</label>
	             		<div class="col-sm-9 col-md-6 col-lg-6">
	               			<input name="product_name" value="<?php echo $this->config->item('product_name');?>"  class="form-control" type="text">		          
	             			<span class="red"><?php echo form_error('product_name'); ?></span>
	             		</div>
		           </div>

		           <div class="form-group">
		             	<label class="col-sm-3 control-label" for=""><?php echo $this->lang->line("product short name");?> 
		             	</label>
	             		<div class="col-sm-9 col-md-6 col-lg-6">
	               			<input name="product_short_name" value="<?php echo $this->config->item('product_short_name');?>"  class="form-control" type="text">		          
	             			<span class="red"><?php echo form_error('product_short_name'); ?></span>
	             		</div>
		           </div>

		           <div class="form-group">
		             	<label class="col-sm-3 control-label" for=""><?php echo $this->lang->line("logo");?>
		             	</label>
	             		<div class="col-sm-9 col-md-6 col-lg-6" >
		           			<div class='text-center' style="background:#357CA5;padding:10px;"><img class="img-responsive" src="<?php echo base_url().'assets/images/logo.png';?>" alt="Logo"/></div>
	               			<?php echo $this->lang->line("Max Dimension");?> : 600 x 300, <?php echo $this->lang->line("Max Size");?> : 200KB,  <?php echo $this->lang->line("Allowed Format");?> : png
	               			<input name="logo" class="form-control" type="file">		          
	             			<span class="red"> <?php echo $this->session->userdata('logo_error'); $this->session->unset_userdata('logo_error'); ?></span>
	             		</div>
		           </div> 

		           <div class="form-group">
		             	<label class="col-sm-3 control-label" for=""><?php echo $this->lang->line("favicon");?>
		             	</label>
	             		<div class="col-sm-9 col-md-6 col-lg-6">
	             			<div class='text-center'><img class="img-responsive" src="<?php echo base_url().'assets/images/favicon.png';?>" alt="Favicon"/></div>
	               			 <?php echo $this->lang->line("Max Dimension");?> : 32 x 32, <?php echo $this->lang->line("Max Size");?> : 50KB, <?php echo $this->lang->line("Allowed Format");?> : png

	               			<input name="favicon"  class="form-control" type="file">		          
	             			<span class="red"><?php echo $this->session->userdata('favicon_error'); $this->session->unset_userdata('favicon_error'); ?></span>
	             		</div>
		           </div> 

		           <div class="form-group">
		             	<label class="col-sm-3 control-label" for=""><?php echo $this->lang->line("language");?>
		             	</label>
	             		<div class="col-sm-9 col-md-6 col-lg-6">	             			
	               			<?php
							$select_lan="english";
							if($this->config->item('language')!="") $select_lan=$this->config->item('language');
							echo form_dropdown('language',$language_info,$select_lan,'class="form-control" id="language"');  ?>		          
	             			<span class="red"><?php echo form_error('language'); ?></span>
	             		</div>
		           </div>

		        
		           <div class="form-group">
		             	<label class="col-sm-3 control-label" for=""><?php echo $this->lang->line("time zone");?>
		             	</label>
	             		<div class="col-sm-9 col-md-6 col-lg-6">	             			
	               			<?php	$time_zone['']="Time Zone";
							echo form_dropdown('time_zone',$time_zone,$this->config->item('time_zone'),'class="form-control" id="time_zone"');  ?>		          
	             			<span class="red"><?php echo form_error('time_zone'); ?></span>
	             		</div>
		           </div> 


		           <div class="form-group">
		             	<label class="col-sm-3 control-label" for=""><?php echo $this->lang->line("front-end website analysis search");?>
		             	</label>
	             		<div class="col-sm-9 col-md-6 col-lg-6">
	             			<?php  $checking='no'; $checking=$this->config->item('front_end_search_display'); ?>             			
	               			<label class="radio-inline"><input <?php if($checking == 'yes') echo 'checked'; ?> type="radio" name="front_end_search_display" value="yes">Show</label>
	               			<label class="radio-inline"><input <?php if($checking == 'no') echo 'checked'; ?> type="radio" name="front_end_search_display" value="no">Don't show</label>
	             		</div>
		           </div> 

		           
		         		               
		           </div> <!-- /.box-body --> 

		           	<div class="box-footer">
		            	<div class="form-group">
		             		<div class="col-sm-12 text-center">
		               			<input name="submit" type="submit" class="btn btn-warning btn-lg" value="<?php echo $this->lang->line("Save");?>"/>  
		              			<input type="button" class="btn btn-default btn-lg" value="<?php echo $this->lang->line("Cancel");?>" onclick='goBack("admin_config",1)'/>  
		             		</div>
		           		</div>
		         	</div><!-- /.box-footer -->         
		        </div><!-- /.box-info -->       
		    </form>     
     	</div>
   </section>
</section>



