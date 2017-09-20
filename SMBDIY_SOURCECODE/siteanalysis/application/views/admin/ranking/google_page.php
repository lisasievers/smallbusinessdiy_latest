<?php $this->load->view('admin/theme/message'); ?>

<!-- Main content -->
<section class="content-header">
	<h1 class = 'text-info'> <?php echo $this->lang->line("google page rank");?></h1>
</section>
<section class="content">  
	<div class="row" >
		<div class="col-xs-12">
			<div class="grid_container" style="width:100%; min-height:760px;">
				<table 
				id="tt"  
				class="easyui-datagrid" 
				url="<?php echo base_url()."rank/google_page_rank_data"; ?>" 

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
							<th field="domain_name" sortable="true">Domain Name</th>
							<th field="google_page_rank" sortable="true">Google Page Rank</th>
							<th field="checked_at" sortable="true">Searched at</th>
						</tr>
					</thead>
				</table>                        
			</div>

			<div id="tb" style="padding:3px">

			<div class="row">
				<div class="col-xs-12">
					<button type="button" style="width:130px;" class="btn btn-primary" id ="new_search_modal_open"><i class="fa fa-search"></i>  <?php echo $this->lang->line("new search");?></button>
				</div>
			</div>
 

			<form class="form-inline" style="margin-top:20px">

				<div class="form-group">
					<input id="domain_name" name="domain_name" class="form-control" size="20" placeholder="<?php echo $this->lang->line("domain name");?>">
				</div>   

				<div class="form-group">
					<input id="from_date" name="from_date" class="form-control datepicker" size="20" placeholder="<?php echo $this->lang->line("from date");?>">
				</div>

				<div class="form-group">
					<input id="to_date" name="to_date" class="form-control  datepicker" size="20" placeholder="<?php echo $this->lang->line("to date");?>">
				</div>                    

				<button class='btn btn-info'  onclick="doSearch(event)"><?php echo $this->lang->line("search report");?></button> <br/>  <br/>  
				<button type="button" style="width:200px;" class="btn btn-info download" id = "download_btn"><i class="fa fa-cloud-download"></i><?php echo $this->lang->line("download selected");?></button>
				<button type="button" style="width:200px;" class="btn btn-info download" id = "download_btn_all"><i class="fa fa-cloud-download"></i> <?php echo $this->lang->line("download all");?></button>
				<button type="button" style="width:200px;" class="btn btn-primary delete" id = "delete_btn" style = 'margin-bottom:10px'><i class="fa fa-times"></i> <?php echo $this->lang->line("delete selected");?></button>
				<button type="button" style="width:200px;" class="btn btn-primary delete" id = "delete_btn_all" style = 'margin-bottom:10px'><i class="fa fa-times"></i> <?php echo $this->lang->line("delete all");?></button>
			
			</div>  

			</form> 

			</div>        
		</div>
	</div>   
</section>

<!-- Start modal for new search. -->
<div id="modal_new_search" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&#215;</span>
				</button>
				<h4 id="new_search_details_title" class="modal-title"><i class="fa fa-trophy"></i> <?php echo $this->lang->line("google page rank");?></h4>
			</div><br/>


			<div id="new_search_view_body" class="modal-body">
				<form enctype="multipart/form-data" method="post" class="form-inline" id="new_search_form" style="margin-bottom:10px">

			
					<div class="row">						
						<div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<?php echo $this->lang->line("enter domain name");?><br/>
							<textarea id="bulk_file" style="width:100%;padding:10px;" rows="7" placeholder="<?php echo $this->lang->line("put your domain names or upload text/csv file - comma / new line separated"); ?>"></textarea>					
							<br/><span style="margin-left:5%"><?php echo $this->lang->line("OR"); ?></span><br/> 
						</div>
					</div>

					<div class="row">						
						<div class="form-group col-xs-12 col-sm-12 col-md-5 col-lg-5">
							<input type="file" name="file_upload" id="file_upload" multiple/>
						</div>
						<div class="form-group col-xs-12 col-sm-12 col-md-7 col-lg-7 clearfix">						
							<button class='btn btn-success'  id ="pull_data"><i class="fa fa-upload"></i> <?php echo $this->lang->line("upload file"); ?></button>     
							<button type="button"  id="new_search_button" class="btn btn-info pull-right"><i class="fa fa-binoculars"></i> <?php echo $this->lang->line("start searching"); ?></button>   
						</div>
					</div>

				</form>
				
			
				<div class="row"> 
					
					<br/>
					<div class="col-xs-12" class="text-center" id="success_msg"></div>    
					 
					<div class="col-xs-12" class="text-center" id="progress_msg">
						<span id="progress_msg_text"></span>
						<div class="progress" style="display: none;" id="progress_bar_con"> 
							<div style="width:3%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="3" role="progressbar" class="progress-bar progress-bar-success progress-bar-striped"><span>1%</span></div> 
						</div>
					</div>     

					<div class="col-xs-12 wow fadeInRight">		  
						<div class="loginmodal-container">
							
							<div id="download_div" class="text-center">
								
							</div>
							
							<ol id="list">
								
							</ol>                     
						</div>
					</div>	

					<div class="col-xs-12 wow fadeInRight table-responsive" id="live_data">	
					</div>		
				</div> 


				
			</div> <!-- End of body div-->

			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line("close"); ?></button>
			</div>
		</div>
	</div>
