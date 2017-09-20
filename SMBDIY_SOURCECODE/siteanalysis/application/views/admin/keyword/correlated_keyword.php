<br/>
<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2 well">
		<div class='text-center'><h4 class="text-info"><strong><?php echo $this->lang->line("correlated keywords");?></strong></h4></div>
		<form enctype="multipart/form-data" method="post" class="form-inline" id="new_search_form" style="margin-top:60px margin-left:10px">
			<div class="row">	
				<br/>			
				<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
					<input id="keyword" type="text" class="form-control" placeholder="Keyword *" style="width:100% !important;"/>
				</div> 
				<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
					<select class="form-control" id="country_name" name="country_name" style="width:100% !important;">
						<option value=""><?php echo $this->lang->line("select country");?> *</option>
						<option value="ar">Argentina</option>
						<option value="au">Australia</option>
						<option value="at">Austria</option>
						<option value="be">Belgium</option>
						<option value="br">Brazil</option>
						<option value="bg">Bulgaria</option>
						<option value="ca">Canada</option>
						<option value="cl">Chile</option>
						<option value="cn">China</option>
						<option value="co">Colombia</option>
						<option value="hr">Croatia</option>
						<option value="cz">Czech Republic</option>
						<option value="dk">Denmark</option>
						<option value="eg">Egypt</option>
						<option value="fi">Finland</option>
						<option value="fr">France</option>
						<option value="de">Germany</option>
						<option value="gr">Greece</option>
						<option value="hu">Hungary</option>
						<option value="in">India</option>
						<option value="id">Indonesia</option>
						<option value="ie">Ireland</option>
						<option value="il">Israel</option>
						<option value="it">Italy</option>
						<option value="jp">Japan</option>
						<option value="my">Malaysia</option>
						<option value="mx">Mexico</option>
						<option value="ma">Morocco</option>
						<option value="nl">Netherlands</option>
						<option value="nz">New Zealand</option>
						<option value="no">Norway</option>
						<option value="pe">Peru</option>
						<option value="ph">Philippines</option>
						<option value="pl">Poland</option>
						<option value="pt">Portugal</option>
						<option value="ro">Romania</option>
						<option value="ru">Russian Federation</option>
						<option value="sa">Saudi Arabia</option><option value="sg">Singapore</option>
						<option value="es">Spain</option><option value="se">Sweden</option>
						<option value="ch">Switzerland</option><option value="tw">Taiwan</option>
						<option value="th">Thailand</option><option value="tr">Turkey</option>
						<option value="ua">Ukraine</option>
						<option value="uk">United Kingdom</option>
						<option value="us">United States</option>
						<option value="ve">Venezuela</option>
						<option value="vn">Viet Nam</option>
					</select>							
				</div>
			</div>
			<br/>
			
			<div class="row">
				<div class="form-group col-xs-12 clearfix  text-center">    
					<button type="button"  id="new_search_button" class="btn btn-info"><i class="fa fa-search"></i> <?php echo $this->lang->line("start searching");?></button>
				</div>
				
			</div>

		</form>
	</div>
</div>


<script>	

$j("document").ready(function(){
		


		var base_url="<?php echo base_url(); ?>";
		
		$("#new_search_button").on('click',function(){
			$("#download_div").html('');
			var keyword=$("#keyword").val();
			var country=$("#country_name").val();
			
			if(keyword=='')
			{
				alert("<?php echo $this->lang->line("please enter keyword");?>");
				return false;
			}

			if(country=='')
			{
				alert("<?php echo $this->lang->line("you have not select any country");?>");
				return false;
			}
			
			$("#modal_result").modal();
			
			$("#success_msg").html('<img class="center-block" src="'+base_url+'assets/pre-loader/Fancy pants.gif" alt="<?php echo $this->lang->line("please wait");?>"><br/>');
			$.ajax({
				url:base_url+'keyword/google_correlated_keyword_action',
				type:'POST',
				data:{keyword:keyword,country:country},
				success:function(response)
				{	
					$("#success_msg").html(response);					
					$("#download_div").html('<a href="<?php echo base_url()."download/keyword_position/correlated_keyword_{$this->user_id}_{$this->download_id}.csv" ?>" target="_blank" class="btn btn-lg btn-warning"><i class="fa fa-cloud-download"></i> <b><?php echo $this->lang->line("download");?></b></a>');
				}
				
			});
			
			
		});
		
	});
	
</script>


				

<!-- Start modal for response-->
<div id="modal_result" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&#215;</span>
				</button>
				<h4 id="new_search_details_title" class="modal-title"><i class="fa fa-tags"></i> <?php echo $this->lang->line("correlated keywords");?></h4>
			</div><br/>


			<div id="new_search_view_body" class="modal-body">			
			
				 
				<div class="row">
					<div class="col-xs-12 text-center wow fadeInRight" id="download_div"></div>						
				</div>

				<div class="row"> 				
					<div class="col-xs-12 table-responsive" id="success_msg"></div>     
				</div>
				
				
			</div> <!-- End of body div-->

			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line("close");?></button>
			</div>
		</div>
	</div>
</div>
<!-- End modal for new search. -->
