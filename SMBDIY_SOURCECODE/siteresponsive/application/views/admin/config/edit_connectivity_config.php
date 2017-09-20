<?php $this->load->view('admin/theme/message'); ?>
<?php 

if(array_key_exists(0,$config_data))
$google_api_key=$config_data[0]["google_api_key"]; 
else $google_api_key="";


 ?>
<section class="content-header">
   <section class="content">
     	<div class="box box-info custom_box">
		    	<div class="box-header">
		         <h3 class="box-title"><i class="fa fa-plug"></i> <?php echo $this->lang->line("google API settings");?></h3>
		        </div><!-- /.box-header -->
		       		<!-- form start -->

		    <form class="form-horizontal" enctype="multipart/form-data" action="<?php echo site_url().'admin_config_connectivity/edit_config';?>" method="POST">
		        

		        <div class="box-body">	

			        <div class="alert alert-info text-center">
			    		<?php echo $this->lang->line("please enter google API key and make sure google page speed insight is enabled."); ?>
			    		<br><br>
			    		<a href="https://www.youtube.com/watch?v=4CeF1k3Sdrw" target="_BLANK"><b><?php echo $this->lang->line("how to get google API key?"); ?></b></a>
			    	</div>	        	
		           	<div class="form-group">
		              	<label class="col-sm-3 control-label" for=""><?php echo $this->lang->line("google API key"); ?> *
		              	</label>
		                	<div class="col-sm-9 col-md-6 col-lg-6">
		               			<input name="google_api_key" value="<?php echo $google_api_key;?>"  class="form-control" type="text">		               
		             			<span class="red"><?php echo form_error('google_api_key'); ?></span>
		             		</div>
		            </div>
		           		                        
		           </div> <!-- /.box-body --> 

		           	<div class="box-footer">
		            	<div class="form-group">
		             		<div class="col-sm-12 text-center">
		               			<input name="submit" type="submit" class="btn btn-warning btn-lg" value="<?php echo $this->lang->line("Save");?>"/>  
		              			<input type="button" class="btn btn-default btn-lg" value="<?php echo $this->lang->line("Cancel");?>" onclick='goBack("admin_config_connectivity",1)'/>  
		             		</div>
		           		</div>
		         	</div><!-- /.box-footer -->         
		        </div><!-- /.box-info -->       
		    </form>     
     	</div>
   </section>
</section>



