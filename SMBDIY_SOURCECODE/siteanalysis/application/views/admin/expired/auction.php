<?php $this->load->view('admin/theme/message'); ?>

<!-- Main content -->
<section class="content-header">
	<h1 class = 'text-info'> <?php echo $this->lang->line("auction domain list");?></h1>
</section>
<section class="content">  
	<div class="row" >
		<div class="col-xs-12">
			<div class="grid_container" style="width:100%; min-height:760px;">
				<table 
				id="tt"  
				class="easyui-datagrid" 
				url="<?php echo base_url()."expired_domain/expired_domain_data"; ?>" 

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
							<th field="auction_type" sortable="true">Auction Type</th>
							<th field="auction_end_date" sortable="true">Auction End Date</th>
							<th field="sync_at" sortable="true">Sync. At</th>
						</tr>
					</thead>
				</table>                        
			</div>

			<div id="tb" style="padding:3px">
 

			<form class="form-inline" style="margin-top:20px">

				<div class="form-group">
					<input id="domain_name" name="domain_name" class="form-control" size="20" placeholder="Domain Name">
				</div>   

			
				<button class='btn btn-info'  onclick="doSearch(event)"><?php echo $this->lang->line("search report");?></button> <br/>  <br/>  
				<button type="button" style="width:195px;" class="btn btn-info download" id = "download_btn"><i class="fa fa-cloud-download"></i> <?php echo $this->lang->line("download selected");?></button>
				<button type="button" style="width:195px;" class="btn btn-info download" id = "download_btn_all"><i class="fa fa-cloud-download"></i> <?php echo $this->lang->line("download all");?></button>
			
			</div>  

			</form> 

			</div>        
		</div>
	</div>   
</section>


<script>



	$(".download").click(function(){
		var base_url="<?php echo base_url(); ?>";
		
		var d_id=$(this).attr("id");
		var all=0;
		if(d_id=="download_btn_all") all=1;

		$('#'+d_id).html('<i class="fa fa-spinner"></i> <?php echo $this->lang->line("please wait"); ?>');
		var url = "<?php echo site_url('expired_domain/expired_domain_download');?>";
		var rows = $j("#tt").datagrid("getSelections");
		var info=JSON.stringify(rows); 
		if(rows == '' && all==0)
		{
			$('#download_btn').html('<i class="fa fa-cloud-download"></i> <?php echo $this->lang->line("download selected"); ?>');
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
					$('#'+d_id).html('<i class="fa fa-cloud-download"></i> <?php echo $this->lang->line("download all"); ?>');
					else $('#'+d_id).html('<i class="fa fa-cloud-download"></i> <?php echo $this->lang->line("download selected"); ?>');
					$('#modal_for_download').modal();
					
				} else {
					alert("<?php echo $this->lang->line("something went wrong, please try again"); ?>");
				}
			}
		});
	});


	//End section for Delete.
	function doSearch(event)
	{
		event.preventDefault(); 
		$j('#tt').datagrid('load',{
			domain_name     :     $j('#domain_name').val(),      
			is_searched		:      1
		});


	} 

	
	
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
								echo "<a href='".base_url()."download/expired_domain/expired_".$this->user_id."_".$download_id.".csv"."'". "title='Download' class='btn btn-warning btn-lg' style='width:200px;'><i class='fa fa-cloud-download' style='color:white'></i> ".$this->lang->line('download')."</a>";							
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