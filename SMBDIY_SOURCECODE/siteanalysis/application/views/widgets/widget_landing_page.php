<div class="row" style="margin-top: 10px; margin-bottom: 30px; margin-right: 10px; margin-left: 10px;">
	<div class="col-xs-12">
		<h2 class="well text-center">Overview Report : Widget</h2>		
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="padding-left: 50px;">
		<!-- <input type="text" class="form-control" id="traffic_domain_name" placeholder="Domain Name"> -->
		<select id="traffic_domain_name" class="form-control">
			<option value=""><?php echo $this->lang->line("select your domain"); ?></option>
			<?php
				foreach($domain_name_array as $value){
					echo '<option value="'.$value['domain_name'].'">'.$value['domain_name'].'</option>';
				}
			?>
		</select>
		<input type="button" class="btn btn-info btn-lg form-control" id="traffic_button" value="<?php echo $this->lang->line("generate widget code"); ?>" style="margin-top: 8px; padding-top: 4px;">
	</div>
	<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
		<div class="col-xs-12" id="div_for_traffic_report_show"></div>
		<div class="col-xs-12" style="padding-right: 25px;">
			<div class="clearfix"><h1 style="margin: 0px;">Code</h1></div>
			<textarea name="" id="text_area_for_traffic_code" class="form-control" rows="5"></textarea>
		</div>
	</div>
</div>


<br/>
<div class="row" style="margin-top: 10px; margin-right: 10px; margin-left: 10px;">
	<div class="col-xs-12">
		<h2 class="well text-center">Country Wise New Visitor Report : Widget</h2>		
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="padding-left: 50px;">
		<!-- <input type="text" class="form-control" name="content_overview_domain_name" id="content_overview_domain_name" placeholder="Domain Name"> -->
		<select id="country_wise_domain_name" class="form-control">
			<option value=""><?php echo $this->lang->line("select your domain"); ?></option>
			<?php
				foreach($domain_name_array as $value){
					echo '<option value="'.$value['domain_name'].'">'.$value['domain_name'].'</option>';
				}
			?>
		</select>
		<input type="button" class="btn btn-info btn-lg form-control" id="country_report_button" value="<?php echo $this->lang->line("generate widget code"); ?>" style="margin-top: 8px; padding-top: 4px;">
	</div>
	<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
		<div class="col-xs-12" id="div_for_country_report_show"></div>
		<div class="col-xs-12" style="padding-right: 25px;">
			<div class="clearfix"><h1 style="margin: 0px;">Code</h1></div>
			<textarea name="" id="text_area_for_country_report_code" class="form-control" rows="5"></textarea>
		</div>
	</div>
</div>


<br/>
<div class="row" style="margin-top: 10px; margin-right: 10px; margin-left: 10px;">
	<div class="col-xs-12">
		<h2 class="well text-center">Content Overview Report : Widget</h2>		
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="padding-left: 50px;">
		<!-- <input type="text" class="form-control" name="content_overview_domain_name" id="content_overview_domain_name" placeholder="Domain Name"> -->
		<select id="content_overview_domain_name" class="form-control">
			<option value=""><?php echo $this->lang->line("select your domain"); ?></option>
			<?php
				foreach($domain_name_array as $value){
					echo '<option value="'.$value['domain_name'].'">'.$value['domain_name'].'</option>';
				}
			?>
		</select>
		<input type="button" class="btn btn-info btn-lg form-control" id="content_overview_button" value="<?php echo $this->lang->line("generate widget code"); ?>" style="margin-top: 8px; padding-top: 4px;">
	</div>
	<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
		<div class="col-xs-12" id="div_for_report_show"></div>
		<div class="col-xs-12" style="padding-right: 25px;">
			<div class="clearfix"><h1 style="margin: 0px;">Code</h1></div>
			<textarea name="" id="text_area_for_code" class="form-control" rows="5"></textarea>
		</div>
	</div>
</div>

<br/>
<br/>

<script>
	$(document.body).on('click','#content_overview_button',function(){
		var domain_name = $("#content_overview_domain_name").val();
		if(domain_name == '') alert('Please Select Your Domain First !');
		else {

			$("#div_for_report_show").html('');
			$("#text_area_for_code").text('');
			$.ajax({
					type: "POST",
					url : "<?php echo site_url('widgets/get_table_name_for_domain'); ?>",
					data:{domain_name:domain_name},
					dataType: '',
					async: false,
					success:function(response){
						if(response == 'no'){
							$("#div_for_report_show").html("<h1 class='text-center' style='color:red;'>No Data To Show</h1>");
						}else{
							$("#div_for_report_show").html(response);
							$("#text_area_for_code").text(response);
						}					
					}
				});
		}
	});
	
	$(document.body).on('click','#country_report_button',function(){
		var domain_name = $("#country_wise_domain_name").val();
		if(domain_name == '') alert('Please Select Your Domain First !');
		else {

			$("#div_for_country_report_show").html('');
			$("#text_area_for_country_report_code").text('');
			$.ajax({
					type: "POST",
					url : "<?php echo site_url('widgets/get_table_name_for_country_report'); ?>",
					data:{domain_name:domain_name},
					dataType: '',
					async: false,
					success:function(response){
						if(response == 'no'){
							$("#div_for_country_report_show").html("<h1 class='text-center' style='color:red;'>No Data To Show</h1>");
						}else{
							$("#div_for_country_report_show").html(response);
							$("#text_area_for_country_report_code").text(response);
						}					
					}
				});
		}
	});

	$(document.body).on('click','#traffic_button',function(){
		var domain_name = $("#traffic_domain_name").val();
		if(domain_name == '') alert("Please Select Your Domain First !");
		else {

			$("#div_for_traffic_report_show").html('');
			$("#text_area_for_traffic_code").text('');
			$.ajax({
					type: "POST",
					url : "<?php echo site_url('widgets/get_table_name_for_domain_traffic'); ?>",
					data:{domain_name:domain_name},
					dataType: '',
					async: false,
					success:function(response){
						if(response == 'no'){
							$("#div_for_traffic_report_show").html("<h1 class='text-center' style='color:red;'>No Data To Show</h1>");
						}else{
							$("#div_for_traffic_report_show").html(response);
							$("#text_area_for_traffic_code").text(response);
						}					
					}
				});
		}
	});
</script>