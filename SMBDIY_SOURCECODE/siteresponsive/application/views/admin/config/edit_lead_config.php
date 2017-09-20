<?php $this->load->view('admin/theme/message'); ?>
<?php 

if(array_key_exists(0,$config_data))
$mailchimp_api_key=$config_data[0]["mailchimp_api_key"]; 
else $mailchimp_api_key="";

if(array_key_exists(0,$config_data))
$mailchimp_list_id=$config_data[0]["mailchimp_list_id"]; 
else $mailchimp_list_id="";

if(array_key_exists(0,$config_data))
$allowed_download_per_email=$config_data[0]["allowed_download_per_email"]; 
else $allowed_download_per_email="";

if(array_key_exists(0,$config_data))
$unlimited_download_emails=$config_data[0]["unlimited_download_emails"]; 
else $unlimited_download_emails=$this->config->item("institute_email");

if(array_key_exists(0,$config_data))
$selected=$config_data[0]["status"]; 
else $selected="1";

if($selected==0) $class="disabled";
else $class="";


?> 
<section class="content-header">
   <section class="content">
     	<div class="box box-info custom_box">
		    	<div class="box-header">
		         <h3 class="box-title"><i class="fa fa-bullseye"></i> <?php echo $this->lang->line("lead settings");?></h3>
		        </div><!-- /.box-header -->
		       		<!-- form start -->

		    <form class="form-horizontal" enctype="multipart/form-data" action="<?php echo site_url().'admin_config_lead/edit_lead_config';?>" method="POST">
		        <div class="box-body">

		        	<div class="alert alert-info text-center">
			    		<a href="https://www.youtube.com/watch?v=ASoR20lszrY" target="_BLANK"><b>How to get Mailchimp API key & List ID?</b></a>
			    	</div>

		        	<div class="form-group">
		             	<label class="col-sm-3 control-label" for=""><?php echo $this->lang->line("status");?> *
		             	</label>
	             		<div class="col-sm-9 col-md-6 col-lg-6">	             			
	               			<?php	$status=array("1"=>$this->lang->line("i want to collect visitor email"),"0"=>$this->lang->line("i do not want to collect lvisitor email"));
							echo form_dropdown('status',$status,$selected,'class="form-control" id="status"');  ?>		          
	             			<span class="red"><?php echo form_error('status'); ?></span>
	             		</div>
		           </div> 

		        	<div class="form-group">
		              	<label class="col-sm-3 control-label" for=""><?php echo $this->lang->line("mailchimp API key"); ?> 
		              	</label>
		                	<div class="col-sm-9 col-md-6 col-lg-6">
		               			<input name="mailchimp_api_key" value="<?php echo $mailchimp_api_key;?>"  id="mailchimp_api_key"  class="change_status form-control" type="text" <?php echo $class; ?>>		               
		             			<span class="red"><?php echo form_error('mailchimp_api_key'); ?></span>
		             		</div>
		            </div>

		            <div class="form-group">
		              	<label class="col-sm-3 control-label" for=""><?php echo $this->lang->line("mailchimp list ID"); ?> 
		              	</label>
		                	<div class="col-sm-9 col-md-6 col-lg-6">
		               			<input name="mailchimp_list_id" value="<?php echo $mailchimp_list_id;?>"  id="mailchimp_list_id"  class="change_status form-control" type="text" <?php echo $class; ?>>		               
		             			<span class="red"><?php echo form_error('mailchimp_list_id'); ?></span>
		             		</div>
		            </div>

		        
		            <div class="form-group">
		              	<label class="col-sm-3 control-label" for=""><?php echo $this->lang->line("how many times a guest user can dowload using same email"); ?> 
		              	</label>
		                	<div class="col-sm-9 col-md-6 col-lg-6">
		               			<input name="allowed_download_per_email" value="<?php echo $allowed_download_per_email;?>"  id="allowed_download_per_email"  class="change_status form-control" type="text" <?php echo $class; ?>>		               
		             			<span class="red"><?php echo form_error('allowed_download_per_email'); ?></span>
		             		</div>
		            </div>

		            <div class="form-group">
		              	<label class="col-sm-3 control-label" for=""><?php echo $this->lang->line("email addresses that can download report unlimited times"); ?> 
		              	</label>
		                	<div class="col-sm-9 col-md-6 col-lg-6">
		               			<textarea placeholder="<?php echo $this->lang->line("type one or more comma seperated emails"); ?>" name="unlimited_download_emails"  id="unlimited_download_emails"  class="change_status form-control" <?php echo $class; ?>><?php echo $unlimited_download_emails;?></textarea>	               
		             			<span class="red"><?php echo form_error('unlimited_download_emails'); ?></span>
		             		</div>
		            </div>


		          		                        
		           </div> <!-- /.box-body --> 

		           	<div class="box-footer">
		            	<div class="form-group">
		             		<div class="col-sm-12 text-center">
		               			<input name="submit" type="submit" class="btn btn-warning btn-lg" value="<?php echo $this->lang->line("Save");?>"/>  
		              			<input type="button" class="btn btn-default btn-lg" value="<?php echo $this->lang->line("Cancel");?>" onclick='goBack("admin_config_lead/lead_config",1)'/>  
		             		</div>
		           		</div>
		         	</div><!-- /.box-footer -->         
		        </div><!-- /.box-info -->       
		    </form>     
     	</div>
   </section>
</section>

<script>
	$(document).ready(function() {
		$("#status").change(function(){
			var selected=$("#status").val();
			if(selected=="0")
			$(".change_status").attr('disabled','disabled');
			else
			$(".change_status").removeAttr('disabled');
		});
	});
</script>