</div>
<!-- End modal for new search. -->

<script>

	$j(function() {
		$( ".datepicker" ).datepicker();
	});
	

	$("#new_search_modal_open").click(function(){
		$("#modal_new_search").modal();
	});

	$(".download").click(function(){
		var base_url="<?php echo base_url(); ?>";
		
		var d_id=$(this).attr("id");
		var all=0;
		if(d_id=="download_btn_all") all=1;

		$('#'+d_id).html('<i class="fa fa-spinner"></i> <?php echo $this->lang->line("please wait"); ?>');
		var url = "<?php echo site_url('rank/google_page_rank_download');?>";
		var rows = $j("#tt").datagrid("getSelections");
		var info=JSON.stringify(rows); 
		if(rows == '' && all==0)
		{
			$('#download_btn').html('<i class="fa fa-cloud-download"></i>  <?php echo $this->lang->line("download selected"); ?>');
			alert("<?php echo $this->lang->line('You have not select any record');?>");
			return false;
		}
		$.ajax({
			type:'POST',
			url:url,
			data:{info:info,all:all},
			success:function(response)
			{
				if (response != '') 
				{
					if(all==1)
					$('#'+d_id).html('<i class="fa fa-cloud-download"></i>  <?php echo $this->lang->line("download all"); ?>');
					else $('#'+d_id).html('<i class="fa fa-cloud-download"></i>  <?php echo $this->lang->line("download selected"); ?>');
					$('#modal_for_download').modal();
					
				} else {
					alert("<?php echo $this->lang->line("something went wrong, please try again"); ?>");
				}
			}
		});
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
			var url = "<?php echo site_url('rank/google_page_rank_delete');?>";
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

	$("#pull_data").click(function(e){
		e.preventDefault();
		var site_url="<?php echo site_url();?>";  
		var queryString = new FormData($("#new_search_form")[0]);
		$.ajax({
			url: site_url+'home/read_text_file',
			type: 'POST',
			data: queryString,
			async: false,
			cache: false,
			contentType: false,
			processData: false,
			success:function(response){            	
				if(response!="0")
				$("#bulk_file").val(response);
				else 
				alert("<?php echo $this->lang->line("something went wrong, please try again"); ?>");	
			}
		});

	});

	function doSearch(event)
	{
		event.preventDefault(); 
		$j('#tt').datagrid('load',{
			domain_name     :     $j('#domain_name').val(),              
			from_date  		:     $j('#from_date').val(),    
			to_date    		:     $j('#to_date').val(),         
			is_searched		:      1
		});


	}   
	
	
	function get_bulk_progress()
	{
		var base_url="<?php echo base_url(); ?>";			
		$.ajax({
			url:base_url+'rank/bulk_google_page_rank_progress_count',
			type:'POST',
			dataType:'json',
			success:function(response){
				var search_complete=response.search_complete;
				var search_total=response.search_total;
				var latest_record=response.latest_record;
				$("#progress_msg_text").html(search_complete +" / "+ search_total +" <?php echo $this->lang->line('completed'); ?>");
				var width=(search_complete*100)/search_total;
				width=Math.round(width);					
				var width_per=width+"%";
				if(width<3)
				{
					$("#progress_bar_con div").css("width","3%");
					$("#progress_bar_con div").attr("aria-valuenow","3");
					$("#progress_bar_con div span").html("1%");
				}
				else
				{
					$("#progress_bar_con div").css("width",width_per);
					$("#progress_bar_con div").attr("aria-valuenow",width);
					$("#progress_bar_con div span").html(width_per);
				}

				if(width==100) 
				{
					$("#progress_msg_text").html("<?php echo $this->lang->line('completed'); ?>");
					$("#success_msg").html('<center><h3 style="color:olive;"><?php echo $this->lang->line("completed"); ?></h3></center>');
					$("#download_div").html('<center><a style="margin: 0px auto;" href="<?php echo base_url()."download/rank/google_page_rank_".$this->user_id."_".$this->session->userdata("download_id").".csv"; ?>" target="_blank" class="btn btn-lg btn-warning"><i class="fa fa-cloud-download"></i> <b><?php echo $this->lang->line("download"); ?></b></a></center>');
					clearInterval(interval);
				}
				
				
			}
		});
		
	}
	
	var interval="";

	$j("document").ready(function(){
		
		var base_url="<?php echo base_url(); ?>";
		
		$("#new_search_button").on('click',function(){
				
			var urls=$("#bulk_file").val();
			
			if(urls=='')
			{
				alert("<?php echo $this->lang->line('you have not enter any domain name'); ?>");
				return false;
			}

			$("#live_data").hide();
			$("#download_div").html("");
			$("#progress_bar_con div").css("width","3%");
			$("#progress_bar_con div").attr("aria-valuenow","3");
			$("#progress_bar_con div span").html("1%");
			$("#progress_msg_text").html("");				
			$("#progress_bar_con").show();				
			interval=setInterval(get_bulk_progress, 10000);

			
			$("#success_msg").html('<img class="center-block" src="'+base_url+'assets/pre-loader/Fancy pants.gif" alt="<?php echo $this->lang->line('please wait'); ?>"><br/>');
			
			$.ajax({
				url:base_url+'rank/google_page_rank_action',
				type:'POST',
				data:{urls:urls},
				success:function(response){		

					$("#live_data").show();		
					$("#live_data").html(response);		
					$("#progress_bar_con div").css("width","100%");
					$("#progress_bar_con div").attr("aria-valuenow","100");
					$("#progress_bar_con div span").html("100%");
					$("#progress_msg_text").html("<?php echo $this->lang->line('completed'); ?>");
					$("#success_msg").html('<center><h3 style="color:olive;"><?php echo $this->lang->line("completed"); ?></h3></center>');
					$("#download_div").html('<center><a style="margin: 0px auto;" href="<?php echo base_url()."download/rank/google_page_rank_".$this->user_id."_".$this->session->userdata("download_id").".csv"; ?>" target="_blank" class="btn btn-lg btn-warning"><i class="fa fa-cloud-download"></i> <b><?php echo $this->lang->line("download"); ?></b></a></center>');
					$j("#tt").datagrid('reload');					
				}
				
			});
			
			
		});
		
	});

	$('#modal_new_search').on('hidden.bs.modal', function () { 


			$("#live_data").hide();
			$("#download_div").html("");
			$("#progress_bar_con div").css("width","3%");
			$("#progress_bar_con div").attr("aria-valuenow","3");
			$("#progress_bar_con div span").html("1%");
			$("#progress_msg_text").html("");				
			$("#progress_bar_con").hide();				
			$("#bulk_file").val('');			
			$("#success_msg").html('');
			$("#file_upload").val('');

});
	
	
</script>

<!-- Modal for download -->
<div id="modal_for_download" class="modal fade">
	<div class="modal-dialog" style="width:65%;">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&#215;</span>
				</button>
				<h4 id="" class="modal-title"><i class="fa fa-cloud-download"></i> <?php echo $this->lang->line("download"); ?></h4>
			</div>

			<div class="modal-body">
				<style>
				.box
				{
					border:1px solid #ccc;	
					margin: 0 auto;
					text-align: center;
					margin-top:10%;
					padding-bottom: 20px;
					background-color: #fffddd;
					color:#000;
				}
				</style>
				<!-- <div class="container"> -->
					<div class="row">
						<div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2">
							<div class="box">
							<h2><?php echo $this->lang->line('your file is ready to download'); ?></h2>
							<?php 
								$download_id=$this->session->userdata('download_id');
								echo '<i class="fa fa-2x fa-thumbs-o-up"style="color:black"></i><br><br>';
								echo "<a href='".base_url()."download/rank/google_page_rank_".$this->user_id."_".$download_id.".csv"."'". "title='Download' class='btn btn-warning btn-lg' style='width:200px;'><i class='fa fa-cloud-download' style='color:white'></i> ".$this->lang->line('download')."</a>";							
							?>
							</div>		
							
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