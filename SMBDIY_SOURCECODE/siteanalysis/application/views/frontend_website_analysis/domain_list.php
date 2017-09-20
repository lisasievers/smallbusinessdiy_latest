<?php $this->load->view('admin/theme/message'); ?>

<?php
	if($this->session->userdata('delete_error') == 1) 
    {
		echo "<div class='alert alert-danger text-center'><h4 style='margin:0;'><i class='fa fa-remove'></i>".$this->lang->line("your data has been failed to delete from the database.")."</h4></div>";
		$this->session->unset_userdata('delete_error');
	}

	if($this->session->userdata('delete_success') == 1) 
    {
		echo "<div class='alert alert-success text-center'><h4 style='margin:0;'><i class='fa fa-check-circle'></i>".$this->lang->line("your data has been successfully deleted from the database.")."</h4></div>";
		$this->session->unset_userdata('delete_success');
	}

    if($this->session->userdata('success_message')==1)
	{		
		echo "<div class='alert alert-success text-center'><h4 style='margin:0;'><i class='fa fa-check-circle'></i>".$this->lang->line("your data has been successfully stored into the database.")."</h4></div>";
		$this->session->unset_userdata('success_message');
	}
        $view_permission    = 1;
        $edit_permission    = 1;
        $delete_permission  = 1;
?>
<!-- Content Header (Page header) -->

<style>
    #copyButton {
        background: white;
        color: black;
        padding-left: 5px;
        padding-right: 5px;
        margin-top: -15px;
        margin-right: -15px;
    }

    #copyButton:hover {
        cursor: pointer;
        background: #93228D;
        color: white;      
    }
</style>

<section class="content-header">
<h1> <?php echo $this->lang->line('website analysis'); ?> </h1>

</section>
<!-- Main content -->
<section class="content">  
  <div class="row">
    <div class="col-xs-12">
        <div class="grid_container" style="width:100%; height:550px;">
            <table 
            id="tt"  
            class="easyui-datagrid" 
            url="<?php echo base_url()."domain/domain_list_for_domain_details_data"; ?>" 
            
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
                        <th field="domain_name" sortable="true"><?php echo $this->lang->line("domain name");?></th>
                        <th field="search_at" sortable="true"><?php echo $this->lang->line("searched at");?></th>
                        <th field="view" formatter='action_column'><?php echo $this->lang->line("actions");?> </th>
                        <!-- <th field="pdf" formatter='action_pdf'><?php echo $this->lang->line("pdf");?> </th> -->
                    </tr>
                </thead>
            </table>                        
         </div>
  
       <div id="tb" style="padding:3px">
       
	       	<a class="btn btn-info"  title="<?php echo $this->lang->line('analyze website'); ?>" href="<?php echo site_url('domain/add_domain');?>">
	       		<i class="fa fa-hourglass-half"></i> <?php echo $this->lang->line('analyze website'); ?>
	       	</a> 
              
            <form class="form-inline" style="margin-top:20px">

                <div class="form-group">
                    <input id="domain_name" name="domain_name" class="form-control" size="20" placeholder="Domain Name">
                </div> 

                <button class='btn btn-info'  onclick="doSearch(event)"><?php echo $this->lang->line("search report");?></button>     

                      
            </form> 

        </div>        
    </div>
  </div>   
</section>



<script>       
    var base_url="<?php echo site_url(); ?>";
    
    function action_column(value,row,index)
    {               
        var details_url=base_url+'domain/domain_details_view/'+row.id;        
        var edit_url=base_url+'domain/update_domain/'+row.id;
        var delete_url=base_url+'domain/delete_domain/'+row.id;
        
        var str="";
        var view_permission="<?php echo $view_permission; ?>";        
        var edit_permission="<?php echo $edit_permission; ?>";   
        var delete_permission="<?php echo $delete_permission; ?>";   

        var more="<?php echo $this->lang->line('more info');?>";
        var delete_str="<?php echo $this->lang->line('delete');?>";

        
        if(view_permission==1)     
        str="<a title='"+more+"' style='cursor:pointer' class='btn btn-primary' href='"+details_url+"'><i class='fa fa-binoculars'></i> "+more+"</a>";


        if(delete_permission == 1)
        str=str+"&nbsp;&nbsp;&nbsp;&nbsp;<a style='cursor:pointer' title='"+delete_str+"' class='btn btn-danger' href='"+delete_url+"' onclick=\"return confirm('"+'<?php echo $this->lang->line("are you sure that you want to delete this record?"); ?>'+"')\" ><i class='fa fa-close'></i> "+delete_str+"</a>";

        var download_title="<?php echo $this->lang->line('download pdf');?>";
        str=str+"&nbsp;&nbsp;&nbsp;&nbsp;<a id='download_pdf' title='"+download_title+"' table_id="+row.id+" style='cursor:pointer' class='btn btn-warning'><i class='fa fa-cloud-download'></i> "+download_title+"</a>";
        
        return str;
    }


    // function action_pdf(value,row,index)
    // {
    //     var download_title="<?php echo $this->lang->line('download pdf');?>";
    //     var download_url =  base_url+'domain/download_pdf/'+row.id;
    //     str="<a target='_blank' title='"+download_title+"' style='cursor:pointer' class='btn btn-warning' href='"+download_url+"'><i class='fa fa-cloud-download'></i> "+download_title+"</a>";
    //     return str;
    // }


    function doSearch(event)
    {
        event.preventDefault(); 
        $j('#tt').datagrid('load',{
          domain_name:      $j('#domain_name').val(),
          is_searched:      1
        });


    }

    $(document.body).on('click','#download_pdf',function(){
        $("#download_div").hide();
        $("#waiting_div").show();
        $('#modal_for_download').modal();
        var base_url="<?php echo site_url(); ?>";
        var download_url =  base_url+'domain/download_pdf';
        var table_id = $(this).attr('table_id');
        $.ajax({
            url: download_url,
            type: 'POST',
            data: {table_id:table_id},
            success:function(response){
                var link = base_url+response;             
                $("#ajax_download_div").attr('href',link);
                $("#download_div").show();
                $("#waiting_div").hide();    
            }
        });
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
                <h4 id="" class="modal-title"><i class="fa fa-cloud-download"></i> <?php echo $this->lang->line('download'); ?></h4>
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
                    <div class="row text-center" id="waiting_div"><img src="<?php echo base_url('assets/pre-loader/Chasing blocks.gif');?>" alt="Please wait.."></div>
                    <div class="row" id="download_div">
                        <div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2">
                            <div class="box">
                            <h2><?php echo $this->lang->line('your file is ready to download'); ?></h2>
                            <?php 
                                echo '<i class="fa fa-2x fa-thumbs-o-up"style="color:black"></i><br><br>';
                                echo "<a id='ajax_download_div' href='' title='Download' class='btn btn-warning btn-lg' style='width:200px;'><i class='fa fa-cloud-download' style='color:white'></i>".$this->lang->line('download')."</a>";                         
                            ?>
                            </div>      
                            
                        </div>
                    </div>
                <!-- </div>  -->
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            </div>
        </div>
    </div>
</div>
