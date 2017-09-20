<?php 
	if($this->session->userdata('trans_error') == 1) 
	{
		echo "<div class='alert alert-danger text-center'><h4 style='margin:0;'><i class='fa fa-remove'></i> ".$this->lang->line("your data has been failed to stored into the database.")."</h4></div>";
		$this->session->unset_userdata('trans_error');
	}
?>
<style>
	#copy_button {
		background: white;
        color: black;
        padding-left: 5px;
        padding-right: 5px;
        margin-top: -15px;
        margin-right: -15px;
	}

	#copy_button:hover {
		cursor: pointer;
		background: orange;
		color: blue;
	}
.listsites{height:36px;}

</style>

<section class="content-header">
	<section class="content">
		<div class="box box-info custom_box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-hourglass-half"></i> <?php echo $this->lang->line('website analysis'); ?></h3>
			</div><!-- /.box-header -->
			<!-- form start -->
			<form class="form-horizontal" action="<?php echo site_url().'domain/add_domain_action';?>" method="POST">
				<div class="box-body">

					<div class="form-group">
						<label class="col-sm-3 control-label" >Domain Name 
						</label>
						<div class="col-sm-9 col-md-6 col-lg-6">
							<!--<input name="domain_name" id="domain_name" value="<?php //echo set_value('domain_name');?>"  class="form-control" type="text" required />-->
							<select name="domain_name" id="domain_name" class="listsites">
								<?php foreach($sites as $si){ ?>
								<option value="<?php echo $si->site_name; ?>"><?php echo $si->site_name; ?></option>
								<?php } ?>
							</select>
							<span class="red"><?php echo form_error('domain_name'); ?></span>
						</div>
					</div>

				</div> <!-- /.box-body --> 
				<div class="box-footer">
					<div class="form-group">
						<div class="col-sm-12 text-center">
							<input name="submit" id="submit" type="submit" class="btn btn-primary btn-lg" value="<?php echo $this->lang->line("analyze website");?>"/>  
							<input type="button" class="btn btn-default btn-lg" value="<?php echo $this->lang->line("cancel");?>" onclick='goBack("domain/domain_list_for_domain_details")'/>  
						</div>
					</div>
				</div><!-- /.box-footer -->         
			</form>	
		</div><!-- /.box-info -->       
	</section>
</section>


<!-- Start modal for js code. -->
<div id="modal_add_domain" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&#215;</span>
				</button>
				<h4 id="" class="modal-title"><i class="fa fa-hourglass-half"></i> <?php echo $this->lang->line('website analysis'); ?></h4>
			</div>

			<div class="modal-body">
				<br/>
				<div class="col-xs-12 text-center">
					<h2>Domain name : <span id="domain_name_show"></span></h2>
				</div>
				<div class="col-xs-12 text-center" id="domain_success_msg"></div>    
				 
				<div class="col-xs-12 text-center" id="progress_msg">
					<span id="domain_progress_msg_text"></span>
					<div class="progress" style="display: none;" id="domain_progress_bar_con"> 
						<div style="width:3%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="3" role="progressbar" class="progress-bar progress-bar-success progress-bar-striped"><span>1%</span></div> 
					</div>
				</div>
				<div class="col-xs-12 text-center"><h2 id="completed_result_link"></h2></div>
				<div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-4 col-lg-8 col-lg-offset-4" id="completed_function_str"></div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
			</div>
		</div>
	</div>
</div>
<!-- End modal for js code. -->

<script type="text/javascript">

	var interval="";

	function get_bulk_progress()
	{
		var base_url="<?php echo base_url(); ?>";			
		$.ajax({
			url:base_url+'domain/bulk_scan_progress_count',
			type:'POST',
			dataType:'json',
			success:function(response){
				var search_complete=response.search_complete;
				var search_total=response.search_total;
				var latest_record=response.latest_record;
				var view_details_button = response.view_details_button;

				$("#domain_progress_msg_text").html(search_complete +" / "+ search_total +" <?php echo $this->lang->line('step completed') ?>");
				$("#completed_function_str").html(response.completed_function_str);
				var width=(search_complete*100)/search_total;
				width=Math.round(width);					
				var width_per=width+"%";
				if(width<3)
				{
					$("#domain_progress_bar_con div").css("width","3%");
					$("#domain_progress_bar_con div").attr("aria-valuenow","3");
					$("#domain_progress_bar_con div span").html("1%");
				}
				else
				{
					$("#domain_progress_bar_con div").css("width",width_per);
					$("#domain_progress_bar_con div").attr("aria-valuenow",width);
					$("#domain_progress_bar_con div span").html(width_per);
				}

				if(width==100) 
				{
					$("#domain_progress_msg_text").html("<?php echo $this->lang->line('completed') ?>");
					$("#domain_success_msg").html('<center><h3 style="color:olive;"><?php echo $this->lang->line("completed") ?></h3></center>');
					$("#completed_result_link").html(response.view_details_button);					
					clearInterval(interval);
				}

				if(view_details_button != 'not_set')
				{
					$("#domain_progress_msg_text").html("<?php echo $this->lang->line('completed') ?>");
					$("#domain_success_msg").html('<center><h3 style="color:olive;"><?php echo $this->lang->line("completed") ?></h3></center>');
					$("#completed_result_link").html(response.view_details_button);					
					clearInterval(interval);
				}				
				
			}
		});
		
	}
	
	

	$('#submit').click(function(e){
		e.preventDefault();
		$('#modal_add_domain').modal();
		var domain_name = $('#domain_name').val().trim();
		$("#domain_name_show").html(domain_name);
		var base_url="<?php echo site_url(); ?>";
		
		if(domain_name == '') {
			alert("<?php echo $this->lang->line('you have not enter any domain name'); ?>");
		} else {

			$("#domain_progress_bar_con div").css("width","3%");
			$("#domain_progress_bar_con div").attr("aria-valuenow","3");
			$("#domain_progress_bar_con div span").html("1%");
			$("#domain_progress_msg_text").html("");				
			$("#domain_progress_bar_con").show();				
			interval=setInterval(get_bulk_progress, 10000);

			
			$("#domain_success_msg").html('<img class="center-block" src="'+base_url+'assets/pre-loader/Fancy pants.gif" alt="<?php echo $this->lang->line('please wait'); ?>"><br/>');
			$("#completed_result_link").html('');
			
			$.ajax({
				type:'POST' ,
				url: "<?php echo site_url(); ?>domain/add_domain_action",
				data:{domain_name:domain_name},
				success:function(response){
					if(response == 0){
						$('#modal_add_domain').modal('hide');
						alert("<?php echo $this->lang->line("something went wrong, please try again"); ?>");
					}						
					else {
						$("#domain_progress_bar_con div").css("width","100%");
						$("#domain_progress_bar_con div").attr("aria-valuenow","100");
						$("#domain_progress_bar_con div span").html("100%");
						$("#domain_progress_msg_text").html("<?php echo $this->lang->line('completed') ?>");
						$("#domain_success_msg").html('<center><h3 style="color:olive;"><?php echo $this->lang->line("completed") ?></h3></center>');
						$("#completed_result_link").html(response);
					}
				}
			}); 
		}
	});

	$('#modal_add_domain').on('hidden.bs.modal', function () { 
		var link="<?php echo site_url('domain/domain_list_for_domain_details'); ?>"; 
		window.location.assign(link); 
	})

</script>



