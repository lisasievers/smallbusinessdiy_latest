<?php $this->session->set_userdata('count',"1");?>
<section class="content-header">
   <section class="content">
     <div class="box box-info custom_box">
       <div class="box-header">
         <h3 class="box-title"><i class="fa fa-tags"></i> <?php echo $this->lang->line("Metatag Generator"); ?></h3>
       </div><!-- /.box-header -->
       <!-- form start -->
        
         <div class="form-horizontal">
          <div class="box-body">
      <!-- Section for google google google google google google google google google google google google google google -->

          <center><input type="checkbox" value="1" id = "google_check_box"/> Google</center><br/>

          <div id = 'google_block' style = "display:none">
           <div class="form-group">
             <label class="col-sm-3 control-label">Description </label>
             <div class="col-sm-9 col-md-6 col-lg-6">
               <input  id ="google_description" class="form-control" value="<?php echo set_value('google_description');?>" name="google_description" type="text"  >
               <span class="red"><?php echo form_error('google_description'); ?></span>
             </div>
           </div> 

           <!-- End of block ***********************************************************************           -->

           <div class="form-group">
             <label class="col-sm-3 control-label">Keywords </label>
             <div class="col-sm-9 col-md-6 col-lg-6">
                <textarea  id ="google_keywords" class="form-control" value="<?php echo set_value('google_keywords');?>" name="google_keywords" type="text" ></textarea>               
               <span class="red"><?php echo form_error('google_keywords'); ?></span>
             </div>
           </div> 

           <!-- End of block ***********************************************************************           -->

           <div class="form-group">
             <label class="col-sm-3 control-label">Author </label>
             <div class="col-sm-9 col-md-6 col-lg-6">
               <input  id ="google_author" class="form-control" value="<?php echo set_value('google_author');?>" name="google_author" type="text"  >
               <span class="red"><?php echo form_error('google_author'); ?></span>
             </div>
           </div> 

           <!-- End of block ***********************************************************************           -->

           <div class="form-group">
             <label class="col-sm-3 control-label">Copyright </label>
             <div class="col-sm-9 col-md-6 col-lg-6">
               <input  id ="google_copyright" class="form-control" value="<?php echo set_value('google_copyright');?>" name="google_copyright" type="text"  >
               <span class="red"><?php echo form_error('google_copyright'); ?></span>
             </div>
           </div> 

           <!-- End of block ***********************************************************************           -->

           <div class="form-group">
             <label class="col-sm-3 control-label">Application Name </label>
             <div class="col-sm-9 col-md-6 col-lg-6">
               <input  id ="google_application_name" class="form-control" value="<?php echo set_value('google_application_name');?>" name="google_application_name" type="text"  >
               <span class="red"><?php echo form_error('google_application_name'); ?></span>
             </div>
           </div> 

           <!-- End of block ***********************************************************************           -->
           </div>



      <!-- End Section for google google google google google google google google google google google google google google -->

          <hr/>

          <!-- Section for Facebook Facebook Facebook Facebook Facebook Facebook Facebook Facebook Facebook  -->

          <center><input type="checkbox" value="1" id = "facebook_check_box"/> Facebook</center><br/>
          <div id="facebook_block" style="display: none">
           <div class="form-group">
             <label class="col-sm-3 control-label">Title </label>
             <div class="col-sm-9 col-md-6 col-lg-6">
               <input  id ="facebook_title" class="form-control" value="<?php echo set_value('facebook_title');?>" name="facebook_title" type="text"  >
               <span class="red"><?php echo form_error('facebook_title'); ?></span>
             </div>
           </div> 

           <!-- End of block ***********************************************************************           -->

           <div class="form-group">
             <label class="col-sm-3 control-label">Type </label>
             <div class="col-sm-9 col-md-6 col-lg-6">
               <input  id ="facebook_type" class="form-control" value="<?php echo set_value('facebook_type');?>" name="facebook_type" type="text"  >
               <span class="red"><?php echo form_error('facebook_type'); ?></span>
             </div>
           </div> 

           <!-- End of block ***********************************************************************           -->

           <div class="form-group">
             <label class="col-sm-3 control-label">Image URL</label>
             <div class="col-sm-9 col-md-6 col-lg-6">
               <input  id ="facebook_image" class="form-control" value="<?php echo set_value('facebook_image');?>" name="facebook_image" type="text"  >
               <span class="red"><?php echo form_error('facebook_image'); ?></span>
             </div>
           </div> 

           <!-- End of block ***********************************************************************           -->

           <div class="form-group">
             <label class="col-sm-3 control-label">Page URL</label>
             <div class="col-sm-9 col-md-6 col-lg-6">
               <input  id ="facebook_url" class="form-control" value="<?php echo set_value('facebook_url');?>" name="facebook_url" type="text"  >
               <span class="red"><?php echo form_error('facebook_url'); ?></span>
             </div>
           </div> 

           <!-- End of block ***********************************************************************           -->

           <div class="form-group">
             <label class="col-sm-3 control-label">Description </label>
             <div class="col-sm-9 col-md-6 col-lg-6">
               <input  id ="facebook_description" class="form-control" value="<?php echo set_value('facebook_description');?>" name="facebook_description" type="text"  >
               <span class="red"><?php echo form_error('facebook_description'); ?></span>
             </div>
           </div> 

           <!-- End of block ***********************************************************************           -->

            <div class="form-group">
             <label class="col-sm-3 control-label">App ID </label>
             <div class="col-sm-9 col-md-6 col-lg-6">
               <input  id ="facebook_app_id" class="form-control" value="<?php echo set_value('facebook_app_id');?>" name="facebook_app_id" type="text"  >
               <span class="red"><?php echo form_error('facebook_app_id'); ?></span>
             </div>
           </div> 

           <!-- End of block ***********************************************************************           -->

            <div class="form-group">
             <label class="col-sm-3 control-label">Localization </label>
             <div class="col-sm-9 col-md-6 col-lg-6">
               <input  id ="facebook_localization" class="form-control" value="<?php echo set_value('facebook_localization');?>" name="facebook_localization" type="text"  >
               <span class="red"><?php echo form_error('facebook_localization'); ?></span>
             </div>
           </div> 

           <!-- End of block ***********************************************************************           -->
           </div>

      <!-- End Section for Facebook Facebook Facebook Facebook Facebook Facebook Facebook Facebook Facebook  -->
      <hr/>

       <!-- Section for twiter twiter twiter twiter twiter twiter twiter twiter twiter twiter twiter twiter -->

         <center><input type="checkbox" value="1" id = "twiter_check_box"/> Twiter</center><br/>

          <div id = "twiter_block" style = "display: none">
           <div class="form-group">
             <label class="col-sm-3 control-label">Card </label>
             <div class="col-sm-9 col-md-6 col-lg-6">
               <input  id ="twiter_card" class="form-control" value="<?php echo set_value('twiter_card');?>" name="twiter_card" type="text"  >
               <span class="red"><?php echo form_error('twiter_card'); ?></span>
             </div>
           </div> 

           <!-- End of block ***********************************************************************           -->

           <div class="form-group">
             <label class="col-sm-3 control-label">Title </label>
             <div class="col-sm-9 col-md-6 col-lg-6">
               <input  id ="twiter_title" class="form-control" value="<?php echo set_value('twiter_title');?>" name="twiter_title" type="text"  >
               <span class="red"><?php echo form_error('twiter_title'); ?></span>
             </div>
           </div> 

           <!-- End of block ***********************************************************************           -->

           <div class="form-group">
             <label class="col-sm-3 control-label">Description </label>
             <div class="col-sm-9 col-md-6 col-lg-6">
               <input  id ="twiter_description" class="form-control" value="<?php echo set_value('twiter_description');?>" name="twiter_description" type="text"  >
               <span class="red"><?php echo form_error('twiter_description'); ?></span>
             </div>
           </div> 

           <!-- End of block ***********************************************************************           -->

           <div class="form-group">
             <label class="col-sm-3 control-label">Image URL</label>
             <div class="col-sm-9 col-md-6 col-lg-6">
               <input  id ="twiter_image" class="form-control" value="<?php echo set_value('twiter_image');?>" name="twiter_image" type="text"  >
               <span class="red"><?php echo form_error('twiter_image'); ?></span>
             </div>
           </div> 

           <!-- End of block ***********************************************************************           -->
           </div>



      <!-- End Section for google google google google google google google google google google google google google google -->
                           
           
         </div><!-- /.box-body --> 
         <div class="box-footer">
            <div class="form-group">
             <div class="col-sm-12 text-center">
               <input id="submit" type="submit" class="btn btn-warning btn-lg" value="<?php echo $this->lang->line('generate'); ?>"/>  
              <input type="button" class="btn btn-default btn-lg" value="<?php echo $this->lang->line('cancel'); ?>" onclick='goBack("tools/meta_tag_list",0)'/>  
             </div>
           </div>                  
         </div><!-- /.box-footer -->         
         </div><!-- /.box-info -->   
        </div>         
     </div>
   </section>
