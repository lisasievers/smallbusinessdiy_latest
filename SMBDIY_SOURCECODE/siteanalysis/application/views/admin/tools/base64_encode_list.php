<br/>
<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2 well">
		<div class='text-center'><h4 class="text-info"><strong><?php echo $this->lang->line("Base64 Encoder/Decoder"); ?></strong></h4></div>
		<form enctype="multipart/form-data" method="post" class="form-inline text-center" id="new_search_form" style="margin-top:60px margin-left:10px">
			<div class="row">				
				<div class="form-group col-xs-12">
					<textarea id="base64" style="width:100%;padding:10px;" rows="10" placeholder="Put your contents here"></textarea>
				</div> 
			</div>
			
			<div class="row">						

				<div class="form-group col-xs-12 clearfix">
					<button type="button"  id="new_search_button" class="btn btn-info "><i class="fa fa-code"></i> </i> <?php echo $this->lang->line("start encoding"); ?></button>
					<button type="button"  id="new_search_button_decode" class="btn btn-info"><i class="fa fa-exchange"></i> <?php echo $this->lang->line("start decoding"); ?></button>
				</div>
				
			</div>

		</form>
	</div>
</div>

<script src="<?=base_url()?>js/clipboard.min.js"></script>

<script>	

$j("document").ready(function(){
		
	var base_url="<?php echo base_url(); ?>";
	
	$("#new_search_button").on('click',function(){

		var base64=$("#base64").val();
		
		if(base64==''){
			alert("You have not enter any content");
			return false;
		}
		
		$("#modal_base64").modal();
		
		$("#success_msg").html('<img class="center-block" src="'+base_url+'assets/pre-loader/Fancy pants.gif" alt="Searching..."><br/>');
		$.ajax({
			url:base_url+'tools/base64_encode_action',
			type:'POST',
			data:{base64:base64},
			success:function(response){					
				
				$("#base64_download_div").html('<center><a  id="copyButton" class="btn btn-lg btn-warning" data-clipboard-action="copy" data-clipboard-target="#copyTarget"><i class="fa fa-copy"></i> <b><?php echo $this->lang->line("copy"); ?></b></a></center>');
				
				$("#success_msg").html('<center><h3><div class="table-responsive"> '+response+' </div></h3></center>');
				
			}
			
		});
		
		
	});
		
});

$("#new_search_button_decode").on('click',function(){

	var base_url="<?php echo base_url(); ?>";

	var base64=$("#base64").val();
	
	if(base64==''){
		alert("You have not enter any content");
		return false;
	}	
	
	$("#modal_base64").modal();
	
	$("#success_msg").html('<img class="center-block"  src="'+base_url+'assets/pre-loader/Fancy pants.gif" alt="Searching..."><br/>');
	$.ajax({
		url:base_url+'tools/base64_decode_action',
		type:'POST',
		data:{base64:base64},
		success:function(response){					
			
			$("#base64_download_div").html('<center><a id="copyButton" class="btn btn-lg btn-warning" data-clipboard-action="copy" data-clipboard-target="#copyTarget"><i class="fa fa-copy"></i> <b><?php echo $this->lang->line("copy"); ?></b></a></center>');

			$("#success_msg").html('<center><h3><div class="table-responsive"> '+response+' </div></h3></center>');
			
		}
		
	});
		
});

var clipboard = new Clipboard('.btn');

clipboard.on('success', function(e) {
    alert('Copied');
});

clipboard.on('error', function(e) {
    alert('Not Copied!');
});

</script>

<div id="modal_base64" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&#215;</span>
				</button>
				<h4 id="new_search_details_title" class="modal-title"><?php echo $this->lang->line("Base64 Encoder/Decoder"); ?></h4>
			</div><br/>


			<div id="new_search_view_body" class="modal-body">
				
			
				<div class="row"> 				
					<div class="col-xs-12" class="text-center" id="success_msg"></div>     
				</div> 
				<div class="row">
					<div class="col-xs-9 col-xs-offset-2 col-sm-9 col-sm-offset-2 col-md-9 col-md-offset-2 col-lg-9 col-lg-offset-2 wow fadeInRight">		  
						<div class="loginmodal-container">
				
							<div id="base64_download_div" style="display:inline">
				
							</div>
							
						</div>
					</div>						
				</div>

				
				
				
			</div> <!-- End of body div-->

			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line("close"); ?></button>
			</div>
		</div>
	</div>
</div>