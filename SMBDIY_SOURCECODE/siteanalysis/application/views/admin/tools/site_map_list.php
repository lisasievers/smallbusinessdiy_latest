<br/>
<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2 well">
		<div class='text-center'><h4 class="text-info"><strong>Sitemap Generator</strong></h4></div>
		<form enctype="multipart/form-data" method="post" class="form-inline text-center" id="new_search_form" style="margin-top:60px margin-left:10px">
			<div class="row">				
				<div class="form-group col-xs-12">
					<input id="bulk_email" style="width:100%;padding:10px;" placeholder="URL"/>
					<br/>
				</div> 
			</div><br/>
			
			<div class="row">
				<div class="form-group clearfix">	
					<center><button type="button"  id="new_search_button" class="btn btn-info"><i class="fa fa-search"></i> Start Searching</button></center>
				</div>
				
			</div>

		</form>
	</div>
</div>


<script>

$j("document").ready(function(){
		
		var base_url="<?php echo base_url(); ?>";
		
		$("#new_search_button").on('click',function(){
			$("#email_download_div").html('');
			$("#valid_email_download_div").html('');
			var emails=$("#bulk_email").val();
			
			if(emails==''){
				alert("please enter emails");
				return false;
			}
			
			$("#modal_valid_email_result").modal();
			
			$("#success_msg").html('<img class="center-block" height="40px" width="100px" src="'+base_url+'assets/pre-loader/Fancy pants.gif" alt="Searching..."><br/>');
			$.ajax({
				url:base_url+'tools/sitemap_generator_action',
				type:'POST',
				data:{domain:emails},
				success:function(response){					
					
					/*$("#email_download_div").html('<a href="<?php echo base_url()."download/email_validator/email_validator_{$this->user_id}_{$this->download_id}.csv" ?>" target="_blank" class="btn btn-lg btn-warning"><i class="fa fa-cloud-download"></i> <b>Download Info</b></a>');
					
					$("#valid_email_download_div").html('<a href="<?php echo base_url()."download/email_validator/email_validator_{$this->user_id}_{$this->download_id}.txt" ?>" target="_blank" class="btn btn-lg btn-warning"><i class="fa fa-cloud-download"></i> <b>Download Valid Email</b></a>');*/
					
					$("#success_msg").html('<center><h3> '+response+' </h3></center>');
					
				}
				
			});
			
			
		});
		
	});
	
</script>


				

<!-- Start modal for response-->
<div id="modal_valid_email_result" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&#215;</span>
				</button>
				<h4 id="new_search_details_title" class="modal-title">Sitemap Generator</h4>
			</div><br/>


			<div id="new_search_view_body" class="modal-body">
				
			
				<div class="row"> 				
					<div class="col-xs-12" class="text-center" id="success_msg"></div>     
				</div> 
				<div class="row">
					<div class="col-xs-9 col-xs-offset-2 col-sm-9 col-sm-offset-2 col-md-9 col-md-offset-2 col-lg-9 col-lg-offset-2 wow fadeInRight">		  
						<div class="loginmodal-container">
				
							<div id="email_download_div" style="display:inline">
				
							</div>
							
							<div id="valid_email_download_div" style="display:inline">
				
							</div>
							                 
						</div>
					</div>						
				</div>

				
				
				
			</div> <!-- End of body div-->

			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<!-- End modal for new search. -->
