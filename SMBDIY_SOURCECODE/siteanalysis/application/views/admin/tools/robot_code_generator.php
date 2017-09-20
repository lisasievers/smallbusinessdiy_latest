
<section class="content-header">
   <section class="content">
     <div class="box box-info custom_box">
       <div class="box-header">
         <h3 class="box-title"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line("Robot Code Generator"); ?></h3>
       </div><!-- /.box-header -->
       <!-- form start -->
        
         <div class="form-horizontal" enctype="multipart/form-data"  method="POST" >
          <div class="box-body">

          <div id = 'basic_setting'>

           <div class="form-group">
             <label class="col-sm-3 control-label">Default -  All Robots are</label>
             <div class="col-sm-9 col-md-6 col-lg-6">               
               <select class="form-control" id="basic_all_robots">
                <option value="allowed" selected>Allowed</option>
                <option value="refused">Refused</option>                
              </select>
               <span class="red"><?php echo form_error('basic_all_robots'); ?></span>
             </div>
           </div>
         <!--   this is the seperator section ******************************** -->

           <div class="form-group">
             <label class="col-sm-3 control-label">Crawl-Delay</label>
             <div class="col-sm-9 col-md-6 col-lg-6">               
               <select class="form-control" id="crawl_delay">
                <option value="0" selected>Default- No delay</option>
                <option value="5">5 Seconds</option>                
                <option value="10">10 Seconds</option>                
                <option value="20">20 Seconds</option>                
                <option value="60">60 Seconds</option>                
                <option value="120" >120 Seconds</option>   
                               
              </select>
               <span class="red"><?php echo form_error('crawl_delay'); ?></span>
             </div>
           </div>
         <!--   this is the seperator section ******************************** -->

         <div class="form-group">
             <label class="col-sm-3 control-label">Sitemap</label>
             <div class="col-sm-9 col-md-6 col-lg-6">               
               <input type="text" class="form-control" id="site_map" placeholder="Leave Blank for none">
               <span class="red"><?php echo form_error('site_map'); ?></span>
             </div>
         </div>
         <!--   this is the seperator section ******************************** --> 
         </div> 

        <!--  *****************************************************************************************************  -->   
        <hr/>
          <span><center><h2 class="text-info"><?php echo $this->lang->line("OR"); ?></h2></center></span>
          <center><button id="custom_robot"  class="btn btn-info btn-lg">Specific Search Robots</button></center>
        <div id = "custom_setting" style = 'display:none'>
          
         

          <br/>

           <div class="form-group">
             <label class="col-sm-3 control-label">Crawl-Delay</label>
             <div class="col-sm-9 col-md-6 col-lg-6">               
               <select class="form-control" id="custom_crawl_delay">
                <option value="0" selected>Default- No delay</option>
                <option value="5">5 Seconds</option>                
                <option value="10">10 Seconds</option>                
                <option value="20">20 Seconds</option>                
                <option value="60">60 Seconds</option>                
                <option value="120" >120 Seconds</option>   
                               
              </select>
               <span class="red"><?php echo form_error('custom_crawl_delay'); ?></span>
             </div>
           </div>
         <!--   this is the seperator section ******************************** -->

         <div class="form-group">
             <label class="col-sm-3 control-label">Sitemap</label>
             <div class="col-sm-9 col-md-6 col-lg-6">               
               <input type="text" class="form-control" id="custom_site_map" placeholder="Leave Blank for none">
               <span class="red"><?php echo form_error('custom_site_map'); ?></span>
             </div>
         </div>
         <!--   this is the seperator section ******************************** -->

         <div class="form-group">
             <label class="col-sm-3 control-label"> Google</label>
             <div class="col-sm-9 col-md-6 col-lg-6">               
               <select class="form-control" id="google">
                <option value="allowed" selected>Allowed</option>
                <option value="refused">Refused</option>               
              </select>
               <span class="red"><?php echo form_error('google'); ?></span>
             </div>
           </div>
         <!--   this is the seperator section ******************************** -->

         <div class="form-group">
             <label class="col-sm-3 control-label">MSN Search</label>
             <div class="col-sm-9 col-md-6 col-lg-6">               
               <select class="form-control" id="msn_search">
                <option value="allowed" selected>Allowed</option>
                <option value="refused">Refused</option>                
              </select>
               <span class="red"><?php echo form_error('msn_search'); ?></span>
             </div>
           </div>
         <!--   this is the seperator section ******************************** -->

         <div class="form-group">
             <label class="col-sm-3 control-label"> Yahoo</label>
             <div class="col-sm-9 col-md-6 col-lg-6">               
               <select class="form-control" id="yahoo">
               <option value="allowed" selected>Allowed</option>
                <option value="refused">Refused</option>               
              </select>
               <span class="red"><?php echo form_error('yahoo'); ?></span>
             </div>
           </div>
         <!--   this is the seperator section ******************************** -->

         <div class="form-group">
             <label class="col-sm-3 control-label"> Ask/Teoma</label>
             <div class="col-sm-9 col-md-6 col-lg-6">               
               <select class="form-control" id="ask_teoma">
               <option value="allowed" selected>Allowed</option>
                <option value="refused">Refused</option>               
              </select>
               <span class="red"><?php echo form_error('ask_teoma'); ?></span>
             </div>
           </div>
         <!--   this is the seperator section ******************************** -->

          <div class="form-group">
             <label class="col-sm-3 control-label"> Cuil</label>
             <div class="col-sm-9 col-md-6 col-lg-6">               
               <select class="form-control" id="cuil">
               <option value="allowed" selected>Allowed</option>
                <option value="refused">Refused</option>               
              </select>
               <span class="red"><?php echo form_error('cuil'); ?></span>
             </div>
           </div>
         <!--   this is the seperator section ******************************** -->

          <div class="form-group">
             <label class="col-sm-3 control-label"> GigaBlast</label>
             <div class="col-sm-9 col-md-6 col-lg-6">               
               <select class="form-control" id="gigablast">
               <option value="allowed" selected>Allowed</option>
                <option value="refused">Refused</option>               
              </select>
               <span class="red"><?php echo form_error('gigablast'); ?></span>
             </div>
           </div>
         <!--   this is the seperator section ******************************** -->

          <div class="form-group">
             <label class="col-sm-3 control-label"> Scrub The Web</label>
             <div class="col-sm-9 col-md-6 col-lg-6">               
               <select class="form-control" id="scrub">
               <option value="allowed" selected>Allowed</option>
                <option value="refused">Refused</option>               
              </select>
               <span class="red"><?php echo form_error('scrub'); ?></span>
             </div>
           </div>
         <!--   this is the seperator section ******************************** -->

          <div class="form-group">
             <label class="col-sm-3 control-label"> DMOZ Checker</label>
             <div class="col-sm-9 col-md-6 col-lg-6">               
               <select class="form-control" id="dmoz_checker">
               <option value="allowed" selected>Allowed</option>
                <option value="refused">Refused</option>               
              </select>
               <span class="red"><?php echo form_error('dmoz_checker'); ?></span>
             </div>
           </div>
         <!--   this is the seperator section ******************************** -->

          <div class="form-group">
             <label class="col-sm-3 control-label"> Nutch</label>
             <div class="col-sm-9 col-md-6 col-lg-6">               
               <select class="form-control" id="nutch">
               <option value="allowed" selected>Allowed</option>
                <option value="refused">Refused</option>               
              </select>
               <span class="red"><?php echo form_error('nutch'); ?></span>
             </div>
           </div>
         <!--   this is the seperator section ******************************** -->

          <div class="form-group">
             <label class="col-sm-3 control-label"> Alexa/Wayback</label>
             <div class="col-sm-9 col-md-6 col-lg-6">               
               <select class="form-control" id="alexa_wayback">
               <option value="allowed" selected>Allowed</option>
                <option value="refused">Refused</option>               
              </select>
               <span class="red"><?php echo form_error('alexa_wayback'); ?></span>
             </div>
           </div>
         <!--   this is the seperator section ******************************** -->

          <div class="form-group">
             <label class="col-sm-3 control-label"> Baidu</label>
             <div class="col-sm-9 col-md-6 col-lg-6">               
               <select class="form-control" id="baidu">
               <option value="allowed" selected>Allowed</option>
                <option value="refused">Refused</option>               
              </select>
               <span class="red"><?php echo form_error('baidu'); ?></span>
             </div>
           </div>
         <!--   this is the seperator section ******************************** -->

         <div class="form-group">
             <label class="col-sm-3 control-label"> Naver</label>
             <div class="col-sm-9 col-md-6 col-lg-6">               
               <select class="form-control" id="never">
               <option value="allowed" selected>Allowed</option>
                <option value="refused">Refused</option>               
              </select>
               <span class="red"><?php echo form_error('never'); ?></span>
             </div>
           </div>
         <!--   this is the seperator section ******************************** -->

         <hr/>
          <h4 class="text-center"><label class="control-label orange">Specific Special Bots</label></h4>

           <div class="form-group">
             <label class="col-sm-3 control-label"> Google Image</label>
             <div class="col-sm-9 col-md-6 col-lg-6">               
               <select class="form-control" id="google_image">
               
               <option value="allowed" selected>Allowed</option>
                <option value="refused">Refused</option>               
              </select>
               <span class="red"><?php echo form_error('google_image'); ?></span>
             </div>
           </div>
         <!--   this is the seperator section ******************************** -->

         <div class="form-group">
             <label class="col-sm-3 control-label"> Google Mobile</label>
             <div class="col-sm-9 col-md-6 col-lg-6">               
               <select class="form-control" id="google_mobile">
               
               <option value="allowed" selected>Allowed</option>
                <option value="refused">Refused</option>               
              </select>
               <span class="red"><?php echo form_error('google_mobile'); ?></span>
             </div>
           </div>
         <!--   this is the seperator section ******************************** -->

         <div class="form-group">
             <label class="col-sm-3 control-label"> Yahoo MM</label>
             <div class="col-sm-9 col-md-6 col-lg-6">               
               <select class="form-control" id="yahoo_mm">
              
               <option value="allowed" selected>Allowed</option>
                <option value="refused">Refused</option>               
              </select>
               <span class="red"><?php echo form_error('yahoo_mm'); ?></span>
             </div>
           </div>
         <!--   this is the seperator section ******************************** -->


         <div class="form-group">
             <label class="col-sm-3 control-label"> MSN PicSearch</label>
             <div class="col-sm-9 col-md-6 col-lg-6">               
               <select class="form-control" id="msn_picsearch">
               
               <option value="allowed" selected>Allowed</option>
                <option value="refused">Refused</option>               
              </select>
               <span class="red"><?php echo form_error('msn_picsearch'); ?></span>
             </div>
           </div>
         <!--   this is the seperator section ******************************** -->

         <div class="form-group">
             <label class="col-sm-3 control-label"> SingingFish</label>
             <div class="col-sm-9 col-md-6 col-lg-6">               
               <select class="form-control" id="singing_fish">
              
               <option value="allowed" selected>Allowed</option>
                <option value="refused">Refused</option>               
              </select>
               <span class="red"><?php echo form_error('singing_fish'); ?></span>
             </div>
           </div>
         <!--   this is the seperator section ******************************** -->

         <div class="form-group">
             <label class="col-sm-3 control-label"> Yahoo Blogs</label>
             <div class="col-sm-9 col-md-6 col-lg-6">               
               <select class="form-control" id="yahoo_blogs">
               
               <option value="allowed" selected>Allowed</option>
                <option value="refused">Refused</option>               
              </select>
               <span class="red"><?php echo form_error('yahoo_blogs'); ?></span>
             </div>
           </div>
         <!--   this is the seperator section ******************************** -->

          


          <hr/>
          <h4 class="text-center"><label class="control-label orange">Restricted Directories</label></h4><br/>
          <center><button type="button" id="btn2" class="btn btn-success"><i class='fa fa-plus-circle'></i> Add Directories</button></center><br/>
          <div id='directory_info'>
          <div class="form-group" >
             <label class="col-sm-3 control-label">Directory</label>
             <div class="col-sm-9 col-md-6 col-lg-6">               
               <input type="text" class="form-control" id = "restricted_dir0"  placeholder="Eg. /temp/img/" name="directory[]">               
             </div>
          </div>
          </div>
          <center><button id="close_custom_robot"  class="btn btn-warning btn-lg">Close Specific Search Robots</button></center> 
          </div>               
          
         </div><!-- /.box-body --> 
         <div class="box-footer">
            <div class="form-group">
             <div class="col-sm-12 text-center">
               <button id="submit" type="submit" class="btn btn-warning btn-lg"><?php echo $this->lang->line("save"); ?></button> 
              <input type="button" class="btn btn-default btn-lg" value="<?php echo $this->lang->line('cancel'); ?>" onclick='goBack("tools/robot_code_generator",0)'/>  
             </div>
           </div>                  
         </div><!-- /.box-footer -->         
         </div><!-- /.box-info -->   
        </div>         
     </div>
   </section>
