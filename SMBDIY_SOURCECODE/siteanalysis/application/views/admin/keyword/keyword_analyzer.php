<div class="row" style="margin-top: 20px;">
	<div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3 well" style="padding-bottom: 20px;">
		<h2 class="text-center" style="margin-bottom: 15px; margin-top: 0px;"><?php echo $this->lang->line("keyword analyzer");?></h2>
		
		<div>
			<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
				<input type="text" class="form-control" id="keyword_domain_name" placeholder="Domain Name">
			</div>
			<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
				<button class="btn btn-info"  id="keyword_domain_search"><i class="fa fa-hourglass-half"></i> <?php echo $this->lang->line("start analysis");?></button>
			</div>
		</div>
		<br/><br/><br/><br/>
		<div id="keyword_waiting_div" class="text-center"></div>
	</div>
</div>

<div class="row" style="margin-top: 30px; padding-left: 30px; padding-right: 30px;" id="keyword_analyzer_data">
	
</div>

<script>
	$(document.body).on('click','#keyword_domain_search',function(){
		var base_url="<?php echo base_url(); ?>";
		var domain_name = $("#keyword_domain_name").val();
		if(domain_name == '') alert('<?php echo $this->lang->line("you have not enter any domain name"); ?>');
		else {
			$('#keyword_waiting_div').html('<img class="center-block" style="" src="'+base_url+'assets/pre-loader/Fancy pants.gif" alt="<?php echo $this->lang->line("please wait"); ?>">');

			$("#keyword_analyzer_data").html('');
			$.ajax({
					type: "POST",
					url : "<?php echo site_url('keyword/keyword_analyzer_data'); ?>",
					data:{domain_name:domain_name},
					dataType: '',
					async: false,
					success:function(response){
						$('#keyword_waiting_div').html('');
						$("#keyword_analyzer_data").html(response);					
					}
				});
		}
	});
</script>