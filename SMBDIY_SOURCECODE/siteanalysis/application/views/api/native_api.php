<?php $this->load->view('admin/theme/message'); ?>
<section class="content-header">
   <section class="content">
	     	<?php 
			$text="Generate Your ".$this->config->item("product_short_name")." API Key";
			$get_key_text="Get Your ".$this->config->item("product_short_name")." API Key";
			if(isset($api_key) && $api_key!="") 
			{
				$text="Re-generate Your ".$this->config->item("product_short_name")." API Key";
				$get_key_text="Your ".$this->config->item("product_short_name")." API Key";
	   		} 
	   		?>
		    	
		       		<!-- form start -->
		    <form class="form-horizontal" enctype="multipart/form-data" action="<?php echo site_url().'native_api/get_api_action';?>" method="GET">
		        <div class="box-body" style="padding-top:0;">
		           	<div class="form-group">
		           		<div class="small-box bg-blue">
							<div class="inner">
								<h4><?php echo $get_key_text; ?></h4>
								<p>									
		   							<h2><?php echo $api_key; ?></h2>
								</p>
								<input name="button" type="submit" class="btn btn-default btn-lg btn" value="<?php echo $text; ?>"/>
							</div>
							<div class="icon">
								<i class="fa fa-key"></i>
							</div>
						</div>
		            </div>	           
	         		               
		           </div> <!-- /.box-body -->      
		    </form> 
		<?php $call_sync_contact_url=site_url("native_api/sync_contact"); ?>	
		

		<div id = 'get_content_overview_data'>
		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> Get Content Overview Data (Your website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/get_content_overview_data?api_key=YOUR_API_KEY&domain_code=Domain_Code_Get_From_Visitor_Analysis_Menu
			<hr/>
			<?php $example_url=site_url()."native_api/get_content_overview_data?api_key=".$api_key."&domain_code=36180644";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			<h4>Example Response (JSON):</h4>
			{""total_view_for_this_domain":0,"content_overview_data":[]"}
		</div>
		</div>
		<!-- seperator****************************************************** -->

		<div id = 'get_overview_data'>
		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> Get Overview Data (Your website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/get_overview_data?api_key=YOUR_API_KEY&domain_code=Domain_Code_Get_From_Visitor_Analysis_Menu
			<hr/>
			<?php $example_url=site_url()."native_api/get_overview_data?api_key=".$api_key."&domain_code=36180644";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			<h4>Example Response (JSON):</h4>
			{"total_page_view":"0","total_unique_visitro":"0","average_visit":"0","average_stay_time":"0:0:0","bounce_rate":0}
		</div>
		</div>
		<!-- seperator****************************************************** -->

		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> DMOZ Check (any website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/dmoz_check?api_key=YOUR_API_KEY&domain=ANY_WEBSITE_DOMAIN
			<hr/>
			<?php $example_url=site_url()."native_api/dmoz_check?api_key=".$api_key."&domain=http://facebook.com";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			<h4>Example Response (JSON):</h4>
			{"status":"1","details":"Success","listed_or_not":"yes"}
		</div>

		<div id = 'facebook_check'>
		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> Facebook Check (any website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/dmoz_check?api_key=YOUR_API_KEY&domain=ANY_WEBSITE_DOMAIN
			<hr/>
			<?php $example_url=site_url()."native_api/facebook_ckeck?api_key=".$api_key."&domain=http://www.xeroneit.net";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			<h4>Example Response (JSON):</h4>
			{"status":"1","details":"Success","total_share":57,"total_like":840,"total_comment":22}
		</div>
		</div>
		<!-- seperator****************************************************** -->

		<div id = 'google_plus_ckeck'>
		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> Google+ Check (any website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/google_plus_ckeck?api_key=YOUR_API_KEY&domain=ANY_WEBSITE_DOMAIN
			<hr/>
			<?php $example_url=site_url()."native_api/google_plus_ckeck?api_key=".$api_key."&domain=http://www.xeroneit.net";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			<h4>Example Response (JSON):</h4>
			{"status":"1","details":"Success","google_plus_count":0}
		</div>
		</div>
		<!-- seperator****************************************************** -->

		<div id = 'linkedin_check'>
		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> Linkedin Check (any website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/linkedin_check?api_key=YOUR_API_KEY&domain=ANY_WEBSITE_DOMAIN
			<hr/>
			<?php $example_url=site_url()."native_api/linkedin_check?api_key=".$api_key."&domain=http://www.xeroneit.net";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			<h4>Example Response (JSON):</h4>
			{"status":"1","details":"Success","total_share":0}
		</div>
		</div>
		<!-- seperator****************************************************** -->

		<div id = 'xing_check'>
		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> Xing Check (any website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/xing_check?api_key=YOUR_API_KEY&domain=ANY_WEBSITE_DOMAIN
			<hr/>
			<?php $example_url=site_url()."native_api/xing_check?api_key=".$api_key."&domain=http://www.xeroneit.net";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			<h4>Example Response (JSON):</h4>
			{"status":"1","details":"Success","total_share":"0 "}
		</div>
		</div>
		<!-- seperator****************************************************** -->
		<div id = 'reddit_check'>
		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> Reddit Check (any website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/reddit_check?api_key=YOUR_API_KEY&domain=ANY_WEBSITE_DOMAIN
			<hr/>
			<?php $example_url=site_url()."native_api/reddit_check?api_key=".$api_key."&domain=http://www.xeroneit.net";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			<h4>Example Response (JSON):</h4>
			{"status":"1","details":"Success","score":0,"downs":0,"ups":0}
		</div>
		</div>
		<!-- seperator****************************************************** -->

		<div id = 'pinterest_check'>
		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> Pinterest Check (any website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/pinterest_check?api_key=YOUR_API_KEY&domain=ANY_WEBSITE_DOMAIN
			<hr/>
			<?php $example_url=site_url()."native_api/pinterest_check?api_key=".$api_key."&domain=http://www.xeroneit.net";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			<h4>Example Response (JSON):</h4>
			{"status":"1","details":"Success","pinterest_pin":0}
		</div>
		</div>
		<!-- seperator****************************************************** -->

		<div id = 'buffer_check'>
		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> Buffer Check (any website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/buffer_check?api_key=YOUR_API_KEY&domain=ANY_WEBSITE_DOMAIN
			<hr/>
			<?php $example_url=site_url()."native_api/buffer_check?api_key=".$api_key."&domain=http://www.xeroneit.net";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			<h4>Example Response (JSON):</h4>
			{"status":"1","details":"Success","buffer_share":0}
		</div>
		</div>
		<!-- seperator****************************************************** -->

		<div id = 'stumbleupon_check'>
		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> Stumbleupon Check (any website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/stumbleupon_check?api_key=YOUR_API_KEY&domain=ANY_WEBSITE_DOMAIN
			<hr/>
			<?php $example_url=site_url()."native_api/stumbleupon_check?api_key=".$api_key."&domain=http://www.xeroneit.net";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			<h4>Example Response (JSON):</h4>
			{"status":"1","details":"Success","total_view":0,"total_like":0,"total_comment":0,"total_list":0}
		</div>
		</div>
		<!-- seperator****************************************************** -->

		<div id = 'pagestatus_check'>
		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> Page Status Check (any website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/pagestatus_check?api_key=YOUR_API_KEY&domain=ANY_WEBSITE_DOMAIN
			<hr/>
			<?php $example_url=site_url()."native_api/pagestatus_check?api_key=".$api_key."&domain=http://www.xeroneit.net";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			<h4>Example Response (JSON):</h4>
			{"status":"1","details":"Success","http_code":200,"total_time":12.293,"namelookup_time":0.124,"connect_time":0.14,"speed_download":6102}
		</div>
		</div>
		<!-- seperator****************************************************** -->

		<!-- <div id = 'whois_check'>
		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> Whois Check (any website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/whois_check?api_key=YOUR_API_KEY&domain=ANY_WEBSITE_DOMAIN
			<hr/>
			<?php $example_url=site_url()."native_api/whois_check?api_key=".$api_key."&domain=http://www.xeroneit.net";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			
		</div>
		</div>
		<!-- seperator****************************************************** --> 

		<div id = 'alexa_check'>
		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> Alexa Check (any website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/alexa_check?api_key=YOUR_API_KEY&domain=ANY_WEBSITE_DOMAIN
			<hr/>
			<?php $example_url=site_url()."native_api/alexa_check?api_key=".$api_key."&domain=http://www.xeroneit.net";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			<h4>Example Response (JSON):</h4>
			{"reach_rank":"515095","country":"Egypt","country_rank":"16776","traffic_rank":"429248"}
		</div>
		</div>
		<!-- seperator****************************************************** -->

		<!-- <div id = 'moz_check'>
		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> Moz Check (any website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/moz_check?api_key=YOUR_API_KEY&domain=ANY_WEBSITE_DOMAIN
			<hr/>
			<?php $example_url=site_url()."native_api/moz_check?api_key=".$api_key."&domain=http://www.xeroneit.net";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			<h4>Example Response (JSON):</h4>
			{"reach_rank":"515095","country":"Egypt","country_rank":"16776","traffic_rank":"429248"}
		</div>
		</div>
		<! seperator****************************************************** --> 

		<div id = 'similar_web_check'>
		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> SimilarWeb Check (any website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/similar_web_check?api_key=YOUR_API_KEY&domain=ANY_WEBSITE_DOMAIN
			<hr/>
			<?php $example_url=site_url()."native_api/similar_web_check?api_key=".$api_key."&domain=http://www.facebook.com";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			
		</div>
		</div>
		<!-- seperator****************************************************** -->

		<!-- <div id = 'google_page_rank_check'>
		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> Google Page Rank Check (any website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/google_page_rank_check?api_key=YOUR_API_KEY&domain=ANY_WEBSITE_DOMAIN
			<hr/>
			<?php $example_url=site_url()."native_api/google_page_rank_check?api_key=".$api_key."&domain=http://www.facebook.com";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			<h4>Example Response :</h4>
			{9}
		</div>
		</div> -->
		<!-- seperator****************************************************** -->

		<!-- <div id = 'google_index_check'>
		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> Google Index Check (any website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/google_index_check?api_key=YOUR_API_KEY&domain=ANY_WEBSITE_DOMAIN
			<hr/>
			<?php $example_url=site_url()."native_api/google_index_check?api_key=".$api_key."&domain=http://www.webasroy.com";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			<h4>Example Response :</h4>
			{16}
		</div>
		</div>
		<!-- seperator****************************************************** --> 

		<div id = 'bing_index_check'>
		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> Bing Index Check (any website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/bing_index_check?api_key=YOUR_API_KEY&domain=ANY_WEBSITE_DOMAIN
			<hr/>
			<?php $example_url=site_url()."native_api/bing_index_check?api_key=".$api_key."&domain=http://www.xeroneit.net";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			<h4>Example Response :</h4>
			{9}
		</div>
		</div>
		<!-- seperator****************************************************** -->

		<div id = 'yahoo_index_check'>
		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> Yahoo Index Check (any website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/yahoo_index_check?api_key=YOUR_API_KEY&domain=ANY_WEBSITE_DOMAIN
			<hr/>
			<?php $example_url=site_url()."native_api/yahoo_index_check?api_key=".$api_key."&domain=http://www.xeroneit.net";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			<h4>Example Response :</h4>
			{9}
		</div>
		</div>
		<!-- seperator****************************************************** -->

		<div id = 'link_analysis_check'>
		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> Link Analysis Check (any website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/link_analysis_check?api_key=YOUR_API_KEY&domain=ANY_WEBSITE_DOMAIN
			<hr/>
			<?php $example_url=site_url()."native_api/link_analysis_check?api_key=".$api_key."&domain=http://www.xeroneit.net";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			<!-- <h4>Example Response (JSON):</h4>
			{"reach_rank":"515095","country":"Egypt","country_rank":"16776","traffic_rank":"429248"} -->
		</div>
		</div>
		<!-- seperator****************************************************** -->

		<div id = 'backlink_check'>
		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> Backlink Check (any website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/backlink_check?api_key=YOUR_API_KEY&domain=ANY_WEBSITE_DOMAIN
			<hr/>
			<?php $example_url=site_url()."native_api/backlink_check?api_key=".$api_key."&domain=http://www.xeroneit.net";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			<h4>Example Response :</h4>
			{0}
		</div>
		</div>
		<!-- seperator****************************************************** -->

		<div id = 'google_malware_check'>
		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> Google Safe Browser Check (any website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/google_malware_check?api_key=YOUR_API_KEY&domain=ANY_WEBSITE_DOMAIN
			<hr/>
			<?php $example_url=site_url()."native_api/google_malware_check?api_key=".$api_key."&domain=http://www.facebook.com";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			<h4>Example Response :</h4>
			{"safe"}
		</div>
		</div>
		<!-- seperator****************************************************** -->

		<div id = 'macafee_malware_check'>
		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> McAfee Malware Check (any website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/macafee_malware_check?api_key=YOUR_API_KEY&domain=ANY_WEBSITE_DOMAIN
			<hr/>
			<?php $example_url=site_url()."native_api/macafee_malware_check?api_key=".$api_key."&domain=http://www.facebook.com";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			<h4>Example Response :</h4>
			{"safe"}
		</div>
		</div>
		<!-- seperator****************************************************** -->

		<div id = 'avg_malware_check'>
		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> AVG Malware Check (any website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/avg_malware_check?api_key=YOUR_API_KEY&domain=ANY_WEBSITE_DOMAIN
			<hr/>
			<?php $example_url=site_url()."native_api/avg_malware_check?api_key=".$api_key."&domain=http://www.facebook.com";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			<h4>Example Response :</h4>
			{"safe"}
		</div>
		</div>
		<!-- seperator****************************************************** -->

		<div id = 'norton_malware_check'>
		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> Norton Malware Check (any website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/norton_malware_check?api_key=YOUR_API_KEY&domain=ANY_WEBSITE_DOMAIN
			<hr/>
			<?php $example_url=site_url()."native_api/norton_malware_check?api_key=".$api_key."&domain=http://www.facebook.com";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			<h4>Example Response :</h4>
			{"safe"}
		</div>
		</div>
		<!-- seperator****************************************************** -->

		<div id = 'domain_ip_check'>
		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> Domain IP Check (any website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/domain_ip_check?api_key=YOUR_API_KEY&domain=ANY_WEBSITE_DOMAIN
			<hr/>
			<?php $example_url=site_url()."native_api/domain_ip_check?api_key=".$api_key."&domain=http://www.xeroneit.net";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			<h4>Example Response (JSON):</h4>
			{"isp":"GoDaddy.com, LLC","ip":"166.62.28.90","city":"Scottsdale","region":"Arizona","country":" United States","time_zone":"America\/Phoenix","longitude":"-111.890600","latitude":"33.611900"}
		</div>
		</div>
		<!-- seperator****************************************************** -->

		<div id = 'sites_in_same_ip_check'>
		<h4 style="margin:0">
			<div class="alert alert-info" style="margin-bottom:0;">
				<i class="fa fa-plug"></i> Sites in Same IP Check (any website)
			</div>
		</h4>
		<div class="well" style="background:#F9F2F4;margin-top:0;border-radius:0;;">
			<h4>API HTTP URL: </h4> <?php echo site_url();?>native_api/sites_in_same_ip_check?api_key=YOUR_API_KEY&ip=ANY_IP_ADDRESS
			<hr/>
			<?php $example_url=site_url()."native_api/sites_in_same_ip_check?api_key=".$api_key."&ip=104.244.42.1";?>
			<h4>Example API HTTP URL: </h4> 		
			<a target="_BLANK" href="<?php echo $example_url;?>"><?php echo $example_url;?></a>
			<h4>Example Response (JSON):</h4>
			{"twitter.com"}
		</div>
		</div>
		<!-- seperator****************************************************** -->

		

   </section>
</section>



