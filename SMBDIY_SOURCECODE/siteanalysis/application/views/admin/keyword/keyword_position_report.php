<section class="content-header">
	<section class="content">
		<div class="row">
			<div class="well col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
				<p class="text-center"><b><?php echo $this->lang->line("Keyword Position Report")." - ".$this->lang->line("Search Panel"); ?></b></p>
				<form method="POST">
					<div class="form-group col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<br/>
						<?php 
						$keywords['']=$this->lang->line("select keyword");
						echo form_dropdown('keyword',$keywords,set_value('keyword'),' style="width:100%" class="form-control" id="keyword"');  
						?>								
					</div>
					<div class="form-group col-xs-12 col-sm-12 col-md-4 col-lg-4">
								<input type="text" class="form-control datepicker" id="from_date" placeholder="<?php echo $this->lang->line('from date'); ?>" style="width:100%; margin-top: 20px;">
					</div>

					<div class="form-group col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<input type="text" class="form-control datepicker" id="to_date" placeholder="<?php echo $this->lang->line('to date'); ?>" style="width:100%; margin-top: 20px;">
					</div>

					<div class="form-group col-xs-12 col-sm-12 col-md-4 col-md-offset-4 col-lg-4 col-lg-offset-4">
						<button id="search" class="btn btn-primary" style="width: 100%;"><?php echo $this->lang->line("Search"); ?></button>
					</div>

				</form>
			</div>
		</div>

		<div class="row" style="margin-top: 30px;padding-left: 15px;" id="report_data">
			
		</div>
	</section>
</section>

<script>
    $(".datepicker").datepicker();
    $(document.body).on('click','#search',function(e){
    		e.preventDefault();
    		var keyword = $("#keyword").val();
    		var from_date = $("#from_date").val();
    		var to_date = $("#to_date").val();
    		if(keyword == '' || from_date == '' || to_date == '')
    			alert("<?php echo $this->lang->line('something is missing');?>");
    		else{
    			$.ajax({
			        type:'POST',
			        async:false,
			        url:"<?php echo site_url();?>"+'keyword_position_tracking/keyword_position_report_data',
			        data:{keyword:keyword,from_date:from_date,to_date:to_date},
			        success:function(response){               
			          $("#report_data").html(response);
			        }                
			      });
    		}
    });
</script>