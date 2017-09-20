<?php $this->load->view('admin/theme/message'); ?>

<?php
	if($this->session->userdata('delete_error') == 1) {
		echo "<div class='alert alert-danger text-center'><h4 style='margin:0;'><i class='fa fa-remove'></i> Your data has been failed to delete from the database. Please try again ! </h4></div>";
		$this->session->unset_userdata('delete_error');
	}

	if($this->session->userdata('delete_success') == 1) {
		echo "<div class='alert alert-success text-center'><h4 style='margin:0;'><i class='fa fa-check-circle'></i> Your data has been successfully deleted from the database.</h4></div>";
		$this->session->unset_userdata('delete_success');
	}

    if($this->session->userdata('success_message')==1)
	{		
		echo "<div class='alert alert-success text-center'><h4 style='margin:0;'><i class='fa fa-check-circle'></i> Your data has been successfully stored into the database. </h4></div>";
		$this->session->unset_userdata('success_message');
	}
        $view_permission    = 1;
    /*if(in_array(3,$this->role_module_accesses_28))*/  
        $edit_permission    = 1;
    /*if(in_array(4,$this->role_module_accesses_28)) */ 
        $delete_permission  = 1;
?>
<!-- Content Header (Page header) -->

<style>
    #copyButton_for_js_code {
        background: white;
        color: black;
        padding-left: 5px;
        padding-right: 5px;
        margin-top: -15px;
        margin-right: -15px;
    }

    #copyButton_for_js_code:hover {
        cursor: pointer;
        background: #93228D;
        color: white;      
    }
</style>

<section class="content-header">
<h1> <?php echo $this->lang->line("visitor analytics : website list");?></h1>

</section>
<!-- Main content -->
<section class="content">  
  <div class="row">
    <div class="col-xs-12">
        <div class="grid_container" style="width:100%; height:550px;">
            <table 
            id="tt"  
            class="easyui-datagrid" 
            url="<?php echo base_url()."domain_details_visitor/domain_list_visitor_data"; ?>" 
            
            pagination="true" 
            rownumbers="true" 
            toolbar="#tb" 
            pageSize="10" 
            pageList="[5,10,20,50,100]"  
            fit= "true" 
            fitColumns= "true" 
            nowrap= "true" 
            view= "detailview"
            idField="id"
            >
            
                <thead>
                    <tr>
                        <th field="id" checkbox="true">ID.</th>                        
                        <th field="domain_name" sortable="true">Domain Name</th>
                        <th field="domain_code" sortable="true">Domain Code</th>
                        <th field="jscode" formatter="js_code_button">JavaScript Code</th>
                        <!-- <th field="update_data" formatter="update_data_button">Update Data</th> -->
                        <th field="view" formatter='action_column'><?php echo $this->lang->line("actions");?></th>
                    </tr>
                </thead>
            </table>                        
         </div>
  
       <div id="tb" style="padding:3px">
       
	       	<a class="btn btn-info"  <?php echo $this->lang->line("add website");?> href="<?php echo site_url('domain_details_visitor/add_domain');?>">
	       		<i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("add website");?>
	       	</a> 
              
            <form class="form-inline" style="margin-top:20px">

                <div class="form-group">
                    <input id="domain_name" name="domain_name" class="form-control" size="20" placeholder="Domain Name">
                </div> 

                <button class='btn btn-info'  onclick="doSearch(event)"><?php echo $this->lang->line("search");?></button>     

                      
            </form> 

        </div>        
    </div>
  </div>   
</section>


<!-- Start modal for js code. -->
<div id="modal_js_code" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&#215;</span>
				</button>
				<h4 id="" class="modal-title"><?php echo $this->lang->line("please copy this code");?></h4>
			</div>

			<div class="modal-body">                
                <h3 class="text-center" style="color:olive;"><?php echo $this->lang->line("copy the code below and paste it to your web page");?></h3>
                <div class="alert alert-success text-center clearfix">
                    <div id="copyButton_for_js_code" class="pull-right" data-toggle="tooltip" data-placement="top" title="<?php echo $this->lang->line("click to copy");?>"><?php echo $this->lang->line("click to copy");?></div><br/>
                    <p id="domain_view_body_for_js_code"></p>
                </div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line("close");?></button>
			</div>
            
		</div>
	</div>
</div>
<!-- End modal for js code. -->


<script type="text/javascript">       
    var base_url="<?php echo site_url(); ?>";
    
    function action_column(value,row,index)
    {               
        var details_url=base_url+'domain_details_visitor/domain_details/'+row.id;        
        var edit_url=base_url+'domain_details_visitor/update_domain/'+row.id;
        var delete_url=base_url+'domain_details_visitor/delete_domain/'+row.id;
        var delete_previous_data_url = base_url+'domain_details_visitor/delete_previous_data/'+row.id;
        
        var str="";
        var view_permission="<?php echo $view_permission; ?>";        
        var edit_permission="<?php echo $edit_permission; ?>";   
        var delete_permission="<?php echo $delete_permission; ?>";   
        
        var more="<?php echo $this->lang->line('more info');?>";
        var delete_str="<?php echo $this->lang->line('delete');?>";
        var delete_previous = "<?php echo $this->lang->line('clear data except last 30 days');?>";
        
        if(view_permission==1)     
        str="<a title='"+more+"' style='cursor:pointer' class='btn btn-primary' href='"+details_url+"'><i class='fa fa-binoculars'></i>"+more+"</a>";

        if(delete_permission == 1){

            str=str+"&nbsp;&nbsp;&nbsp;&nbsp;<a style='cursor:pointer' title='"+delete_str+"' class='btn btn-danger' href='"+delete_url+"' onclick=\"return confirm('"+'<?php echo $this->lang->line("are you sure that you want to delete this record?"); ?>'+"')\" ><i class='fa fa-close'></i> "+delete_str+"</a>";

            str=str+"&nbsp;&nbsp;&nbsp;&nbsp;<a style='cursor:pointer' title='"+delete_previous+"' class='btn btn-danger' href='"+delete_previous_data_url+"' onclick=\"return confirm('"+'<?php echo $this->lang->line("are you sure that you want to delete this record?"); ?>'+"')\" ><i class='fa fa-close'></i> "+delete_previous+"</a>";
        }

        
        return str;
    }

    function js_code_button(value,row,index)
    {
    	var str = '';
    	str = str+'<span class="label label-success js_code" style="cursor:pointer" data="'+row.js_code+'"><?php echo $this->lang->line("get js code"); ?></sapn>';
    	return str;
    }

    function update_data_button(value,row,index)
    {
        var update_link = base_url+'domain_details_visitor/update_common_info/'+row.id; 
        var str = '';
        str = str+'<a class="label label-warning update_data_button" href="'+update_link+'" style="cursor:pointer;">update data</a>';
        return str;
    }

    $(document.body).on('click','.js_code',function(){
    	var js_code = $(this).attr('data');
    	$("#modal_js_code").modal();
    	$("#domain_view_body_for_js_code").text(js_code);
    });
   
    function doSearch(event)
    {
        event.preventDefault(); 
        $j('#tt').datagrid('load',{
          domain_name:          $j('#domain_name').val(),
          is_searched:      1
        });


    }


</script>
