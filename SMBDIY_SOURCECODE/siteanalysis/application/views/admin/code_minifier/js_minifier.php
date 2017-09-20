<?php $this->load->view("include/upload_js"); ?>

<section class="content-header">
   <section class="content">
     <div class="box box-info custom_box">
       <div class="box-header">
         <h3 class="box-title"><i class="fa fa-coffee"></i> <?php echo $this->lang->line('Js code minifier');?> </h3>
       </div><!-- /.box-header -->
	   
	   
       <!-- form start -->
	   
	   
       <form class="form-horizontal" method="POST">
	   
         <div class="box-body">	  

         	<div class="form-group">
	             <label class="col-sm-2 control-label" for=""></label>
	             <div class="col-sm-8 col-md-8 col-lg-8">
	             	<textarea style="width:100% !important;" class="form-control" rows="10" name="js_code" id="js_code" placeholder="<?php echo $this->lang->line('Write your js code here...');?>"></textarea><br/>
	             	<div style="display:none;" id="div_for_output">
	             		<div class="label label-success" style="font-size:16px;"><?php echo $this->lang->line('Minified output of the above code');?></div><br/>
		             	<textarea style="width:100% !important;" id="output_js_code" class="form-control" rows="10"></textarea><br/>
	             	</div>
	             	<button type="button" class="btn btn-info" id="minify_js"><?php echo $this->lang->line('minify the above code');?></button><br/>
	             </div>
            </div>
		   
			
            <div class="form-group">
            	<label class="col-sm-2 control-label" for="message"></label>
            	<div class="col-sm-8 col-md-8 col-lg-8">
            		<br/><br/>
            		<div class="alert alert-warning" style="font-size:16px;margin-bottom:10px;"><?php echo $this->lang->line('To minify your single or multiple js files, please select your files by clicking the upload button bellow.');?></div><br/>
            	</div>
            </div>

            <div class="form-group">
            	<label class="col-sm-2 control-label" for="message"></label>
            	<div class="col-sm-5 col-md-5 col-lg-5">
            		<div id="fileuploader"><?php echo $this->lang->line('Upload');?></div>
            	</div>
            </div>
		   
            
           </div> <!-- /.box-body --> 
		   
		   
                   
         </div><!-- /.box-info -->      
		  
       </form>     
     </div>
   </section>
</section>

<script>
$j("document").ready(function(){

	var base_url = "<?php echo base_url(); ?>";

	$(document.body).on('click','#minify_js',function(){
		var js_code = $("#js_code").val().trim();

		if(js_code==''){
			alert("<?php echo $this->lang->line('Please write your js code first');?>");
			return false;
		}

		$.ajax({
			type: "POST",
			url: base_url+"code_minifier/js_minifier_textarea",
			data:{code:js_code},
			async: false,
			success: function(data) {
				$("#output_js_code").text(data);
				$("#div_for_output").show();
			}
		});

	});
	

	$("#fileuploader").uploadFile({
		url:base_url+"code_minifier/js_minifier_action",
		fileName:"myfile",
		returnType: "json",
		dragDrop: true,
		showDelete: true,
		showDownload:true,
		multiple:true,
		sequential:true,
		sequentialCount:1,
		acceptFiles:".js",
		deleteCallback: function (data, pd) {
			var delete_url="<?php echo site_url('code_minifier/delete_js');?>";
		    for (var i = 0; i < data.length; i++) {
		        $.post(delete_url, {op: "delete",name: data[i]},
		            function (resp,textStatus, jqXHR) {		                		                
		            });
		    }
		},
		downloadCallback:function(filename,pd)
		{
			var download_url="<?php echo site_url('code_minifier/download_js');?>";
			location.href = download_url+"/"+encodeURI(filename);
		}

	});


});
</script>