</section>


<script>

var is_google = 0;
var is_twiter = 0;
var is_facebook = 0;


$j("document").ready(function(){   
    
 //google ***************************************************   
        $("#google_check_box").on('click',function(){ 

         if ($('#google_check_box').is(":checked")){
          is_google = 1;
          $('#google_block').slideDown();
        } 

        else{
          is_google = 0;
          $('#google_block').slideUp();
        }


      });

  //facebook ****************************************

    $("#facebook_check_box").on('click',function(){ 

         if ($('#facebook_check_box').is(":checked")){
          is_facebook = 1;
          $('#facebook_block').slideDown();
        } 

        else{
          is_facebook = 0;
          $('#facebook_block').slideUp();
        }


      });

  //twiter ************************************************ 
  
   $("#twiter_check_box").on('click',function(){ 

         if ($('#twiter_check_box').is(":checked")){
          is_twiter = 1;
          $('#twiter_block').slideDown();
        } 

        else{
          is_twiter = 0;
          $('#twiter_block').slideUp();
        }


      }); 
    
  });

//Submit section **************************************

$(document.body).on('click','#submit',function(){ 


  
  var base_url="<?php echo base_url(); ?>";

  if(is_google ==0 && is_twiter ==0 && is_facebook == 0)
   alert("<?php echo $this->lang->line('one or more required fields are missing'); ?>");
  
  else{
     $("#modal_unique_email_result").modal();
      
      $("#success_msg").html('<img class="center-block"  src="'+base_url+'assets/pre-loader/Fancy pants.gif" alt="Searching..."><br/>'); 
    $.ajax({
      type:'POST',
      url: base_url+"tools/meta_tag_action",
      data:{is_google:is_google,
            is_facebook:is_facebook,
            is_twiter:is_twiter,
            google_description:$("#google_description").val(),          
            google_keywords:$("#google_keywords").val(),          
            google_copyright:$("#google_copyright").val(),          
            google_author:$("#google_author").val(),          
            google_application_name:$("#google_application_name").val(),          
            facebook_title:$("#facebook_title").val(),          
            facebook_type:$("#facebook_type").val(),          
            facebook_image:$("#facebook_image").val(),          
            facebook_url:$("#facebook_url").val(),          
            facebook_description:$("#facebook_description").val(),          
            facebook_app_id:$("#facebook_app_id").val(),          
            facebook_localization:$("#facebook_localization").val(),          
            twiter_card:$("#twiter_card").val(),          
            twiter_title:$("#twiter_title").val(),          
            twiter_description:$("#twiter_description").val(),          
            twiter_image:$("#twiter_image").val()          
      },
      success:function(response){
       
         $("#unique_email_download_div").html('<a href="<?php echo base_url()."download/metatag/metatag_{$this->user_id}_{$this->download_id}.txt" ?>" target="_blank" class="btn btn-lg btn-warning"><i class="fa fa-cloud-download"></i> <b><?php echo $this->lang->line("download"); ?></b></a>');
          
          $("#success_msg").html('<center><h3> '+response+' </h3></center>');
      }

    });

  }//end of else *******************
    
});




</script>

<!-- Start modal for response-->
<div id="modal_unique_email_result" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&#215;</span>
        </button>
        <h4 id="new_search_details_title" class="modal-title"><?php echo $this->lang->line("Metatag Generator"); ?></h4>
      </div><br/>


      <div id="new_search_view_body" class="modal-body">        
      
        <div class="row">         
          <div class="col-xs-12" class="text-center" id="success_msg"></div>     
        </div> 
        <div class="row">        
        
              <div id="unique_email_download_div" class="text-center">
        
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