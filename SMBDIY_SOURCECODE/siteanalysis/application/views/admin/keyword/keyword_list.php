<?php
	if($this->session->userdata('delete_success_message') == 1){

		echo "<div class='alert alert-success text-center'><h4 style='margin:0;'><i class='fa fa-check-circle'></i> ".$this->lang->line("your data has been successfully deleted from the database.")."</h4></div>";
		$this->session->unset_userdata('delete_success_message');
	}

	if($this->session->userdata('delete_error_message') == 1){
		echo "<div class='alert alert-success text-center'><h4 style='margin:0;'><i class='fa fa-check-circle'></i> ".$this->lang->line("your data has been failed to delete from the database.")."</h4></div>";
		$this->session->unset_userdata('delete_error_message');
	}

?>

<!-- Main content -->
<section class="content-header">
	<h1 class = 'text-info'><?php echo $this->lang->line("Keyword Tracking Settings");?> </h1>
</section>
<section class="content">  
	<div class="row" >
		<div class="col-xs-12">
			<div class="grid_container" style="width:100%; min-height:500px;">
				<table 
				id="tt"  
				class="easyui-datagrid" 
				url="<?php echo base_url()."keyword_position_tracking/keyword_list_data"; ?>" 

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
							<th field="keyword" sortable="true"><?php echo $this->lang->line("Keyword")?></th>
							<th field="website" sortable="true"><?php echo $this->lang->line("Website")?></th>
							<th field="country" sortable="true"><?php echo $this->lang->line("Country")?></th>
							<th field="language" sortable="true"><?php echo $this->lang->line("Language")?></th>
							<!-- <th field="add_date" sortable="true"><?php echo $this->lang->line("Add Date")?></th> -->
							<th field="view" formatter='action_column'><?php echo $this->lang->line("Actions")?></th>
						</tr>
					</thead>
				</table>                        
			</div>

			<div id="tb" style="padding:3px">

				<?php if($number_of_keyword < 3) { ?>
					<a style="margin-bottom: 5px;" class="btn btn-warning"  title="<?php echo $this->lang->line("add"); ?>" href="<?php echo site_url('keyword_position_tracking/keyword_tracking_settings');?>">
				    <i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("add"); ?>
					</a><br/>
 
				<?php } else echo "<h4><div class='alert alert-warning text-center'>You can add maximum 3 keywords.</div></h4>"; ?>


			</div>        
		</div>
	</div>   
</section>

<script>

	var base_url="<?php echo site_url(); ?>"
    
    function action_column(value,row,index)
    {               
        var delete_url=base_url+'keyword_position_tracking/delete_keyword_action/'+row.id;
        
        var str=""; 
        var delete_permission= 1;
        

        if(delete_permission == 1)
        str=str+"&nbsp;&nbsp;&nbsp;&nbsp;<a style='cursor:pointer' onclick=\"return confirm('"+'<?php echo $this->lang->line("are you sure that you want to delete this record?"); ?>'+"')\" title='Delete' href='"+delete_url+"'>"+' <img src="<?php echo base_url("plugins/grocery_crud/themes/flexigrid/css/images/close.png");?>" alt="Delete">'+"</a>";
        
        return str;
    }  

	
</script>
