	<?php 
	if($load_css_js==1) 
	{
		$include_css_js=include("application/views/site/report_css_js.php");
		echo "<!DOCTYPE html><html><head>".$include_css_js."</head></style><body>";
	}
	$lead_config=$this->basic->get_data("lead_config",array("where"=>array("status"=>"1")));
	if(isset($lead_config[0])) $direct_download="0";
	else $direct_download="1";
	echo "<input type='hidden' value='".$comparision_info[0]["id"]."' id='hidden_id'/>";		
	?>


	<?php 
		if($load_css_js==1) 
		{
			$path = 'assets/images/logo.png';
			$type = pathinfo($path, PATHINFO_EXTENSION);
			$data = file_get_contents($path);
			$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
			echo "<img style='margin-left:92px;max-width:500px;' src='".$base64."' alt='".$this->config->item("institute_address1")."'>";
			echo "<h3 class='text-center'>".$this->config->item("institute_address1")."</h3>";
			if($this->config->item("institute_address2")!="")
			echo "<h6 class='text-center'>Address: ".$this->config->item("institute_address2")."</h6>";
			echo "<h6 class='text-center'>Contact: ".$this->config->item("institute_email");
			echo " | ".$this->config->item("institute_mobile");
			echo "</h6><h6 class='text-center'>Website: <a href=".site_url()." target='_BLANK'>".site_url()."</a></h6>";
		}
	?>

	
	<div class="space"></div>
	<div class="space"></div>
	<div class="space"></div>
	<div class="space"></div>

	<?php 
	if($load_css_js==1) 
	{
		$headline="<br>Health Report";
		$searched_at="Compared at";
	}
	else
	{
		$headline=$this->lang->line("comparitive health report");
		$searched_at=$this->lang->line('compared at');
	}
	?>

	<h3 id="" class="title text-center"><?php echo $headline; ?> : <a href="<?php echo $site_info[0]["domain_name"]; ?>"  target="_BLANK"><?php echo $site_info[0]["domain_name"]; ?></a> Vs. <a href="<?php echo $site_info2[0]["domain_name"]; ?>"  target="_BLANK"><?php echo $site_info2[0]["domain_name"]; ?></a></h3>
	<p class="text-center slogan"> <?php echo $searched_at." : ".$comparision_info[0]["searched_at"]; ?></p> 
	

	<hr>
   
	<div class="container-fluid boss-container">
		<?php if($load_css_js!=1) {?>
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-lg-8 col-md-8 share-container text-center"><?php include("application/views/site/share_button.php");?></div>
				<div class="col-xs-12 col-sm-12 col-lg-4 col-md-4 share-sibing text-center"><a id="download_list" class="btn btn-lg btn-success"> <i class="fa fa-cloud-download"></i> <?php echo $this->lang->line("download pdf");?></a></div>
				<div class="col-xs-12">
					<div class="box box-green no_radius" id="subscribe_div" style="display:none;">
						<br>
						<div class="box-body chart-responsive minus">
							<div class="col-xs-12">
								<div id="success_msg"></div>
							</div>
							<div class="col-xs-12">
								<div class="alert alert-custom text-center" id="send_email_message"><?php echo $this->lang->line('the download link will be sent to your email'); ?></div>
							</div>
							<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
								<input type="text" class="form-control" id="name" required placeholder="<?php echo $this->lang->line('your name'); ?> *">
							</div>
							<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
								<input type="text" class="form-control" id="email" required placeholder="<?php echo $this->lang->line('your email'); ?> *">
							</div>
							<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
								<a class="btn btn-success btn-lg" id="send_email"> <i class="fa fa-send"></i> <?php echo $this->lang->line('send report'); ?></a>
							</div>
						</div>
						<br>
					</div>
				</div>
			</div>
			<div class="space"></div>
		<?php } ?>

		<?php if($this->is_ad_enabled && $this->is_ad_enabled1 && $load_css_js!=1) : ?>	
		<div class="add-970-90 hidden-xs hidden-sm"><?php echo $this->ad_content1; ?></div>
		<div class="add-320-100 hidden-md hidden-lg"><?php echo $this->ad_content1_mobile; ?></div>		
		<div class="space"></div>
		<?php endif; ?>	

		<div class="row">
			<div class="col-xs-12 col-md-6">
				<?php include("application/views/site/site_left.php"); ?>
			</div>
			<div class="col-xs-12 col-md-6">
				<?php include("application/views/site/site_right.php"); ?>
			</div>
		</div>
	</div>


	<?php if($load_css_js!=1) { ?>

	<style type="text/css" media="screen">
		.boss-container .box.tiny{min-height:200px !important;}
		.boss-container .box.small{min-height:375px !important;}
		.boss-container .box.medium{min-height:500px !important;}
		.boss-container .box.normal{min-height:600px !important;}
		.boss-container .box.large{min-height:700px !important;}
		.boss-container .box.huge{min-height:800px !important;}
		.boss-container .box.max{min-height:1000px !important;}
	</style>

	<script>
	$(document).ready(function(){
	    $('[data-toggle="popover"]').popover(); 

	    $(".minus").click(function() {
	    	$(this).parent().parent().next(".box-body").toggle();
		});
		$(".recommendation_link").click(function() {
	    	$(this).next(".recommendation").toggle();
		});

		$(document).on('click','#download_list',function(){
			var direct_download="<?php echo $direct_download;?>";
			if(direct_download=="1") 
			{			
				$("#subscribe_div").html('<div class="box-body chart-responsive minus"><div class="col-xs-12"><div style="font-size:18px" class="alert text-center"><img class="center-block" src="<?php echo site_url();?>assets/pre-loader/Fading squares.gif" alt="Processing..."><br/><?php echo $this->lang->line("this may take a while to generate pdf"); ?> </div></div></div>');
				$("#subscribe_div").css("background","#f1f1f1");
				$("#subscribe_div").show();
				var hidden_id=$("#hidden_id").val();
				var base_url="<?php echo site_url();?>";
				$.ajax({
				url:base_url+'health_check/direct_download_comparision',
				type:'POST',
				data:{hidden_id:hidden_id},
				success:function(response){
					$("#subscribe_div").html(response);
				  }
			   });	
			}

			else $("#subscribe_div").css("display","block");
		});





		$(document).on('click','#send_email',function(){
			var email=$("#email").val();
			var name=$("#name").val();
			var hidden_id=$("#hidden_id").val();

			
			if(email=="" || name=="") 
			{
				alert("<?php echo $this->lang->line('something is missing'); ?>");
				return;
			}

			$("#send_email_message").removeClass('alert-success');
			$("#send_email_message").removeClass('alert-danger');
			$("#send_email_message").addClass('alert-custom');
			$("#send_email_message").html("<?php echo $this->lang->line('this may take a while to generate pdf and send email'); ?>");
			$('#send_email').addClass('disabled');


			$("#success_msg").html('<img class="center-block" src="<?php echo site_url();?>assets/pre-loader/custom.gif" alt="Processing..."><br/>');
			var base_url="<?php echo site_url();?>";
			$.ajax({
			url:base_url+'health_check/send_download_link_comparision',
			type:'POST',
			data:{email:email,name:name,hidden_id:hidden_id},
			success:function(response){
				$("#send_email_message").show();
				$("#success_msg").html("");	
				$('#send_email').removeClass('disabled');
				if(response=="0")
				{
					$("#send_email_message").removeClass('alert-custom');
					$("#send_email_message").removeClass('alert-success');
					$("#send_email_message").addClass('alert-danger');
					$("#send_email_message").html("<?php echo $this->lang->line('you can not download more result using this email, download quota is crossed'); ?>");
				}					
				else
				{
					$("#send_email_message").removeClass('alert-custom');
					$("#send_email_message").removeClass('alert-danger');
					$("#send_email_message").addClass('alert-success');
					$("#send_email_message").html("<?php echo $this->lang->line('a email has been sent to your email'); ?>");
				}
			}
		});
	  });


	});
	</script>

	 <script type="text/javascript">
		var desktop_speed=$("#desktop_speed").val();
		$("#desktop_speed").myfunc({divFact:10,cusVal:desktop_speed});
		var mobile_speed=$("#mobile_speed").val();
		$("#mobile_speed").myfunc({divFact:10,cusVal:mobile_speed});
		var mobile_usability=$("#mobile_usability").val();
		$("#mobile_usability").myfunc({divFact:10,cusVal:mobile_usability});

		var desktop_speed2=$("#desktop_speed2").val();
		$("#desktop_speed2").myfunc({divFact:10,cusVal:desktop_speed2});
		var mobile_speed2=$("#mobile_speed2").val();
		$("#mobile_speed2").myfunc({divFact:10,cusVal:mobile_speed2});
		var mobile_usability2=$("#mobile_usability2").val();
		$("#mobile_usability2").myfunc({divFact:10,cusVal:mobile_usability2});
	</script>
	<script type="text/javascript">
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-36251023-1']);
	  _gaq.push(['_setDomainName', 'jqueryscript.net']);
	  _gaq.push(['_trackPageview']);

	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	</script>
<?php } ?>

<?php if($load_css_js==1) echo "</body></html>";?>