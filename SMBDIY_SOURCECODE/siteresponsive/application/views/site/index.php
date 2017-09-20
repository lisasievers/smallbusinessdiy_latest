<style>
.listsites{height:36px;}
</style>
<div id="banner" class="banner">
	
	<div class="banner-caption">				
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-10 object-non-visible" data-animation-effect="fadeIn">
					<h2 class="text-center product_name"> <span> <?php echo $this->config->item('product_name');?></span></h2>
					<p class="lead text-center">
						<?php //echo $this->lang->line("catch line"); ?></p>
						<div class="lead text-center form_holder">
							
							<?php 
							if($compare==1) 
							{
								$search_lang=$this->lang->line('type competitor web address');
							?>
							<input type="text" name="page_search" id="page_search" placeholder="<?php echo $search_lang; ?>" />
							<?php
							}
							else 
							{
								$search_lang=$this->lang->line('type web address');
							?>

							<select name="page_search" id="page_search" class="listsites">
								<?php foreach($sites as $si){ ?>
								<option value="<?php echo $si->site_name; ?>"><?php echo $si->site_name; ?></option>
								<?php } ?>
							</select>
							
							<?php 
							} 
							?>
						
							<button id="search" type="submit"> <i class="fa fa-search"></i> </button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- banner end -->

	<div class="space"></div>
	<div class="space"></div>

	<?php if($this->is_ad_enabled && $this->is_ad_enabled1) : ?>	
	<div class="add-970-90 hidden-xs hidden-sm"><?php //echo $this->ad_content1; ?></div>	
	<div class="add-320-100 hidden-md hidden-lg"><?php //echo $this->ad_content1_mobile; ?></div>	
	<?php endif; ?>		

	<!-- section start -->
	
	<div class="container-fluid">
	<h1 id="latest_search_report" class="title text-left"><?php echo $this->lang->line("recent health check report"); ?></h1>
	
	<hr>

		<div class="space"></div>
		<div class="row" >	
			<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 <?php if($this->is_ad_enabled && ($this->is_ad_enabled2 || $this->is_ad_enabled3)) echo ""; else echo "col-md-offset-2 col-lg-offset-2";?>">
				
				<div class="table-responsive">
					<table class="table table-bordered table-striped table-hover data-table">
						<tr>
							<th class='text-center'><?php echo $this->lang->line("sl"); ?></th>
							<th><?php echo $this->lang->line("website"); ?></th>
							<th class='text-center'><?php echo $this->lang->line("examined at"); ?></th>
							<th class='text-center'><?php echo $this->lang->line("report"); ?></th>
						</tr>
						<?php 
						$i=0;
						foreach($recent_search as $row) 
						{
							$i++;
							echo "<tr>";
								echo "<td class='text-center'>";
									echo $i;
								echo "</td>";
								echo "<td>";
									$domain_name_data=strtolower($row["domain_name"]);
									$domain_name_data=str_replace(array("www.","http://","https://"), "", $domain_name_data);
									echo $domain_name_data;
								echo "</td>";
								echo "<td class='text-center'>";
									echo $row['searched_at'];
								echo "</td>";
								echo "<td class='text-center'>";
									$details_url=site_url('health_check/report'."/".$row['id'].'/'.$domain_name_data);  
									echo "<a href='".$details_url."'> <i class='fa fa-binoculars'></i> ".$this->lang->line('report')."</a>";     
								echo "</td>";
							echo "</tr>";
						}
						?>
					</table>
				</div>
			</div>


			<?php if($this->is_ad_enabled && ($this->is_ad_enabled2 || $this->is_ad_enabled3)) : ?>
			<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 add-300-250">
				<h3>&nbsp;</h3><br/>
				<?php if($this->is_ad_enabled2) //echo $this->ad_content2; ?>
				<?php if($this->is_ad_enabled3) //echo "<br/><br/><br/>".$this->ad_content3; ?>
			</div>
			<?php endif; ?>

		</div>

		<div class="space"></div>
		<div class="space"></div>	

		<div class="row">

			<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 <?php if($this->is_ad_enabled && $this->is_ad_enabled4) echo ""; else echo "col-md-offset-2 col-lg-offset-2";?>">
				<h1 class="title text-left"><?php echo $this->lang->line("comparitive health report"); ?></h1><br/>
				<div class="table-responsive">
					<table class="table table-bordered table-striped table-hover data-table">
					<tr>
						<th class='text-center'><?php echo $this->lang->line("sl"); ?></th>
						<th><?php echo $this->lang->line("website compared"); ?></th>
						<th class='text-center'><?php echo $this->lang->line("compared at"); ?></th>
						<th class='text-center'><?php echo $this->lang->line("report"); ?></th>
					</tr>
					<?php 
					$i=0;
					foreach($recent_comparison as $row) 
					{
						$i++;
						echo "<tr>";
							echo "<td class='text-center'>";
								echo $i;
							echo "</td>";
							echo "<td>";
								$domain_name_data=strtolower($row["base_domain"]." vs ".$row["competutor_domain"]);
								$domain_name_data=str_replace(array("www.","http://","https://"), "", $domain_name_data);
								echo $domain_name_data;
							echo "</td>";
							echo "<td class='text-center'>";
								echo $row['searched_at'];
							echo "</td>";
							echo "<td class='text-center'>";
								$details_url=site_url('health_check/comparison_report'."/".$row['id']);  
								echo "<a href='".$details_url."'> <i class='fa fa-binoculars'></i> ".$this->lang->line('report')."</a>";     
							echo "</td>";
						echo "</tr>";
					}
					?>
				</table>
				</div>
			</div>


			<?php if($this->is_ad_enabled && $this->is_ad_enabled4) : ?>
			<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 add-300-600">
				<h3>&nbsp;</h3><br/>
				<?php if($this->is_ad_enabled4) //echo $this->ad_content4; ?>
			</div>
			<?php endif; ?>

		</div>
	</div>
	<!-- section end -->

	
	<?php //echo $this->load->view("site/contact.php"); ?>

	<script type="text/javascript">

		
		var interval="";

		function get_bulk_progress()
		{
			var base_url="<?php echo base_url(); ?>";			
			$.ajax({
				url:base_url+'home/progress_count',
				type:'POST',
				dataType:'json',
				success:function(response){
					var search_complete=response.search_complete;
					var search_total=response.search_total;
					$("#domain_progress_msg_text").html(search_complete +" / "+ search_total +" <?php echo $this->lang->line('step completed') ?>");
					var width=(search_complete*100)/search_total;
					width=Math.round(width);					
					var width_per=width+"%";
					if(width<3)
					{
						$("#domain_progress_bar_con div").css("width","3%");
						$("#domain_progress_bar_con div").attr("aria-valuenow","3");
						$("#domain_progress_bar_con div span").html("1%");
					}
					else
					{
						$("#domain_progress_bar_con div").css("width",width_per);
						$("#domain_progress_bar_con div").attr("aria-valuenow",width);
						$("#domain_progress_bar_con div span").html(width_per);
					}
					if(width==100) 
					{											
						$("#domain_progress_msg_text").html("<?php echo $this->lang->line('you will be redirected to report page within few seconds')?>");
						clearInterval(interval);						
					}				
					
				}
			});
			
		}
	

		$j(document).ready(function() {

			var base_url="<?php echo base_url(); ?>";
			var compare="<?php echo $compare;?>";
			var base_site="<?php echo $base_site;?>";

			$("#search").on('click',function(){

				var website = $("#page_search").val();

				if(website == '')
				{
					alert("<?php echo $this->lang->line('you have not enter any website'); ?>");
					return false;
				}
				
				$("#domain_progress_bar_con div").css("width","3%");
				$("#domain_progress_bar_con div").attr("aria-valuenow","3");
				$("#domain_progress_bar_con div span").html("1%");	
				$("#domain_progress_bar_con").show();
				$("#domain_progress_msg_text").html('<?php echo $this->lang->line("please wait"); ?>');

				interval=setInterval(get_bulk_progress, 5000);

				$("#demo_search").modal({backdrop: 'static', keyboard: false});			

				// $("#success_msg").html('<img class="center-block" src="'+base_url+'assets/pre-loader/custom.gif" alt="Processing..."><br/>');
							
				$("#domain_success_msg").html('<img class="center-block" src="'+base_url+'assets/pre-loader/custom_lg.gif" alt="Processing..."><br/>');
				$.ajax({
					url:base_url+'home/search_action',
					type:'POST',
					data:{website:website,base_site:base_site,compare:compare},
					dataType:'json',
					success:function(response){			
						if(response.status=="0")
						{
							$("#domain_progress_bar_con").hide();
							alert("<?php echo $this->lang->line("something went wrong, please try again."); ?>");
						}

						else
						{
							$("#domain_progress_msg_text").html("<?php echo $this->lang->line('you will be redirected to report page within few seconds')?>");
							clearInterval(interval);
							$("#domain_progress_bar_con div").css("width","100%");
							$("#domain_progress_bar_con div").attr("aria-valuenow","100");
							$("#domain_progress_bar_con div span").html("100%");
							$("#domain_success_msg").html('<center><h2 class="violet"><?php echo $this->lang->line("completed") ?></h2></center>');
							
							var delay=5000;
							setTimeout(function() {
								window.location.href=response.details_url;
							}, delay);

						}
					}

				});


			});

		});
	</script>

	<div class="modal fade" id="demo_search">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"> <i class="fa fa-close fa-2x"></i> </button>
					<h3 class="modal-title"> <i class="fa fa-coffee"></i> <?php echo $this->config->item('product_name'); ?></h3>
				</div>
				<div class="modal-body clearfix">

					<div class="col-xs-12 text-center" id="domain_success_msg"></div> 
					
					<div class="col-xs-12 text-center" id="progress_msg">
						<b><span id="domain_progress_msg_text"></span></b><br/>
						<div class="progress" style="display: none;" id="domain_progress_bar_con"> 
							<div style="width:3%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="3" role="progressbar" class="progress-bar progress-bar-info progress-bar-striped"><b><span>1%</span></b></div> 
						</div>
					</div>				

				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		var ip_link="<?php echo base_url(); ?>"+"js_controller/get_ip";
		var server_link="<?php echo base_url(); ?>"+"js_controller/server_info";
		var scroll_server_link="<?php echo base_url(); ?>"+"js_controller/scroll_info";
		var click_server_link="<?php echo base_url(); ?>"+"js_controller/click_info";
		var browser_js_link="<?php echo base_url(); ?>"+"js/analytics_js/useragent.js";


		function document_height(){
			var body = document.body,
		    html = document.documentElement;
			var height = Math.max( body.scrollHeight, body.offsetHeight, 
		                       html.clientHeight, html.scrollHeight, html.offsetHeight );
			return height;
		}

		function getScrollTop(){
		    if(typeof pageYOffset!= 'undefined'){
		        //most browsers except IE before #9
		        return pageYOffset;
		    }
		    else{
		        var B= document.body; //IE 'quirks'
		        var D= document.documentElement; //IE with doctype
		        D= (D.clientHeight)? D: B;
		        return D.scrollTop;
		    }
		}


		function ajax_dolphin(link,data){
			  xhr = new XMLHttpRequest();
			  xhr.open('POST',link);
			  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			  xhr.send(data);
		}




		function get_browser_info(){
				    var ua=navigator.userAgent,tem,M=ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || []; 
				    if(/trident/i.test(M[1])){
				        tem=/\brv[ :]+(\d+)/g.exec(ua) || []; 
				        return {name:'IE',version:(tem[1]||'')};
				        }   
				    if(M[1]==='Chrome'){
				        tem=ua.match(/\bOPR\/(\d+)/)
				        if(tem!=null)   {return {name:'Opera', version:tem[1]};}
				        }   
				    M=M[2]? [M[1], M[2]]: [navigator.appName, navigator.appVersion, '-?'];
				    if((tem=ua.match(/version\/(\d+)/i))!=null) {M.splice(1,1,tem[1]);}
				    return {
				      name: M[0],
				      version: M[1]
				    };
		 }
		 
		 /*** Creating Cookie function ***/
		 function createCookie(name,value,days) {
		    if (days) {
		        var date = new Date();
		        date.setTime(date.getTime()+(days*24*60*60*1000));
		        var expires = "; expires="+date.toGMTString();
		    }
		    else var expires = "";
		    document.cookie = name+"="+value+expires+"; path=/";
		}

		/***Read Cookie function**/
		function readCookie(name) {
		    var nameEQ = name + "=";
		    var ca = document.cookie.split(';');
		    for(var i=0;i < ca.length;i++) {
		        var c = ca[i];
		        while (c.charAt(0)==' ') c = c.substring(1,c.length);
		        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		    }
		    return null;
		}

		/*** Delete Cookie Function ***/
		function eraseCookie(name) {
		    createCookie(name,"",-1);
		}


		function time_difference(from_time,to_time){
			var differenceTravel = to_time.getTime() - from_time.getTime();
			var seconds = Math.floor((differenceTravel) / (1000));
			return seconds;
			
		}
		 
		function ajax_call(){
				
				/**Load browser plugin***/
				var y = document.createElement('script');
				y.src = browser_js_link;
				document.getElementsByTagName("head")[0].appendChild(y);
				
				/**after browser plugin loaded**/
				y.onload=function(){
				
						var ip;
						var device;
						var mobile_desktop;
						
						device=jscd.os;
						if(jscd.mobile){
							mobile_desktop="Mobile";
						}
						else{
							mobile_desktop="Desktop";
						}
						
						var browser_info=get_browser_info();
						var browser_name=browser_info.name;
						var browser_version=browser_info.version;
						
						var browser_rawdata = JSON.stringify(navigator.userAgent);
						// var website_code = document.getElementById("domain").getAttribute("data-name");
						// var website_code = document.querySelector("script#domain").getAttribute("data-name");
						var website_code = "007";
						
						/**Get referer Address**/
						var referrer = document.referrer;
						
						/** Get Current url **/
						var current_url = window.location.href;
						
						/*** Get cookie value , if it is already set or not **/
						
						var cookie_value=readCookie("xerone_dolphin");
						var extra_value= new Date().getTime();
						
						/**if new visitor set the cookie value a random number***/
						if(cookie_value=='' || cookie_value==null || cookie_value === undefined){
							var is_new=1;
							var random_cookie_value=Math.floor(Math.random()*999999);
							random_cookie_value=random_cookie_value+extra_value.toString();
							createCookie("xerone_dolphin",random_cookie_value,1);
							cookie_value=random_cookie_value;
						}
						
						else{
							createCookie("xerone_dolphin",cookie_value,1);
							var is_new=0;
						}
						
						
						var session_value=sessionStorage.xerone_dolphin_session;
						
						if(session_value=='' || session_value==null || session_value === undefined){
							var random_session_value=Math.floor(Math.random()*999999);
							random_session_value=random_session_value+extra_value.toString();
							sessionStorage.xerone_dolphin_session=random_session_value;
							session_value=random_session_value;
						}
						
						/**if it is a new session then create session***/
						
						
						var data="website_code="+website_code+"&browser_name="+browser_name+"&browser_version="+browser_version+"&device="+device+"&mobile_desktop="+mobile_desktop+"&referrer="+referrer+"&current_url="+current_url+"&cookie_value="+cookie_value+"&is_new="+is_new+"&session_value="+session_value+"&browser_rawdata="+browser_rawdata;
						
							ajax_dolphin(server_link,data);
											
					
					/** Scrolling detection, if it is scrolling more than 50%  and after 5 seceond of last scroll then enter the time ****/
					
						var last_scroll_time;
						var scroll_track=0;
						var time_dif=0;
						
						window.onscroll	=	function(){
							
							 var  wintop = getScrollTop();
							 var  docheight = document_height();
							 var  winheight = window.innerHeight;
							 
							 var  scrolltrigger = 0.50;
							 
							 if  ((wintop/(docheight-winheight)) > scrolltrigger) {
							 
							 	scroll_track++;
								var to_time=new Date();
								
								if(scroll_track>1){
									time_dif=time_difference(last_scroll_time,to_time);
								}
								
								
								if(scroll_track==1 || time_dif>5){
									last_scroll_time=new Date();
									
									var data="website_code="+website_code+"&current_url="+current_url+"&cookie_value="+cookie_value+"&session_value="+session_value;
									ajax_dolphin(scroll_server_link,data);
									
									
								}
					   	 	}
					};		
					
					
					/*** track each engagement record. Enagagment is calculated by click function****/
					
						
						var last_click_time;
						var click_track=0;
						var click_time_dif=0;
						
						document.onclick	=  function(){
								click_track++;
								var to_time=new Date();
								
								if(click_track>1){
									click_time_dif=time_difference(last_click_time,to_time);
								}
								
								if(click_track==1 || click_time_dif>5){
									last_click_time=new Date();
									
									var data="website_code="+website_code+"&current_url="+current_url+"&cookie_value="+cookie_value+"&session_value="+session_value;
									ajax_dolphin(click_server_link,data);
									
								}	
						};	
				}
			}

		function init(){
			ajax_call();
		}

		init();
	</script>




