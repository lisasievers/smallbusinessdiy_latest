<?php $this->load->view('admin/theme/message'); ?>

<!-- Main content -->
<section class="content-header">
	<h1 class = 'text-info'><?php echo $this->lang->line("comparitive health report");?></h1>
</section>
<section class="content">  
	<div class="row" >
		<div class="col-xs-12">
			<div class="grid_container" style="width:100%; min-height:760px;">
				<table 
				id="tt"  
				class="easyui-datagrid" 
				url="<?php echo base_url()."admin/comparative_check_report_data"; ?>" 

				pagination="true" 
				rownumbers="true" 
				toolbar="#tb" 
				pageSize="15" 
				pageList="[5,10,15,20,50,100]"  
				fit= "true" 
				fitColumns= "true" 
				nowrap= "true" 
				view= "detailview"
				idField="id"
				>
				
					<!-- url is the link to controller function to load grid data -->					

					<thead>
						<tr>
							<th field="id"  checkbox="true"></th>
							<th field="base_domain" sortable="true"><?php echo $this->lang->line('website');?></th>
							<th field="competutor_domain" sortable="true"><?php echo $this->lang->line('competutor website');?></th>
							<th field="searched_at" sortable="true"><?php echo $this->lang->line('compared at');?></th>
							<th field="email" sortable="true"><?php echo $this->lang->line('user email');?></th>
							<th field="details" sortable="true"><?php echo $this->lang->line('report');?></th>
						</tr>
					</thead>
				</table>                        
			</div>

			<div id="tb" style="padding:3px">

			<form class="form-inline" style="margin-top:20px">

				<div class="form-group">
					<input id="domain_name" name="domain_name" class="form-control" size="20" placeholder="<?php echo $this->lang->line('website');?>">
				</div>

				<div class="form-group">
					<input id="compare_domain_name" name="compare_domain_name" class="form-control" size="20" placeholder="<?php echo $this->lang->line('competutor website');?>">
				</div>

				
				<div class="form-group">
					<select class="form-control" id="with_or_without" style="width:120px">
					  <option value=""><?php echo $this->lang->line("all report"); ?></option>
					  <option value="with"><?php echo $this->lang->line("compared & downloaded"); ?></option>
					  <option value="without"><?php echo $this->lang->line("only compared"); ?></option>
					</select>
				</div>


				<div class="form-group">
					<input id="email" name="email" style="width:150px" class="form-control" size="20" placeholder="<?php echo $this->lang->line('user email');?>">
				</div>  

				<div class="form-group">
					<input id="from_date" style="width:100px" name="from_date" class="form-control datepicker" size="20" placeholder="<?php echo $this->lang->line('from date');?>">
				</div>

				<div class="form-group">
					<input id="to_date"  style="width:100px"name="to_date" class="form-control  datepicker" size="20" placeholder="<?php echo $this->lang->line("to date");?>">
				</div>                    

				<button class='btn btn-info'  onclick="doSearch(event)"><i class="fa fa-binoculars"></i> <?php echo $this->lang->line("search report");?></button> <br/>  <br/>  
				<button type="button" style="width:220px;" class="btn btn-danger delete" id = "delete_btn" style = 'margin-bottom:10px'><i class="fa fa-times"></i> <?php echo $this->lang->line("delete selected");?></button>
				<button type="button" style="width:220px;" class="btn btn-danger delete" id = "delete_btn_all" style = 'margin-bottom:10px'><i class="fa fa-times"></i> <?php echo $this->lang->line("delete all");?></button>
			
			</div>  

			</form> 

			</div>        
		</div>
	</div>   
</section>


<script>

	$j(function() {
		$( ".datepicker" ).datepicker();
	});
	
	//section for Delete
	$(".delete").click(function(){
		var result = confirm("<?php echo $this->lang->line("are you sure that you want to delete this record?"); ?>");

		if(result)
		{
			
			var d_id=$(this).attr("id");
			var all=0;
			if(d_id=="delete_btn_all") all=1;
			$('#'+d_id).html('<i class="fa fa-spinner"></i> <?php echo $this->lang->line("please wait"); ?>');

			var base_url="<?php echo base_url(); ?>";		
			var url = "<?php echo site_url('admin/comparative_check_report_delete');?>";
	        var rows = $j("#tt").datagrid("getSelections");
	        var info=JSON.stringify(rows); 

	         /***For deleteing rows ***/
			var rowsLength = rows.length;	
			var rr = [];
			for (i = 0; i < rowsLength; i++) {
			     rr.push(rows[i]);
			}
			/****Sengment end for deleting rows*****/
	        if(rows == ''  && all==0)
	        {
	        	alert("<?php echo $this->lang->line('You have not select any record');?>");
	        	$('#delete_btn').html('<i class="fa fa-times"></i> <?php echo $this->lang->line("delete selected");?>');
	            return false;
	        }
	        $.ajax({
	            type:'POST',
	            url:url,
	            data:{info:info,all:all},
	            success:function(response){	
	            	if(all==1)
					$('#'+d_id).html('<i class="fa fa-times"></i> <?php echo $this->lang->line("delete all");?>');
					else $('#'+d_id).html('<i class="fa fa-times"></i> <?php echo $this->lang->line("delete selected");?>');
	
	            	/***For deleteing rows ***/					
					$.map(rr, function(row){
						var index = $j("#tt").datagrid('getRowIndex', row);
						$j("#tt").datagrid('deleteRow', index);
					});					
					/****Sengment end for deleting rows*****/ 
	            	$j('#tt').datagrid('reload'); 	              
	            }
	        });


		}//end of if.			

	});

	//End section for Delete.


	function doSearch(event)
	{
		event.preventDefault(); 
		$j('#tt').datagrid('load',{
			domain_name     :     $j('#domain_name').val(),              
			compare_domain_name     :     $j('#compare_domain_name').val(),              
			with_or_without :     $j('#with_or_without').val(),              
			email     		:     $j('#email').val(),              
			from_date  		:     $j('#from_date').val(),    
			to_date    		:     $j('#to_date').val(),         
			is_searched		:     1
		});


	}


	$(document.body).on('click','.email_list',function(){
		var email_list = $(this).attr('data');
		email_list = email_list.split(',');
		var emails = "";
		for(var i=0; i<email_list.length; i++){
			var j = i+1;
			emails += j + ". " +email_list[i] + "<br/>";
		}

		$("#show_email_list").html(emails);
		$("#modal_for_email_list").modal();
	});

</script>


<!-- Modal for email list -->
<div id="modal_for_email_list" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&#215;</span>
				</button>
				<h4 class="modal-title"><i class="fa fa-envelope"></i> <?php echo $this->lang->line('emails used to download report'); ?></h4>
			</div>

			<div class="modal-body">
				<!-- <div class="container"> -->
					<div class="row">
						<div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2">								
							<div id="show_email_list"></div>
						</div>
					</div>
				<!-- </div>	 -->
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
			</div>
		</div>
	</div>
</div>