</section>
<script>

var number_dir = 0;
var all_robot = 1;
var custom_robot = 0;

$j("document").ready(function(){

  $("#btn2").click(function()
  { 
    number_dir++;
    var added_dir = "restricted_dir"+number_dir;
    var str = '<div class="form-group" ><label class="col-sm-3 control-label">Directory</label><div class="col-sm-9 col-md-6 col-lg-6"><input type="text" class="form-control" id = "'+added_dir+'" placeholder="Eg. /temp/img/" name="directory[]"></div></div>';
   
    $("#directory_info").append(str);    
  });    

});

$(document.body).on('click','#custom_robot',function(){
  $('#custom_setting').slideDown(1000);
  all_robot = 0;
  custom_robot =1;
});

$(document.body).on('click','#close_custom_robot',function(){
  $('#custom_setting').slideUp(1000);
   all_robot = 1;
  custom_robot =0;
});

$(document.body).on('click','#submit',function(){

  var i;
  var dir_str = '';
  var dir = '';
  var restricted_dir = '';
  for(i = 0; i<= number_dir; i++){
    dir = 'restricted_dir'+i;
    dir_str = $('#'+dir+'').val();
    restricted_dir = restricted_dir+dir_str+',';
  }

  $("#modal_unique_email_result").modal();
      
      $("#success_msg").html('<img class="center-block" src="'+base_url+'assets/pre-loader/Fancy pants.gif" alt="Searching..."><br/>');

  
  var base_url="<?php echo base_url(); ?>";
    $.ajax({
      type:'POST',
      url: base_url+"tools/robot_code_generator_action",
      data:{all_robot:all_robot,
            custom_robot:custom_robot,

            basic_all_robots:$("#basic_all_robots").val(),
            crawl_delay:$("#crawl_delay").val(),
            site_map:$("#site_map").val(),
            custom_crawl_delay:$("#custom_crawl_delay").val(),
            custom_site_map:$("#custom_site_map").val(),
            google:$("#google").val(),
            msn_search:$("#msn_search").val(),
            yahoo:$("#yahoo").val(),
            ask_teoma:$("#ask_teoma").val(),
            cuil:$("#cuil").val(),
            gigablast:$("#gigablast").val(),
            scrub:$("#scrub").val(),
            dmoz_checker:$("#dmoz_checker").val(),
            nutch:$("#nutch").val(),
            alexa_wayback:$("#alexa_wayback").val(),
            baidu:$("#baidu").val(),
            never:$("#never").val(),

            google_image:$("#google_image").val(),
            google_mobile:$("#google_mobile").val(),
            yahoo_mm:$("#yahoo_mm").val(),
            msn_picsearch:$("#msn_picsearch").val(),
            SingingFish:$("#SingingFish").val(),
            yahoo_blogs:$("#yahoo_blogs").val(),
            restricted_dir:restricted_dir
            
      },
      success:function(response){
       $("#unique_email_download_div").html('<a href="<?php echo base_url()."download/robot/robot_{$this->user_id}_{$this->download_id}.txt" ?>" target="_blank" class="btn btn-lg btn-warning"><i class="fa fa-cloud-download"></i> <b><?php echo $this->lang->line("download"); ?></b></a>');
          
          $("#success_msg").html('<center><h3> '+response+' </h3></center>');

      }

    });
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
        <h4 id="new_search_details_title" class="modal-title"><?php echo $this->lang->line("Robot Code Generator"); ?></h4>
      </div><br/>


      <div id="new_search_view_body" class="modal-body">        
      
        <div class="row">         
          <div class="col-xs-12" class="text-center" id="success_msg"></div>     
        </div> 
        <div class="row">         
         <!--  <div class="col-xs-9 col-xs-offset-2 col-sm-9 col-sm-offset-2 col-md-9 col-md-offset-3 col-lg-9 col-lg-offset-3 wow fadeInRight">     
            <div class="loginmodal-container"> -->
              
              <div id="unique_email_download_div" class="text-center">
        
              </div>
                               
            <!-- </div>
          </div>  -->     
        </div>

        
        
        
      </div> <!-- End of body div-->

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line("close"); ?></button>
      </div>
    </div>
  </div>
</div>
<!-- End modal for new search. -->

