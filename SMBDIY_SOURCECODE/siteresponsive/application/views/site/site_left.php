<?php if($load_css_js!=1) {?>
<div class="row">
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
<?php } ?>


<?php 

	$mobile_ready_data=json_decode($site_info[0]['mobile_ready_data'],true);
	$pass="Unknown";
	if(isset($mobile_ready_data["ruleGroups"]["USABILITY"]["pass"]))
	$pass=$mobile_ready_data["ruleGroups"]["USABILITY"]["pass"];	
	
	$warning_count=$site_info[0]["warning_count"];
	if($pass!="1") $warning_count++;
	
	$warning_class="success";
	if($warning_count>0) $warning_class="warning";
 ?>
<?php echo "<div style='font-size:22px !important;' class='text-center'><a href='".$site_info[0]["domain_name"]."' target='_BLANK'><b>".$site_info[0]["domain_name"]."</b></a></div>"; ?>
<?php echo "<div style='font-size:20px !important;' class='text-center alert alert-".$warning_class."'><b>".$this->config->item('product_short_name')."</b> found <b>".$warning_count."</b> major issues</div>"; ?>
<div class="row">
<div class="col-xs-12">				
	<!-- page title start-->
	<?php 
	$recommendation_word="Knowledge Base";
	$value=$site_info[0]["title"];
	$check=$this->site_check->title_check($value); 
	$item="Page Title";
	$long_recommendation=$this->config->item('page_title_recommendation');
	if(strlen($value)==0) //error
	{
		$class="red";
		$status="remove";
		$short_recommendation="Your site do not have any title.";
	}
	else if($check=="1") // warning
	{
		$class="yellow";
		$status="exclamation-circle";
		$short_recommendation="Your page title exceeds 60 characters. It's not good.";
	}
	else //ok
	{
		$class="green";
		$status="check";
		$short_recommendation="Your page title does not exceed 60 characters. It's fine.";
	}
	?>
	<div class="box box-<?php echo $class;?> normal">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus">	
			<i class='fa fa-<?php echo $status;?>'></i> <b><?php echo $item; ?> :</b> <?php echo $value; ?>				
			<br/><br/><br/>
			<?php echo $short_recommendation; ?>
			<br/><br/>
			<a  class="recommendation_link" title="<?php echo $item; ?> : <?php echo $recommendation_word; ?>"> <i class="fa fa-book"></i> <b><?php echo $recommendation_word; ?></b></a>
			<div class="recommendation well"><?php echo $long_recommendation; ?></div>

		</div>
	</div> 
	<!--  page title end-->
</div>
</div>





<div class="row">
<div class="col-xs-12">				
	<!-- meta description start-->
	<?php 
	$value=$site_info[0]["description"];
	$check=$this->site_check->description_check($value); 
	$item="Meta Description";
	$long_recommendation=$this->config->item('description_recommendation');
	if(strlen($value)==0) // error
	{
		$class="red";
		$status="remove";
		$short_recommendation="Your site do not have any meta description.";
	}
	else if($check=="1") //warning
	{
		$class="yellow";
		$status="exclamation-circle";
		$short_recommendation="Your page title exceeds 150 characters. It's not good.";
	}
	else // ok
	{
		$class="green";
		$status="check";
		$short_recommendation="Your page title does not exceed 150 characters. It's fine.";
	}
	?>
	<div class="box box-<?php echo $class;?> large">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus">	
			<i class='fa fa-<?php echo $status;?>'></i> <b><?php echo $item; ?> :</b> <?php echo $value; ?>				
			<br/><br/><br/>
			<?php echo $short_recommendation; ?>
			<br/><br/>
			<a  class="recommendation_link" title="<?php echo $item; ?> : <?php echo $recommendation_word; ?>"> <i class="fa fa-book"></i> <b><?php echo $recommendation_word; ?></b></a>
			<div class="recommendation well"><?php echo $long_recommendation; ?></div>

		</div>
	</div> 
	<!--  meta description end-->
</div>
</div>





<div class="row">
<div class="col-xs-12">				
	<!-- meta keyword start-->
	<?php 
	$value=$site_info[0]["meta_keyword"];
	$check=empty($value) ? 1 : 0;
	$item="Meta Keyword";
	$long_recommendation=$this->config->item('meta_keyword_recommendation');
	if($check=="1") //error
	{
		$class="red";
		$status="remove";
		$short_recommendation="<br/><br/><br/>Your site do not have any meta keyword.";
	}
	else //ok
	{
		$class="green";
		$status="check";
		$short_recommendation="";
	}
	?>
	<div class="box box-<?php echo $class;?> medium">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus">	
			<i class='fa fa-<?php echo $status;?>'></i> <b><?php echo $item; ?> :</b> <?php echo $value; ?>			
			
			<?php echo $short_recommendation; ?>
			<br/><br/>
			<a  class="recommendation_link" title="<?php echo $item; ?> : <?php echo $recommendation_word; ?>"> <i class="fa fa-book"></i> <b><?php echo $recommendation_word; ?></b></a>
			<div class="recommendation well"><?php echo $long_recommendation; ?></div>

		</div>
	</div> 
	<!--  meta keyword end-->
</div>
</div>






<div class="row">
<?php 
$one_phrase=json_decode($site_info[0]["keyword_one_phrase"],true); 
$two_phrase=json_decode($site_info[0]["keyword_two_phrase"],true); 
$three_phrase=json_decode($site_info[0]["keyword_three_phrase"],true); 
$four_phrase=json_decode($site_info[0]["keyword_four_phrase"],true); 
$total_words=empty($site_info[0]["total_words"]) ? 0 : $site_info[0]["total_words"];
include("application/views/site/array_spam_keyword.php");

$class="blue-dark";
$status="info-circle";

?>
<div class="col-xs-12">	
	<div class="box box-<?php echo $class;?> small">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> Single Keywords</h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus">	
			<div class="table-resposive table-responsive-vertical">
				<table class="table table-bordered table-bordered table-condensed table-striped">
					<tr>
						<th>Keyword</th>
						<th>Occurrence</th>
						<th>Density</th>
						<th>Possible Spam</th>
					</tr>
					<?php foreach ($one_phrase as $key => $value) : ?>
						<tr>
							<td><?php echo $key; ?></td>
							<td><?php echo $value; ?></td>
							<td><?php $occurence = ($value/$total_words)*100; echo round($occurence, 3)." %"; ?></td>
							<td><?php 
									if(in_array(strtolower($key), $array_spam_keyword)) echo "Yes";
									else echo 'No'; 
								?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>
		</div>
	</div> 
</div>

<div class="col-xs-12">	
	<div class="box box-<?php echo $class;?> small">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> Two Word Keywords</h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus">	
			<div class="table-resposive table-responsive-vertical">
				<table class="table table-bordered table-condensed table-striped">
					<tr>
						<th>Keyword</th>
						<th>Occurrence</th>
						<th>Density</th>
						<th>Possible Spam</th>
					</tr>
					<?php foreach ($two_phrase as $key => $value) : ?>
						<tr>
							<td><?php echo $key; ?></td>
							<td><?php echo $value; ?></td>
							<td><?php $occurence = $value/$total_words*100; echo round($occurence, 3)." %"; ?></td>
							<td><?php 
									if(in_array(strtolower($key), $array_spam_keyword)) echo "Yes";
									else echo 'No'; 
								?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>
		</div>
	</div> 
</div>

<div class="col-xs-12">	
	<div class="box box-<?php echo $class;?> small">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> Three Word Keywords</h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus">	
			<div class="table-resposive table-responsive-vertical">
				<table class="table table-bordered table-bordered table-condensed table-striped">
					<tr>
						<th>Keyword</th>
						<th>Occurrence</th>
						<th>Density</th>
						<th>Possible Spam</th>
					</tr>
					<?php foreach ($three_phrase as $key => $value) : ?>
						<tr>
							<td><?php echo $key; ?></td>
							<td><?php echo $value; ?></td>
							<td><?php $occurence = $value/$total_words*100; echo round($occurence, 3)." %"; ?></td>
							<td><?php 
									if(in_array(strtolower($key), $array_spam_keyword)) echo "Yes";
									else echo 'No'; 
								?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>
		</div> 
	</div>
</div>

<div class="col-xs-12">	
	<div class="box box-<?php echo $class;?> small">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> Four Word Keywords</h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus">	
			<div class="table-resposive table-responsive-vertical">
				<table class="table table-bordered table-condensed table-striped">
					<tr>
						<th>Keyword</th>
						<th>Occurrence</th>
						<th>Density</th>
						<th>Possible Spam</th>
					</tr>
					<?php foreach ($four_phrase as $key => $value) : ?>
						<tr>
							<td><?php echo $key; ?></td>
							<td><?php echo $value; ?></td>
							<td><?php $occurence = $value/$total_words*100; echo round($occurence, 3)." %"; ?></td>
							<td><?php 
									if(in_array(strtolower($key), $array_spam_keyword)) echo "Yes";
									else echo 'No'; 
								?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>
		</div> 
	</div>
</div>

</div> <!-- end of 1,2,3,4 keyword -->





<div class="row">
<div class="col-xs-12">				
	<!-- Key words usage start-->
	<?php 
	$value=$site_info[0]["meta_keyword"];
	$check=$this->site_check->keyword_usage_check($site_info[0]["meta_keyword"],array_keys($one_phrase),array_keys($two_phrase),array_keys($three_phrase),array_keys($four_phrase));
	$item="Keyword Usage";
	$long_recommendation=$this->config->item('keyword_usage_recommendation');
	if($check=="1") //error
	{
		$class="red";
		$status="remove";
		$short_recommendation="The most using keywords do not match with meta keywords.";
	}
	else //ok
	{
		$class="green";
		$status="check";
		$short_recommendation="The most using keywords match with meta keywords.";
	}
	?>
	<div class="box box-<?php echo $class;?>">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus">	
			<?php echo $short_recommendation; ?>
			<br/><br/>
			<a  class="recommendation_link" title="<?php echo $item; ?> : <?php echo $recommendation_word; ?>"> <i class="fa fa-book"></i> <b><?php echo $recommendation_word; ?></b></a>
			<div class="recommendation well"><?php echo $long_recommendation; ?></div>

		</div>
	</div> 
	<!--  Key words usage end-->
</div>
</div>





<div class="row">
<div class="col-xs-12">				
	<!--total words start-->
	<?php 
	$value=$site_info[0]["total_words"];
	$item="Total Words";
	$long_recommendation=$this->config->item('unique_stop_words_recommendation');
	$class="blue-dark";
	$status="info-circle";
	
	?>
	<div class="box box-<?php echo $class;?>">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus">	
			<i class='fa fa-<?php echo $status;?>'></i> <b><?php echo $item; ?> :</b> <?php echo $value; ?>				
			<br/><br/>
			<a  class="recommendation_link" title="<?php echo $item; ?> : <?php echo $recommendation_word; ?>"> <i class="fa fa-book"></i> <b><?php echo $recommendation_word; ?></b></a>
			<div class="recommendation well"><?php echo $long_recommendation; ?></div>

		</div>
	</div> 
	<!--  total words end-->
</div>
</div>



<div class="row">
<div class="col-xs-12">				
	<!-- text_to_html_ratiostart-->
	<?php 
	$check=round($site_info[0]["text_to_html_ratio"]); 
	$item="Text/HTML Ratio Test";
	$long_recommendation=$this->config->item('text_to_html_ratio_recommendation');

	if($check<20) //error
	{
		$class="red";
		$status="remove";
		$short_recommendation="Site failed text/HTML ratio test.";
	}
	else //ok
	{
		$class="green";
		$status="check";
		$short_recommendation="Site passed text/HTML ratio test.";
	}
	$short_recommendation.="<br/><br/><i class='fa fa-".$status."'></i> <b>".$item." : ".$check."%</b>";
	?>
	<div class="box box-<?php echo $class;?>">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus">						
			<?php echo $short_recommendation; ?>
			<br/><br/>
			<a  class="recommendation_link" title="<?php echo $item; ?> : <?php echo $recommendation_word; ?>"> <i class="fa fa-book"></i> <b><?php echo $recommendation_word; ?></b></a>
			<div class="recommendation well"><?php echo $long_recommendation; ?></div>

		</div>
	</div> 
	<!--  text_to_html_ratio end-->
</div>
</div>







<div class="row">
<?php 
	$h1=json_decode($site_info[0]["h1"],true); 
	$h2=json_decode($site_info[0]["h2"],true); 
	$h3=json_decode($site_info[0]["h3"],true); 
	$h4=json_decode($site_info[0]["h4"],true); 
	$h5=json_decode($site_info[0]["h5"],true); 
	$h6=json_decode($site_info[0]["h6"],true); 			
?>
<?php 
	$item="HTML Headings";
	$long_recommendation=$this->config->item('heading_recommendation');
	$class="blue-dark";
	$status="info-circle";
?>
<div class="col-xs-12">
	<div class="box box-<?php echo $class;?>">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<h3 class="highlight_header">H1(<?php echo count($h1) ?>)</h3>
				<div class="highlight_header_content">
					<ul>
					<?php foreach($h1 as $key=>$value): ?>
						<li><?php echo $value; ?></li>
					<?php endforeach; ?>
					</ul>
				</div>
			</div>					
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<h3 class="highlight_header">H2(<?php echo count($h2) ?>)</h3>
				<div class="highlight_header_content">
					<ul>
					<?php foreach($h2 as $key=>$value): ?>
						<li><?php echo $value; ?></li>
					<?php endforeach; ?>
					</ul>
				</div>
			</div>					
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<h3 class="highlight_header">H3(<?php echo count($h3) ?>)</h3>
				<div class="highlight_header_content">
					<ul>
					<?php foreach($h3 as $key=>$value): ?>
						<li><?php echo $value; ?></li>
					<?php endforeach; ?>
					</ul>
				</div>
			</div>					
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<h3 class="highlight_header">H4(<?php echo count($h4) ?>)</h3>
				<div class="highlight_header_content">
					<ul>
					<?php foreach($h4 as $key=>$value): ?>
						<li><?php echo $value; ?></li>
					<?php endforeach; ?>
					</ul>
				</div>
			</div>					
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<h3 class="highlight_header">H5(<?php echo count($h5) ?>)</h3>
				<div class="highlight_header_content">
					<ul>
					<?php foreach($h5 as $key=>$value): ?>
						<li><?php echo $value; ?></li>
					<?php endforeach; ?>
					</ul>
				</div>
			</div>					
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
				<h3 class="highlight_header">H6(<?php echo count($h6) ?>)</h3>
				<div class="highlight_header_content">
					<ul>
					<?php foreach($h6 as $key=>$value): ?>
						<li><?php echo $value; ?></li>
					<?php endforeach; ?>
					</ul>
				</div>
			</div>	
			<a  class="recommendation_link" title="<?php echo $item; ?> : <?php echo $recommendation_word; ?>"> <i class="fa fa-book"></i> <b><?php echo $recommendation_word; ?></b></a>
			<div class="recommendation well"><?php echo $long_recommendation; ?></div>				
		</div><!-- /.box-body -->
	</div><!-- /.box -->
</div>		
</div>






<div class="row">
<div class="col-xs-12">				
	<!-- robot start-->
	<?php 
	$value=$site_info[0]["robot_txt_exist"];
	$check=$value;
	$item="robot.txt";
	$long_recommendation=$this->config->item('robot_recommendation');
	if($check=="0") //warning
	{
		$class="red";
		$status="remove";
		$short_recommendation="Your site does not have robot.txt.";
	}
	else //ok
	{
		$class="green";
		$status="check";
		$short_recommendation="Your site have robot.txt";
	}
	?>
	<div class="box box-<?php echo $class;?>">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus">					
			<?php echo $short_recommendation; ?>			
			<br/><br/>						
			<a  class="recommendation_link" title="<?php echo $item; ?> : <?php echo $recommendation_word; ?>"> <i class="fa fa-book"></i> <b><?php echo $recommendation_word; ?></b></a>
			<div class="recommendation well"><?php echo $long_recommendation; ?></div>

		</div>
	</div> 
	<!--  robot end-->
</div>
</div>






<div class="row">
<div class="col-xs-12">				
	<!-- sitemap start-->
	<?php 
	$value=$site_info[0]["sitemap_exist"];
	$check=$value;
	$item="Sitemap";
	$long_recommendation=$this->config->item('sitemap_recommendation');
	if($check=="0") //warning
	{
		$class="red";
		$status="remove";
		$short_recommendation="Your site does not have sitemap";
	}
	else //ok
	{
		$class="green";
		$status="check";
		$short_recommendation="Your site have sitemap";
	}
	?>
	<div class="box box-<?php echo $class;?>">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus">
			<?php echo $short_recommendation; ?>
			<?php if($check=="1") echo "<br>Location: <a href='".$site_info[0]["sitemap_location"]."' target='_BLANK'>".$site_info[0]["sitemap_location"]."</a>"; else echo "<br/>";?>
			<br/><br/>
			<a  class="recommendation_link" title="<?php echo $item; ?> : <?php echo $recommendation_word; ?>"> <i class="fa fa-book"></i> <b><?php echo $recommendation_word; ?></b></a>
			<div class="recommendation well"><?php echo $long_recommendation; ?></div>

		</div>
	</div> 
	<!--  sitemap end-->
</div>
</div>







<div class="row">
<div class="col-xs-12">				
	<!-- sitemap start-->
	<?php 
	$item="Internal Vs. External Links";				
	$class="blue-dark";
	$status="info-circle";
	
	?>
	<div class="box box-<?php echo $class;?>">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus clearfix">

			<div class="row">
				
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
					<div class="info-box" style="border:1px solid #0073B7;border-bottom:2px solid #0073B7;">
						<span class="info-box-icon bg-blue"><i class="fa fa-question-circle"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Total Internal Links?</span>
							<span class="info-box-number"><?php echo $site_info[0]["internal_link_count"];?></span>
						</div><!-- /.info-box-content -->
					</div>
				</div>

				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
					<div class="info-box" style="border:1px solid #0073B7;border-bottom:2px solid #0073B7;">
						<span class="info-box-icon bg-blue"><i class="fa fa-question-circle"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Total External Links?</span>
							<span class="info-box-number"><?php echo $site_info[0]["external_link_count"];?></span>
						</div><!-- /.info-box-content -->
					</div>
				</div>

				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">								
					<h3 class="highlight_header">Internal Links </h3>
					<div class="highlight_header_content">
						<ul>
							<?php 
								$internal_link=json_decode($site_info[0]["internal_link"],true);											
								foreach ($internal_link as $value) 
								{
									echo "<li>".$value["link"]."</li>";
								}
							?>
						</ul>
					</div>						
				</div>

				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">								
					<h3 class="highlight_header">External Links </h3>
					<div class="highlight_header_content">
						<ul>
							<?php 
								$external_link=json_decode($site_info[0]["external_link"],true);
								foreach ($external_link as $value) 
								{
									echo "<li>".$value["link"]."</li>";
								}
							?>
						</ul>
					</div>						
				</div>
			</div>

		</div>
	</div> 
	<!--  sitemap end-->
</div>
</div>






<div class="row">
<div class="col-xs-12">				
	<!-- sitemap start-->
	<?php 
	$item="NoIndex , NoFollow, DoDollow Links";
	$long_recommendation=$this->config->item('no_do_follow_recommendation');
	
	$class="blue-dark";
	$status="info-circle";
	
	?>
	<div class="box box-<?php echo $class;?>">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus clearfix">

			<div class="row">
				<div class="col-xs-12">
					<div class="info-box" style="border:1px solid #DD4B39;border-bottom:2px solid #DD4B39;">
						<span class="info-box-icon bg-red"><i class="fa fa-remove"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Total NoIndex Links </span>
							<span class="info-box-number"><?php echo count(json_decode($site_info[0]["noindex_list"],true)); ?></span>
						</div><!-- /.info-box-content -->
					</div>
				</div>

				<div class="col-xs-12">
					<div class="info-box" style="border:1px solid #F39C12;border-bottom:2px solid #F39C12;">
						<span class="info-box-icon bg-yellow"><i class="fa fa-remove"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Total NoFollow Links </span>
							<span class="info-box-number"><?php echo $site_info[0]["nofollow_link_count"]; ?></span>
						</div><!-- /.info-box-content -->
					</div>
				</div>
				<div class="col-xs-12">
					<div class="info-box" style="border:1px solid #00A65A;border-bottom:2px solid #00A65A;">
						<span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Total DoFollow Links</span>
							<span class="info-box-number"><?php echo $site_info[0]["dofollow_link_count"]; ?></span>
						</div><!-- /.info-box-content -->
					</div>
				</div>


				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
					<div class="info-box" style="border:1px solid #0073B7;border-bottom:2px solid #0073B7;">
						<span class="info-box-icon bg-blue"><i class="fa fa-question-circle"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">NoIndex Enabled by Meta Robot?</span>
							<span class="info-box-number"><?php echo $site_info[0]["noindex_by_meta_robot"];?></span>
						</div><!-- /.info-box-content -->
					</div>
				</div>

				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
					<div class="info-box" style="border:1px solid #0073B7;border-bottom:2px solid #0073B7;">
						<span class="info-box-icon bg-blue"><i class="fa fa-question-circle"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">NoFollow Enabled by Meta Robot?</span>
							<span class="info-box-number"><?php echo $site_info[0]["nofollowed_by_meta_robot"];?></span>
						</div><!-- /.info-box-content -->
					</div>
				</div>

				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">								
					<h3 class="highlight_header">NoIndex Links </h3>
					<div class="highlight_header_content">
						<ul>
							<?php 
								$noindex_list=json_decode($site_info[0]["noindex_list"],true);
								foreach ($noindex_list as $value) 
								{
									echo "<li>".$value."</li>";
								}
							?>
						</ul>
					</div>						
				</div>

				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">								
					<h3 class="highlight_header">NoFollow Links </h3>
					<div class="highlight_header_content">
						<ul>
							<?php 
								$nofollow_links=json_decode($site_info[0]["nofollow_link_list"],true);
								foreach ($nofollow_links as $value) 
								{
									echo "<li>".$value."</li>";
								}
							?>
						</ul>
					</div>						
				</div>

			</div>
			

			<br/><br/>
			<a  class="recommendation_link" title="<?php echo $item; ?> : <?php echo $recommendation_word; ?>"> <i class="fa fa-book"></i> <b><?php echo $recommendation_word; ?></b></a>
			<div class="recommendation well"><?php echo $long_recommendation; ?></div>

		</div>
	</div> 
	<!--  sitemap end-->
</div>
</div>







<div class="row">
<div class="col-xs-12">				
	<!-- seo friendly link start-->
	<?php 
	$value=json_decode($site_info[0]["not_seo_friendly_link"],true);
	$check=count($value);
	$item="SEO Friendly Links";
	$long_recommendation=$this->config->item('seo_friendly_recommendation');
	if($check==0) //ok
	{
		$class="green";
		$status="check";
		$short_recommendation="Links of your site are SEO friendly.";
	}
	else //error
	{
		$class="red";
		$status="remove";
		$short_recommendation="Some links of your site are not SEO friendly.";
	}
	?>
	<div class="box box-<?php echo $class;?> large">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus">
			<?php 
				echo $short_recommendation; 
				if($check>0)
				{ ?>															
					<br><br>
					<h3 class="highlight_header">Not SEO Friendly Links </h3>
					<div class="highlight_header_content">
						<ul>
							<?php 
								foreach ($value as $val) 
								{
									echo "<li>".$val."</li>";
								}
							?>
						</ul>
					</div>	
				<?php
				}
			?>
			<br/><br/>
			<a  class="recommendation_link" title="<?php echo $item; ?> : <?php echo $recommendation_word; ?>"> <i class="fa fa-book"></i> <b><?php echo $recommendation_word; ?></b></a>
			<div class="recommendation well"><?php echo $long_recommendation; ?></div>

		</div>
	</div> 
	<!--  seo friendly link end-->
</div>
</div>






<div class="row">
<div class="col-xs-12">				
	<!-- favicon start-->
	<?php 
	$check=$site_info[0]["is_favicon_found"];
	$item="Favicon";
	$long_recommendation="<a target='_BLANK' href='http://blog.woorank.com/2014/07/favicon-seo/'><i class='fa fa-hand-o-right'></i>  Learn more</a>";
	if($check=="0") //error
	{
		$class="yellow";
		$status="exclamation-circle";
		$short_recommendation="Your site does not have favicon.";
	}
	else //ok
	{
		$class="green";
		$status="check";
		$short_recommendation="Your site have favicon.";
	}
	?>
	<div class="box box-<?php echo $class;?>">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus">
			<?php 
				echo $short_recommendation; 
			?>
			<br/><br/>						
			<?php echo $long_recommendation; ?>

		</div>
	</div> 
	<!--  favicon end-->
</div>
</div>







<div class="row">
<div class="col-xs-12">				
	<!-- img alt start-->
	<?php 
	$value=json_decode($site_info[0]["image_not_alt_list"],true);
	$check=$site_info[0]["image_without_alt_count"];
	$item="Image 'alt' Test";
	$long_recommendation=$this->config->item('img_alt_recommendation');
	if($check=="0") //ok
	{
		$class="green";
		$status="check";
		$short_recommendation="Your site does not have any image without alt text.";
	}
	else //warning
	{
		$class="yellow";
		$status="exclamation-circle";
		$short_recommendation="Your site have ".$check." images without alt text.";
	}
	?>
	<div class="box box-<?php echo $class;?> medium">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus">
			<?php 
				echo $short_recommendation; 
				if($check>0)
				{ ?>															
					<br><br>
					<h3 class="highlight_header">Images Without alt </h3>
					<div class="highlight_header_content">
						<ul>
							<?php 
								foreach ($value as $val) 
								{
									echo "<li>".$val."</li>";
								}
							?>
						</ul>
					</div>	
				<?php
				}
			?>
			<br/>
			<a  class="recommendation_link" title="<?php echo $item; ?> : <?php echo $recommendation_word; ?>"> <i class="fa fa-book"></i> <b><?php echo $recommendation_word; ?></b></a>
			<div class="recommendation well"><?php echo $long_recommendation; ?></div>

		</div>
	</div> 
	<!--  img alt end-->
</div>
</div>




<div class="row">
<div class="col-xs-12">				
	<!-- page title start-->
	<?php 
	$value=$site_info[0]["doctype"];
	$check=$site_info[0]["doctype_is_exist"]; 
	$item="DOC Type";
	$long_recommendation=$this->config->item('doc_type_recommendation');
	if($check=="0") //error
	{
		$class="red";
		$status="remove";
		$short_recommendation="Page do not have doc type";
	}
	else //ok
	{
		$class="green";
		$status="check";
		$short_recommendation="Page have doc type.";
	}
	?>
	<div class="box box-<?php echo $class;?>">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus">	
			<i class='fa fa-<?php echo $status;?>'></i> <b><?php echo $item; ?> :</b> <?php echo $value; ?>				
			<br/><br/><br/>
			<?php echo $short_recommendation; ?>
			<br/><br/>
			<a  class="recommendation_link" title="<?php echo $item; ?> : <?php echo $recommendation_word; ?>"> <i class="fa fa-book"></i> <b><?php echo $recommendation_word; ?></b></a>
			<div class="recommendation well"><?php echo $long_recommendation; ?></div>

		</div>
	</div> 
	<!--  page title end-->
</div>
</div>







<div class="row">
<div class="col-xs-12">				
	<!-- depreciate tag start-->
	<?php 
	$value=json_decode($site_info[0]["depreciated_html_tag"],true);
	$check=array_sum($value);
	$item="Depreciated HTML Tag";
	$long_recommendation=$this->config->item('depreciated_html_recommendation');
	if($check==0) //ok
	{
		$class="green";
		$status="check";
		$short_recommendation="Your site does not have any depreciated HTML tag.";
	}
	else //warning
	{
		$class="red";
		$status="remove";
		$short_recommendation="Your site have ".$check." depreciated HTML tags.";
	}
	?>
	<div class="box box-<?php echo $class;?> medium">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus">
			<?php 
				echo $short_recommendation; 
				if($check>0)
				{ ?>															
					<br><br>
					<h3 class="highlight_header">Depreciated HTML Tags </h3>
					<div class="highlight_header_content">
						<ul>
							<?php 
								foreach ($value as $key=>$val) 
								{
									echo "<li>".$key." : ".$val."</li>";
								}
							?>
						</ul>
					</div>	
				<?php
				}
			?>
			<br/>
			<a  class="recommendation_link" title="<?php echo $item; ?> : <?php echo $recommendation_word; ?>"> <i class="fa fa-book"></i> <b><?php echo $recommendation_word; ?></b></a>
			<div class="recommendation well"><?php echo $long_recommendation; ?></div>

		</div>
	</div> 
	<!--  depreciate tag end-->
</div>
</div>






<div class="row">
<div class="col-xs-12">				
	<!-- html page size start-->
	<?php 
	$value=round($site_info[0]["total_page_size_general"])." KB";
	$check=$value; 
	$item="HTML Page Size";
	$long_recommendation=$this->config->item('html_page_size_recommendation');
	if($check>100) // warning
	{
		$class="yellow";
		$status="exclamation-circle";
		$short_recommendation="HTML page size is > 100KB";
	}
	else // ok
	{
		$class="green";
		$status="check";
		$short_recommendation="HTML page size is <= 100KB";
	}
	?>
	<div class="box box-<?php echo $class;?>">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus">	
			<i class='fa fa-<?php echo $status;?>'></i> <b><?php echo $item; ?> :</b> <?php echo $value; ?>				
			<br/><br/><br/>
			<?php echo $short_recommendation; ?>
			<br/><br/>
			<a  class="recommendation_link" title="<?php echo $item; ?> : <?php echo $recommendation_word; ?>"> <i class="fa fa-book"></i> <b><?php echo $recommendation_word; ?></b></a>
			<div class="recommendation well"><?php echo $long_recommendation; ?></div>

		</div>
	</div> 
	<!--  html page size end-->
</div>
</div>







<div class="row">
<div class="col-xs-12">				
	<!-- html page size start-->
	<?php 

	$value=round($site_info[0]["page_size_gzip"])." KB";
	$check=$site_info[0]["is_gzip_enable"]; 
	$item="GZIP Compression";
	$item2="GZIP Compressed Size";
	$long_recommendation=$this->config->item('gzip_recommendation');
	if($check=="0") // warning
	{
		$class="red";
		$status="remove";
		$short_recommendation="GZIP compression is disabled.";
	}
	else // ok
	{
		$class="green";
		$status="check";
		$short_recommendation="GZIP compression is enabled.";
		if(round($site_info[0]["page_size_gzip"]) > 33) 
		{
			$short_recommendation.= " GZIP compressed size should be < 33KB";
			$class="red";
			$status="remove";
		}
	}
	?>
	<div class="box box-<?php echo $class;?> medium">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus">	

			<?php if($check=="1") 
			{ ?>
				<i class='fa fa-<?php echo $status;?>'></i> <b><?php echo $item2; ?> :</b> <?php echo $value; ?>				
				<br/><br/><br/>
			<?php 
			} ?>

			<?php echo $short_recommendation; ?>
			<br/><br/>
			<a  class="recommendation_link" title="<?php echo $item; ?> : <?php echo $recommendation_word; ?>"> <i class="fa fa-book"></i> <b><?php echo $recommendation_word; ?></b></a>
			<div class="recommendation well"><?php echo $long_recommendation; ?></div>

		</div>
	</div> 
	<!--  html page size end-->
</div>
</div>








<div class="row">
<div class="col-xs-12">				
	<!--  inline css start-->
	<?php 
	$value=json_decode($site_info[0]["inline_css"],true);
	$check=count($value);
	$item="Inline CSS";
	$long_recommendation=$this->config->item('inline_css_recommendation');
	if($check==0) //ok
	{
		$class="green";
		$status="check";
		$short_recommendation="Your site does not have any inline css.";
	}
	else //warning
	{
		$class="red";
		$status="remove";
		$short_recommendation="Your site have ".$check." inline css.";
	}
	?>
	<div class="box box-<?php echo $class;?> medium">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus">
			<?php 
				echo $short_recommendation; 
				if($check>0)
				{ ?>															
					<br><br>
					<h3 class="highlight_header">Inline CSS </h3>
					<div class="highlight_header_content">
						<ul>
							<?php 
								foreach ($value as $val) 
								{
									echo "<li>".$val."</li>";
								}
							?>
						</ul>
					</div>	
				<?php
				}
			?>
			<br/>
			<a  class="recommendation_link" title="<?php echo $item; ?> : <?php echo $recommendation_word; ?>"> <i class="fa fa-book"></i> <b><?php echo $recommendation_word; ?></b></a>
			<div class="recommendation well"><?php echo $long_recommendation; ?></div>

		</div>
	</div> 
	<!--   inline css end-->
</div>
</div>





<div class="row">
<div class="col-xs-12">				
	<!--  inline css start-->
	<?php 
	$value=json_decode($site_info[0]["internal_css"],true);
	$check=count($value);
	$item="Internal CSS";
	$long_recommendation=$this->config->item('internal_css_recommendation');
	if($check==0) //ok
	{
		$class="green";
		$status="check";
		$short_recommendation="Your site does not have any internal css.";
	}
	else //warning
	{
		$class="red";
		$status="remove";
		$short_recommendation="Your site have ".$check." internal css.";
	}
	?>
	<div class="box box-<?php echo $class;?>">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus">						
			<?php echo $short_recommendation; ?>
			<br/><br/>
			<a  class="recommendation_link" title="<?php echo $item; ?> : <?php echo $recommendation_word; ?>"> <i class="fa fa-book"></i> <b><?php echo $recommendation_word; ?></b></a>
			<div class="recommendation well"><?php echo $long_recommendation; ?></div>
		</div>
	</div> 
	<!--   inline css end-->
</div>
</div>





<div class="row">
<div class="col-xs-12">				
	<!-- micro data schema start-->
	<?php 
	$value=json_decode($site_info[0]["micro_data_schema_list"],true);
	$check=count($value);
	$item="Micro Data Schema Test";
	$long_recommendation=$this->config->item('micro_data_recommendation');
	if($check>0) //ok
	{
		$class="green";
		$status="check";
		$short_recommendation="Site passed micro data schema test. ".$check." results found.";
	}
	else //error
	{
		$class="red";
		$status="remove";
		$short_recommendation="Site failed micro data schema test.";
	}
	?>
	<div class="box box-<?php echo $class;?> large">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus">
			<?php 
				echo $short_recommendation; 
				if($check>0)
				{ ?>															
					<br><br>
					<h3 class="highlight_header">Micro data schema list </h3>
					<div class="highlight_header_content">
						<ul>
							<?php 
								foreach ($value as $val) 
								{
									echo "<li>".$val."</li>";
								}
							?>
						</ul>
					</div>	
				<?php
				}
			?>
			<br/>
			<a  class="recommendation_link" title="<?php echo $item; ?> : <?php echo $recommendation_word; ?>"> <i class="fa fa-book"></i> <b><?php echo $recommendation_word; ?></b></a>
			<div class="recommendation well"><?php echo $long_recommendation; ?></div>

		</div>
	</div> 
	<!--  micro data schema end-->
</div>
</div>






<div class="row">
<div class="col-xs-12">				
	<!-- ip dns start-->
	<?php 
	$item="IP & DNS Report";				
	$class="blue-dark";
	$status="info-circle";
	
	?>
	<div class="box box-<?php echo $class;?> medium">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus clearfix">

			<div class="row">

				
				<div class="col-xs-12">
					<div class="info-box" style="border:1px solid #0073B7;border-bottom:2px solid #0073B7;">
						<span class="info-box-icon bg-blue"><i class="fa fa-map-marker"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">IPv4</span>
							<span class="info-box-number"><?php echo $site_info[0]["ip"];?></span>
						</div><!-- /.info-box-content -->
					</div>
				</div>

				<div class="col-xs-12">
					<div class="info-box" style="border:1px solid #0073B7;border-bottom:2px solid #0073B7;">
						<span class="info-box-icon bg-blue"><i class="fa fa-map-marker"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">IPv6</span>
							<span class="info-box-number"><?php if($site_info[0]["is_ipv6_compatiable"]==0) echo "Not Compatiable "; else echo $site_info[0]["ipv6"];?></span>
						</div><!-- /.info-box-content -->
					</div>
				</div>

				<?php 
				$dns_report=json_decode($site_info[0]["dns_report"],true);
				
				if(count($dns_report)>0)
				{ ?>
					<div class="col-xs-12">								
						<h5>DNS Report </h5>
						<div class="highlight_header_content_large table-resposive no_padding" >
							<?php 										
								echo "<table class='table table-bordered table-striped table-hover'>";
								echo "<tr class='success'>";
									echo "<td>SL</td>";
									echo "<td>Host</td>";
									echo "<td>Class</td>";
									echo "<td>TTL</td>";
									echo "<td>Type</td>";
									echo "<td>PRI</td>";
									echo "<td>Target</td>";
									echo "<td>IP</td>";
								echo "</tr>";
								$sl=0;
								foreach ($dns_report as $value) 
								{
									$sl++;
									if(!isset($value["host"]))  $value["host"]="";
									if(!isset($value["class"])) $value["class"]="";
									if(!isset($value["ttl"]))   $value["ttl"]="";
									if(!isset($value["type"]))  $value["type"]="";
									if(!isset($value["pri"])) 	$value["pri"]="";
									if(!isset($value["target"]))$value["target"]="";
									if(!isset($value["ip"])) 	$value["ip"]="";
									if($value["type"]=="AAAA")
										$value["ip"]=$value["ipv6"];
									echo "<tr>";
										echo "<td>".$sl."</td>";
										echo "<td>".$value["host"]."</td>";
										echo "<td>".$value["class"]."</td>";
										echo "<td>".$value["ttl"]."</td>";
										echo "<td>".$value["type"]."</td>";
										echo "<td>".$value["pri"]."</td>";
										echo "<td>".$value["target"]."</td>";
										echo "<td>".$value["ip"]."</td>";
									echo "</tr>";
								}
								echo "</table>";
							?>
						</div>						
					</div>
				<?php } 
				else echo '<div class="col-xs-12"><h5 class=" alert alert-warning"> <i class="fa fa-remove"></i> No DNS report found</h5></div>';?>
			</div>
		</div>
	</div> 	<!--  ip dns end-->
</div>
</div>






<div class="row">
<div class="col-xs-12">				
	<!-- ip can start-->
	<?php 
	$check=$site_info[0]["is_ip_canonical"]; 
	$item="IP Canonicalization Test";
	$long_recommendation=$this->config->item('ip_canonicalization_recommendation');
	if($check=="0") //error
	{
		$class="red";
		$status="remove";
		$short_recommendation="Site failed IP canonicalization test.";
	}
	else //ok
	{
		$class="green";
		$status="check";
		$short_recommendation="Site passed IP canonicalization test.";
	}
	?>
	<div class="box box-<?php echo $class;?>">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus">
			<?php echo $short_recommendation; ?>
			<br/><br/>
			<a  class="recommendation_link" title="<?php echo $item; ?> : <?php echo $recommendation_word; ?>"> <i class="fa fa-book"></i> <b><?php echo $recommendation_word; ?></b></a>
			<div class="recommendation well"><?php echo $long_recommendation; ?></div>

		</div>
	</div> 
	<!--  ip can end-->
</div>
</div>




<div class="row">
<div class="col-xs-12">				
	<!-- url can start-->
	<?php 
	$check=$site_info[0]["is_url_canonicalized"]; 
	$item="URL Canonicalization Test";
	$long_recommendation=$this->config->item('url_canonicalization_recommendation');
	if($check=="0") //error
	{
		$class="red";
		$status="remove";
		$short_recommendation="Site failed URL canonicalization test.";
	}
	else //ok
	{
		$class="green";
		$status="check";
		$short_recommendation="Site passed URL canonicalization test.";
	}
	?>
	<div class="box box-<?php echo $class;?>">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus">
			<?php echo $short_recommendation; ?>
			<br/><br/>
			<a  class="recommendation_link" title="<?php echo $item; ?> : <?php echo $recommendation_word; ?>"> <i class="fa fa-book"></i> <b><?php echo $recommendation_word; ?></b></a>
			<div class="recommendation well"><?php echo $long_recommendation; ?></div>

		</div>
	</div> 
	<!--  url can end-->
</div>
</div>





<div class="row">
<div class="col-xs-12">				
	<!--  plain email start-->
	<?php 
	$value=json_decode($site_info[0]["email_list"],true);
	$check=count($value);
	$item="Plain Text Email Test";
	$long_recommendation=$this->config->item('plain_email_recommendation');
	if($check==0) //ok
	{
		$class="green";
		$status="check";
		$short_recommendation="Site passed plain text email test. No plain text email found.";
	}
	else //warning
	{
		$class="red";
		$status="remove";
		$short_recommendation="Site failed plain text email test. ".$check." plain text email found.";
	}
	?>
	<div class="box box-<?php echo $class;?>">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus">
			<?php 
				echo $short_recommendation; 
				?>															
					<br><br>
					<h3 class="highlight_header">Plain Text Email List </h3>
					<div class="highlight_header_content">
						<ul>
							<?php 
								foreach ($value as $val) 
								{
									echo "<li>".$val."</li>";
								}
							?>
						</ul>
					</div>	
				
			<br/><br/>
			<a  class="recommendation_link" title="<?php echo $item; ?> : <?php echo $recommendation_word; ?>"> <i class="fa fa-book"></i> <b><?php echo $recommendation_word; ?></b></a>
			<div class="recommendation well"><?php echo $long_recommendation; ?></div>

		</div>
	</div> 
	<!--   plain email end-->
</div>
</div>





<div class="row">
<div class="col-xs-12">				
	<!-- sitemap start-->
	<?php 
	$item="cURL Response";				
	$class="blue-dark";
	$status="info-circle";
	
	?>
	<div class="box box-<?php echo $class;?>">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus clearfix">

			<div class="row">
				<div class="col-xs-12 table-responsive table-responsive-vertical">
					<?php $curl_response=json_decode($site_info[0]["general_curl_response"],true) ?>
						<?php 										
							echo "<table class='table table-condensed table-striped table-bordered'>";
							$sl=0;
							foreach ($curl_response as $key=>$value) 
							{
								if(is_array($value)) $value=implode(",", $value);
								$sl++;
								if(($sl+1)%2==0)
								echo "<tr>";
									echo "<td><b>".str_replace("_"," ",$key)."</b></td>";
									echo "<td>".$value."</td>";
								if($sl%2==0)
								echo "</tr>";
							}
							echo "</table>";
						?>
				</div>

			</div>

		</div>
	</div> 
	<!--  sitemap end-->
</div>
</div>




<?php 
$pass="Unknown";
$score="Unknown";
if(isset($mobile_ready_data["ruleGroups"]["USABILITY"]["pass"]))
$pass=$mobile_ready_data["ruleGroups"]["USABILITY"]["pass"];
if(isset($mobile_ready_data["ruleGroups"]["USABILITY"]["score"]))
$score=$mobile_ready_data["ruleGroups"]["USABILITY"]["score"];

if($pass=="1") //ok
{
$class="green";
$status="check";
}
else //error
{
$class="red";
$status="remove";
}
$item="Mobile Friendly Check";	

?>
<div class="row">
<div class="col-xs-12">
	<div class="box box-<?php echo $class;?>">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body">	
			<div class="row">
				<div class="col-xs-12">
					<?php 							
					 
					if($pass=="1") 
					echo "<br><h3 style='margin-top:0px;'><span class='label label-success'>Mobile Friendly : Yes <br></span></h3> Score : ".$score;
					else if($pass=="Unknown")  
					echo "<br><h3 style='margin-top:0px;'><span class='label label-danger'>Mobile Friendly : Unknown <br></span></h3> Score : ".$score;
					else echo "<br><h3 style='margin-top:0px;'><span class='label label-danger'>Mobile Friendly : No <br></span></h3> Score : ".$score;

					?>
					<br><br>
					<div class=" table-responsive highlight_header_content_large">
						<table class="table table-hover table-striped">
							<tr>
								<th>Localized Rule Name</th>
								<th>Rule Impact</th>
							</tr>
							<?php if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["localizedRuleName"]) || isset($mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"]))
							{?>		
								<tr>
									<td>
										<?php 
										if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["localizedRuleName"]))
										echo $mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["localizedRuleName"];
										?>
									</td>
									<td>
										<?php 
										if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"]))
										echo $mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"];
										?>
									</td>
								</tr>
							<?php 
							} ?>

							<?php if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["UseLegibleFontSizes"]["localizedRuleName"]) || isset($mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"]))
							{?>		
								<tr>
									<td>
										<?php 
										if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["UseLegibleFontSizes"]["localizedRuleName"]))
										echo $mobile_ready_data["formattedResults"]["ruleResults"]["UseLegibleFontSizes"]["localizedRuleName"];
										?>
									</td>
									<td>
										<?php 
										if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["UseLegibleFontSizes"]["ruleImpact"]))
										echo $mobile_ready_data["formattedResults"]["ruleResults"]["UseLegibleFontSizes"]["ruleImpact"];
										?>
									</td>
								</tr>
							<?php 
							} ?>

							<?php if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["AvoidPlugins"]["localizedRuleName"]) || isset($mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"]))
							{?>		
								<tr>
									<td>
										<?php 
										if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["AvoidPlugins"]["localizedRuleName"]))
										echo $mobile_ready_data["formattedResults"]["ruleResults"]["AvoidPlugins"]["localizedRuleName"];
										?>
									</td>
									<td>
										<?php 
										if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["AvoidPlugins"]["ruleImpact"]))
										echo $mobile_ready_data["formattedResults"]["ruleResults"]["AvoidPlugins"]["ruleImpact"];
										?>
									</td>
								</tr>
							<?php 
							} ?>

							<?php if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["SizeContentToViewport"]["localizedRuleName"]) || isset($mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"]))
							{?>		
								<tr>
									<td>
										<?php 
										if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["SizeContentToViewport"]["localizedRuleName"]))
										echo $mobile_ready_data["formattedResults"]["ruleResults"]["SizeContentToViewport"]["localizedRuleName"];
										?>
									</td>
									<td>
										<?php 
										if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["SizeContentToViewport"]["ruleImpact"]))
										echo $mobile_ready_data["formattedResults"]["ruleResults"]["SizeContentToViewport"]["ruleImpact"];
										?>
									</td>
								</tr>
							<?php 
							} ?>

							<?php if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["SizeTapTargetsAppropriately"]["localizedRuleName"]) || isset($mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"]))
							{?>		
								<tr>
									<td>
										<?php 
										if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["SizeTapTargetsAppropriately"]["localizedRuleName"]))
										echo $mobile_ready_data["formattedResults"]["ruleResults"]["SizeTapTargetsAppropriately"]["localizedRuleName"];
										?>
									</td>
									<td>
										<?php 
										if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["SizeTapTargetsAppropriately"]["ruleImpact"]))
										echo $mobile_ready_data["formattedResults"]["ruleResults"]["SizeTapTargetsAppropriately"]["ruleImpact"];
										?>
									</td>
								</tr>
							<?php 
							} ?>

							<?php if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["AvoidInterstitials"]["localizedRuleName"]) || isset($mobile_ready_data["formattedResults"]["ruleResults"]["ConfigureViewport"]["ruleImpact"]))
							{?>		
								<tr>
									<td>
										<?php 
										if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["AvoidInterstitials"]["localizedRuleName"]))
										echo $mobile_ready_data["formattedResults"]["ruleResults"]["AvoidInterstitials"]["localizedRuleName"];
										?>
									</td>
									<td>
										<?php 
										if(isset($mobile_ready_data["formattedResults"]["ruleResults"]["AvoidInterstitials"]["ruleImpact"]))
										echo $mobile_ready_data["formattedResults"]["ruleResults"]["AvoidInterstitials"]["ruleImpact"];
										?>
									</td>
								</tr>
							<?php 
							} ?>

							<?php if(!isset($mobile_ready_data["formattedResults"]["ruleResults"])) 
							{?>		
								<tr>
									<td colspan="2" class="text-center">No data to show.</td>
								</tr>
							<?php 
							} ?>

						</table>
					</div>

					<div class="well">
						<b>CMS:</b> <?php if(isset($mobile_ready_data["pageStats"]["cms"])) echo $mobile_ready_data["pageStats"]["cms"];?>								
						<br><b>Locale:</b> <?php if(isset($mobile_ready_data["formattedResults"]["locale"])) echo $mobile_ready_data["formattedResults"]["locale"];?>								
						<br><b>Roboted Resources:</b> <?php if(isset($mobile_ready_data["pageStats"]["numberRobotedResources"])) echo $mobile_ready_data["pageStats"]["numberRobotedResources"];?>								
						<br><b>Transient Fetch Failure Resources:</b> <?php if(isset($mobile_ready_data["pageStats"]["numberTransientFetchFailureResources"])) echo $mobile_ready_data["pageStats"]["numberTransientFetchFailureResources"];?>								
						<br>
					</div>

				</div>
				<div class="col-xs-12" style="margin-bottom:30px !important;padding-left:12px;min-height:530px;background: url('<?php echo base_url("assets/images/mobile.png");?>') no-repeat !important;">
					<?php 
					$mobile_ready_data=json_decode($site_info[0]["mobile_ready_data"],true);
											
					if(isset($mobile_ready_data["screenshot"]["data"]))
					{
						$src=str_replace("_", "/", $mobile_ready_data["screenshot"]["data"]);
						$src=str_replace("-", "+", $src);
						echo '<img src="data:image/jpeg;base64,'.$src.'" style="max-width:225px !important;margin-top:52px;">';
					}
					?>
				</div>
			</div>				
		</div><!-- /.box-body -->
	</div><!-- /.box -->
</div>		

</div>




<div class="row">
<div class="col-xs-12">		
	<?php 
	$item="Google Page Speed Insight (Mobile)";				
	$class="blue-dark";
	$status="info-circle";				
	?>
	<div class="box box-<?php echo $class;?>">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus clearfix">
			
			<div class="row">								
				<?php 
				$item="Page Speed";				
				$class="blue-dark";
				$status="info-circle";	
				$value=$site_info[0]['speed_score_mobile'];			
				?>
				<?php if($load_css_js!=1) {?>
				<div class="col-xs-12 table-responsive">
					<div class="box box-<?php echo $class;?>">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<input id="mobile_speed" type="hidden" value="<?php echo $value; ?>" />
						</div>
					</div>	
				</div>
				<?php } else { ?>
				<div class="col-xs-12 col-md-6">
					<div style="border:1px solid #0073B7;border-bottom:2px solid #0073B7;" class="info-box">
						<span class="info-box-icon bg-blue"><i class="fa fa-bullseye"></i></span>
						<div class="info-box-content">
							<span class="info-box-text"><?php echo $item;?></span>
							<span class="info-box-number"><?php echo $value; ?></span>
						</div><!-- /.info-box-content -->
					</div>
				</div>
				<?php } ?>


				<?php 
				$item="Usability Score";				
				$class="blue-dark";
				$status="info-circle";		
				$value=$site_info[0]['speed_usability_mobile'];		
				?>

				<?php if($load_css_js!=1) {?>
				<div class="col-xs-12 table-responsive">								
					<div class="box box-<?php echo $class;?>">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<input id="mobile_usability" type="hidden" value="<?php echo $value; ?>" />
						</div>
					</div>							
				</div> <!-- col end -->

				<?php } else { ?>
				<div class="col-xs-12 col-md-6">
					<div style="border:1px solid #0073B7;border-bottom:2px solid #0073B7;" class="info-box">
						<span class="info-box-icon bg-blue"><i class="fa fa-bullseye"></i></span>
						<div class="info-box-content">
							<span class="info-box-text"><?php echo $item;?></span>
							<span class="info-box-number"><?php echo $value; ?></span>
						</div><!-- /.info-box-content -->
					</div>
				</div>
				<?php } ?>


			</div>


			<div class="row">
				<?php 
				$item="Page Statistics";				
				$class="blue-dark";
				$status="info-circle";				
				?>
				<div class="col-xs-12">
					<div class="box box-<?php echo $class;?>">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive table-responsive-vertical  minus clearfix">
							<?php $curl_response=json_decode($site_info[0]["pagestat_mobile"],true) ?>
								<?php 										
									echo "<br><table class='table table-striped table-bordered'>";
									$sl=0;
									foreach ($curl_response as $key=>$value) 
									{
										if(is_array($value)) $value=implode(",", $value);
										$sl++;
										if(($sl+1)%2==0)
										echo "<tr>";
											echo "<td><b>".str_replace("_"," ",$key)."</b></td>";
											echo "<td>".$value."</td>";
										if($sl%2==0)
										echo "</tr>";
									}
									echo "</table><br><br>";
								?>
						</div>
					</div>							
				</div> <!-- col end -->
			</div>

	

			<div class="row">							
				<div class="col-xs-12">
					<?php 
					$item="Avoid App Install Interstitials That Hide Content";				
					$value=json_decode($site_info[0]["avoid_interstitials_mobile"],true);
					$check=$value["app_count"];
					if(is_numeric($check) && $check>0)
					{
						$class="red";
						$status="remove";	
						$short_recommendation='Your page have <big></b>('.$check.')</b></big> app install interstitials that are hiding a significant amount of content. Learn more about the importance of <a href="https://developers.google.com/webmasters/mobile-sites/mobile-seo/common-mistakes/avoid-interstitials" target="_blank">avoiding the use of app install interstitials</a>.';
						$short_recommendation.="<br><br><ul class='highlight_header_content'>";
						foreach($value["urls"] as $val)
						$short_recommendation.="<li>".$val["result"]["args"][0]["value"]."</li>";
						$short_recommendation.="</ul>";
						
					}
					else
					{
						$class="green";
						$status="check";	
						$short_recommendation='Your page does not appear to have any app install interstitials that hide a significant amount of content. Learn more about the importance of <a href="https://developers.google.com/webmasters/mobile-sites/mobile-seo/common-mistakes/avoid-interstitials" target="_blank">avoiding the use of app install interstitials</a>.';
					}			
				?>
					<div class="box box-<?php echo $class;?> small">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<?php echo $short_recommendation; ?>
						</div>
					</div>	
				</div> <!-- end col -->
			</div>




			<div class="row">							
				<div class="col-xs-12">
					<?php 
					$item="Avoid Plugins";				
					$value=json_decode($site_info[0]["avoid_plugins_mobile"],true);
					$check=$value["plugin_count"];
					if(is_numeric($check) && $check>0)
					{
						$class="red";
						$status="remove";	
						$short_recommendation='Your page have <big></b>('.$check.')</b></big> plugins, which are preventing content from being usable on many platforms. Learn more about the importance of <a href="https://developers.google.com/speed/docs/insights/AvoidPlugins" target="_blank">avoiding plugins</a>.';
						$short_recommendation.="<br><br><ul class='highlight_header_content'>";
						foreach($value["urls"] as $val)
						$short_recommendation.="<li>".$val["result"]["args"][0]["value"]."</li>";
						$short_recommendation.="</ul>";
						
					}
					else
					{
						$class="green";
						$status="check";	
						$short_recommendation='Your page does not appear to use plugins, which would prevent content from being usable on many platforms. Learn more about the importance of <a href="https://developers.google.com/speed/docs/insights/AvoidPlugins" target="_blank">avoiding plugins</a>.';
					}			
				?>
					<div class="box box-<?php echo $class;?> small">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<?php echo $short_recommendation; ?>
						</div>
					</div>	
				</div> <!-- end col -->
			</div>


			<div class="row">							
				<div class="col-xs-12">
					<?php 
					$item="Configure Viewport";				
					$value=json_decode($site_info[0]["configure_viewport_mobile"],true);
					$check=$value["rule_impact"];
					if(is_numeric($check) && $check>0)
					{
						$class="red";
						$status="remove";	
						$short_recommendation='Your page does not have a viewport specified. This causes mobile devices to render your page as it would appear on a desktop browser, scaling it down to fit on a mobile screen. Configure a viewport to allow your page to render properly on all devices.<br/><a href="https://developers.google.com/speed/docs/insights/ConfigureViewport" target="_blank">Configure a viewport</a> for this page.';
			
					}
					else
					{
						$class="green";
						$status="check";	
						$short_recommendation='Your page specifies a viewport matching the device\'s size, which allows it to render properly on all devices. Learn more about <a href="https://developers.google.com/speed/docs/insights/ConfigureViewport" target="_blank">configuring viewports</a>.';
					}			
				?>
					<div class="box box-<?php echo $class;?> tiny">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<?php echo $short_recommendation; ?>
						</div>
					</div>	
				</div> <!-- end col -->
			</div>



			<div class="row">							
				<div class="col-xs-12">
					<?php 
					$item="Size Content to Viewport";				
					$value=json_decode($site_info[0]["size_content_to_viewport_mobile"],true);
					$check=$value["rule_impact"];
					$content_width=$value["content_width"];
					$viewport_width=$value["viewport_width"];
					if(is_numeric($check) && $check>0)
					{
						$class="red";
						$status="remove";	
						$short_recommendation='The page content is too wide for the viewport, forcing the user to scroll horizontally. <br/><a href="https://developers.google.com/speed/docs/insights/SizeContentToViewport" target="_blank">Size the page content to the viewport</a> to provide a better user experience.<br/><br/>The page content is <b>'.$content_width.' CSS pixels</b> wide, but the viewport is only <b>'.$viewport_width.' CSS pixels</b> wide. The following elements fall outside the viewport:';
						$short_recommendation.="<br><br><div class='highlight_header_content'><ul>";
						foreach($value["urls"] as $val)
						$short_recommendation.="<li>".htmlspecialchars($val["result"]["args"][0]["value"])."</li>";
						$short_recommendation.="</ul></div>";
						
					}
					else
					{
						$class="green";
						$status="check";	
						$short_recommendation='The contents of your page fit within the viewport. Learn more about <a href="https://developers.google.com/speed/docs/insights/SizeContentToViewport" target="_blank">sizing content to the viewport</a>.';
					}			
				?>
					<div class="box box-<?php echo $class;?> small">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<?php echo $short_recommendation; ?>
						</div>
					</div>	
				</div> <!-- end col -->
			</div>


			<div class="row">							
				<div class="col-xs-12">
					<?php 
					$item="Size Tap Targets Appropriately";				
					$value=json_decode($site_info[0]["size_tap_targets_appropriately_mobile"],true);
					$check=$value["rule_impact"];
					if(is_numeric($check) && $check>0)
					{
						$class="red";
						$status="remove";	
						$short_recommendation='Some of the links/buttons on your webpage may be too small for a user to easily tap on a touchscreen. Consider <a href="https://developers.google.com/speed/docs/insights/SizeTapTargetsAppropriately" target="_blank">making these tap targets larger</a> to provide a better user experience.The following tap targets are close to other nearby tap targets and may need additional spacing around them.';
						$short_recommendation.="<br><br><div class='highlight_header_content'><ul>";
						foreach($value["urls"] as $val)
						$short_recommendation.="<li>The tap target ".htmlspecialchars($val["result"]["args"][0]["value"])."  is close to <b>".$val["result"]["args"][1]["value"]."</b> other tap targets</li>";
						$short_recommendation.="</ul></div>";
						
					}
					else
					{
						$class="green";
						$status="check";	
						$short_recommendation='All of your page\'s links/buttons are large enough for a user to easily tap on a touchscreen. Learn more about <a href="https://developers.google.com/speed/docs/insights/SizeTapTargetsAppropriately" target="_blank">sizing tap targets appropriately</a>.';
					}			
				?>
					<div class="box box-<?php echo $class;?> small">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<?php echo $short_recommendation; ?>
						</div>
					</div>	
				</div> <!-- end col -->
			</div>



			<div class="row">							
				<div class="col-xs-12">
					<?php 
					$item="Use Legible Font Sizes";				
					$value=json_decode($site_info[0]["use_legible_font_sizes_mobile"],true);
					$check=$value["rule_impact"];
					if(is_numeric($check) && $check>0)
					{
						$class="red";
						$status="remove";	
						$short_recommendation='The following text on your page renders in a way that may be difficult for some of your visitors to read. <a href="https://developers.google.com/speed/docs/insights/UseLegibleFontSizes" target="_blank">Use legible font sizes</a> to provide a better user experience.<br><br>The following text fragments have a small font size. Increase the font size to make them more legible.';
						$short_recommendation.="<br><br><div class='highlight_header_content'><ul>";
						foreach($value["urls"] as $val)
						$short_recommendation.="<li><span class='label label-warning'>".htmlspecialchars($val["result"]["args"][0]["value"])."</span> renders only <b>".$val["result"]["args"][1]["value"]."</b> pixels tall (<b>".$val["result"]["args"][2]["value"]."</b> CSS pixels) </li>";
						$short_recommendation.="</ul></div>";
						
					}
					else
					{
						$class="green";
						$status="check";	
						$short_recommendation='The text on your page is legible. Learn more about <a href="https://developers.google.com/speed/docs/insights/UseLegibleFontSizes" target="_blank">using legible font sizes</a>.';
					}			
				?>
					<div class="box box-<?php echo $class;?> small">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<?php echo $short_recommendation; ?>
						</div>
					</div>	
				</div> <!-- end col -->
			</div>






			<div class="row">							
				<div class="col-xs-12">
					<?php 
					$item="Landing Page Redirects";				
					$value=json_decode($site_info[0]["avoid_landing_page_redirects_mobile"],true);
					$check=$value["redirect_count"];
					if(is_numeric($check) && $check>0)
					{
						$class="red";
						$status="remove";	
						$short_recommendation='Your page has '.$check.' redirects. Redirects introduce additional delays before the page can be loaded. <br><a target="_blank" href="https://developers.google.com/speed/docs/insights/AvoidRedirects">Avoid landing page redirects</a> for the following chain of redirected URLs:';
						$short_recommendation.="<br><br><ul class='highlight_header_content'>";
						foreach($value["urls"] as $val)
						$short_recommendation.="<li>".$val["result"]["args"][0]["value"]."</li>";
						$short_recommendation.="</ul>";
						
					}
					else
					{
						$class="green";
						$status="check";	
						$short_recommendation='Your page has no redirects. Learn more about <a target="_blank" href="https://developers.google.com/speed/docs/insights/AvoidRedirects">avoiding landing page redirects</a>';
					}			
				?>
					<div class="box box-<?php echo $class;?> small">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<?php echo $short_recommendation; ?>
						</div>
					</div>	
				</div> <!-- end col -->
			</div>

			<div class="row">							
				<div class="col-xs-12">
					<?php 
					$item="GZIP Compression";				
					$value=json_decode($site_info[0]["gzip_compression_mobile"],true);
					$check=$value["rule_impact"];
					$size_compressable=$value["total_size_compressable"];
					$percent_compressable=$value["total_percentage_compressable"];
					if(is_numeric($check) && $check>0)
					{
						$class="red";
						$status="remove";	
						$short_recommendation='Compressing resources with gzip or deflate can reduce the number of bytes sent over the network.
						<br><a target="_blank" href="https://developers.google.com/speed/docs/insights/EnableCompression">Enable compression</a> for the following resources to reduce their transfer size by <b>'.$size_compressable.' ( '.$percent_compressable.' reduction )</b>.';
						
						$short_recommendation.="<br><br><div class='highlight_header_content'><ul>";
						foreach($value["urls"] as $val)
						$short_recommendation.="<li>".$val["result"]["args"][0]["value"]." could save <b>".$val["result"]["args"][1]["value"]." ( ".$val["result"]["args"][2]["value"]."reduction )</b> </li>";
						$short_recommendation.="</ul></div>";
						
					}
					else
					{
						$class="green";
						$status="check";	
						$short_recommendation='You have compression enabled. Learn more about <a target="_blank" href="https://developers.google.com/speed/docs/insights/EnableCompression">enabling compression</a>.';
					}			
					?>
					<div class="box box-<?php echo $class;?> small">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<?php echo $short_recommendation; ?>
						</div>
					</div>							
				</div> <!-- col end -->
			</div>


			<div class="row">
				<div class="col-xs-12">
					<?php 
					$item="Leverage Browser Caching";				
					$value=json_decode($site_info[0]["leverage_browser_caching_mobile"],true);
					$check=$value["rule_impact"];
					if(is_numeric($check) && $check>0)
					{
						$class="red";
						$status="remove";	
						$short_recommendation='Setting an expiry date or a maximum age in the HTTP headers for static resources instructs the browser to load previously downloaded resources from local disk rather than over the network.
						<br><a target="_blank" href="https://developers.google.com/speed/docs/insights/LeverageBrowserCaching">Leverage browser caching</a> for the following cacheable resources:';
						
						$short_recommendation.="<br><br><div class='highlight_header_content'><ul>";
						foreach($value["urls"] as $val)
						$short_recommendation.="<li>".$val["result"]["args"][0]["value"]."</li>";
						$short_recommendation.="</ul></div>";
						
					}
					else
					{
						$class="green";
						$status="check";	
						$short_recommendation='You have enabled browser caching. Learn more about <a target="_blank" href="https://developers.google.com/speed/docs/insights/LeverageBrowserCaching">browser caching recommendations</a>.';
					}			
					?>

					<div class="box box-<?php echo $class;?> small">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<?php echo $short_recommendation; ?>
						</div>
					</div>							
				</div> <!-- col end -->
			</div>


			<div class="row">
				<div class="col-xs-12">
					<?php 
					$item="Server Response Time";				
					$value=json_decode($site_info[0]["main_resource_server_response_time_mobile"],true);
					$check=$value["rule_impact"];
					if(is_numeric($check) && $check>0)
					{
						$class="red";
						$status="remove";	
						$short_recommendation='In our test, your server responded in <b>'.$value["response_time"].'</b>. There are many factors that can slow down your server response time. <br><a href="https://developers.google.com/speed/docs/insights/Server" target="_blank">Please read our recommendations</a> to learn how you can monitor and measure where your server is spending the most time.';
					}
					else
					{
						$class="green";
						$status="check";	
						$short_recommendation='Your server responded quickly. Learn more about <a href="https://developers.google.com/speed/docs/insights/Server" target="_blank">server response time optimization</a>.';
					}			
					?>

					<div class="box box-<?php echo $class;?> tiny">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<?php echo $short_recommendation; ?>
						</div>
					</div>							
				</div> <!-- col end -->
			</div>



			<div class="row">							
				<div class="col-xs-12">
					<?php 
					$item="Minify CSS";				
					$value=json_decode($site_info[0]["minify_css_mobile"],true);
					$check=$value["rule_impact"];
					if(is_numeric($check) && $check>0)
					{
						$class="red";
						$status="remove";	
						$size_minifiable=$check=$value["total_size_minifiable"];
						$percent_minifiable=$check=$value["total_percentage_minifiable"];
						$short_recommendation='Compacting CSS code can save many bytes of data and speed up download and parse times. <br><a href="https://developers.google.com/speed/docs/insights/MinifyResources" target="_blank">Minify CSS</a> for the following resources to reduce their size by  <b>'.$size_minifiable.' ( '.$percent_minifiable.' reduction )</b>.';
						$short_recommendation.="<br><br><div class='highlight_header_content'><ul>";
						foreach($value["urls"] as $val)
						$short_recommendation.="<li>".$val["result"]["args"][0]["value"]." could save <b>".$val["result"]["args"][1]["value"]." ( ".$val["result"]["args"][2]["value"]."reduction )</b> </li>";
						$short_recommendation.="</ul></div>";
						
					}
					else
					{
						$class="green";
						$status="check";	
						$short_recommendation='Your CSS is minified. Learn more about <a href="https://developers.google.com/speed/docs/insights/MinifyResources" target="_blank">minifying CSS</a>.';
					}			
					?>
					<div class="box box-<?php echo $class;?> small">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<?php echo $short_recommendation; ?>
						</div>
					</div>							
				</div> <!-- col end -->
			</div>



			<div class="row">							
				<div class="col-xs-12">
					<?php 
					$item="Minify HTML";				
					$value=json_decode($site_info[0]["minify_html_mobile"],true);
					$check=$value["rule_impact"];
					if(is_numeric($check) && $check>0)
					{
						$class="red";
						$status="remove";	
						$size_minifiable=$check=$value["total_size_minifiable"];
						$percent_minifiable=$check=$value["total_percentage_minifiable"];
						$short_recommendation='Compacting HTML code, including any inline JavaScript and CSS contained in it, can save many bytes of data and speed up download and parse times. <br><a href="https://developers.google.com/speed/docs/insights/MinifyResources" target="_blank">Minify HTML</a> for the following resources to reduce their size by  <b>'.$size_minifiable.' ( '.$percent_minifiable.' reduction )</b>.';
						$short_recommendation.="<br><br><div class='highlight_header_content'><ul>";
						foreach($value["urls"] as $val)
						$short_recommendation.="<li>".$val["result"]["args"][0]["value"]." could save <b>".$val["result"]["args"][1]["value"]." ( ".$val["result"]["args"][2]["value"]."reduction )</b> </li>";
						$short_recommendation.="</ul></div>";
						
					}
					else
					{
						$class="green";
						$status="check";	
						$short_recommendation='Your HTML is minified. Learn more about <a href="https://developers.google.com/speed/docs/insights/MinifyResources" target="_blank">minifying HTML</a>.';
					}			
					?>
					<div class="box box-<?php echo $class;?> small">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<?php echo $short_recommendation; ?>
						</div>
					</div>							
				</div> <!-- col end -->
			</div>


			<div class="row">							
				<div class="col-xs-12">
					<?php 
					$item="Minify JavaScript";				
					$value=json_decode($site_info[0]["minify_javaScript_mobile"],true);
					$check=$value["rule_impact"];
					if(is_numeric($check) && $check>0)
					{
						$class="red";
						$status="remove";	
						$size_minifiable=$check=$value["total_size_minifiable"];
						$percent_minifiable=$check=$value["total_percentage_minifiable"];
						$short_recommendation='Compacting JavaScript code can save many bytes of data and speed up downloading, parsing, and execution time. <br><a href="https://developers.google.com/speed/docs/insights/MinifyResources" target="_blank">Minify JavaScript</a> for the following resources to reduce their size by  <b>'.$size_minifiable.' ( '.$percent_minifiable.' reduction )</b>.';
						$short_recommendation.="<br><br><div class='highlight_header_content'><ul>";
						foreach($value["urls"] as $val)
						$short_recommendation.="<li>".$val["result"]["args"][0]["value"]." could save <b>".$val["result"]["args"][1]["value"]." ( ".$val["result"]["args"][2]["value"]."reduction )</b> </li>";
						$short_recommendation.="</ul></div>";
						
					}
					else
					{
						$class="green";
						$status="check";	
						$short_recommendation='Your JavaScript content is minified. Learn more about <a href="https://developers.google.com/speed/docs/insights/MinifyResources" target="_blank">minifying HTML</a>.';
					}			
					?>
					<div class="box box-<?php echo $class;?> small">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<?php echo $short_recommendation; ?>
						</div>
					</div>							
				</div> <!-- col end -->
			</div>


			<div class="row">							
				<div class="col-xs-12">
					<?php 
					$item="Render-blocking JavaScript and CSS";				
					$value=json_decode($site_info[0]["minimize_render_blocking_resources_mobile"],true);
					$check=$value["rule_impact"];
					if(is_numeric($check) && $check>0)
					{
						$class="red";
						$status="remove";	
						$blocking_js_count=isset($value["js_urls"]) ? count($value["js_urls"]) : 0;
						$blocking_css_count=isset($value["css_urls"]) ? count($value["css_urls"]) : 0;
						$short_recommendation="<div class='col-xs-12'>Your page has <big><b>(".$blocking_js_count.")</b></big> blocking script resources and <big><b>(".$blocking_css_count.")</b></big> blocking CSS resources. This causes a delay in rendering your page. None of the above-the-fold content on your page could be rendered without waiting for the following resources to load. Try to defer or asynchronously load blocking resources, or inline the critical portions of those resources directly in the HTML.</div>";

						if(isset($value["js_urls"]))
						$short_recommendation.="
						<div class='col-xs-12'>
							<br/><h3 class='highlight_header'>Render-blocking JavaScript</h3>
							<div class='highlight_header_content'>
								<ul>";
									foreach($value["js_urls"] as $val)
									$short_recommendation.="<li>".$val["result"]["args"][0]["value"]."</li>";
									$short_recommendation.="
								</ul>
							</div>
						</div>";

						if(isset($value["css_urls"]))
							$short_recommendation.="
						<div class='col-xs-12'>
							<br><h3 class='highlight_header'>Render-blocking CSS</h3>
							<div class='highlight_header_content'>
								<ul>";
									foreach($value["css_urls"] as $val)
									$short_recommendation.="<li>".$val["result"]["args"][0]["value"]."</li>";
									$short_recommendation.="
								</ul>
							</div>
						</div>";
						
					}
					else
					{
						$class="green";
						$status="check";	
						$short_recommendation='You have no render-blocking resources. Learn more about <a href="https://developers.google.com/speed/docs/insights/BlockingJS" target="_blank">removing render-blocking resources</a>.';
					}			
					?>
					<div class="box box-<?php echo $class;?> large">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<?php echo $short_recommendation; ?>
						</div>
					</div>							
				</div> <!-- col end -->
			</div>



			<div class="row">							
				<div class="col-xs-12">
					<?php 
					$item="Optimize Images";				
					$value=json_decode($site_info[0]["optimize_images_mobile"],true);
					$check=$value["rule_impact"];
					$size_compressable=$value["total_size_optimizable"];
					$percent_compressable=$value["total_percentage_optimizable"];
					if(is_numeric($check) && $check>0)
					{
						$class="red";
						$status="remove";	
						$short_recommendation='Properly formatting and compressing images can save many bytes of data.
						<br><a target="_blank" href="https://developers.google.com/speed/docs/insights/OptimizeImages">Optimize the following images</a> to reduce their transfer size by <b>'.$size_compressable.' ( '.$percent_compressable.' reduction )</b>.';
						
						$short_recommendation.="<br><br><div class='highlight_header_content'><ul>";
						foreach($value["urls"] as $val)
						$short_recommendation.="<li>".$val["result"]["args"][0]["value"]." could save <b>".$val["result"]["args"][1]["value"]." ( ".$val["result"]["args"][2]["value"]."reduction )</b> </li>";
						$short_recommendation.="</ul></div>";
						
					}
					else
					{
						$class="green";
						$status="check";	
						$short_recommendation='Your images are optimized. Learn more about <a href="https://developers.google.com/speed/docs/insights/OptimizeImages" target="_blank">optimizing images</a>.';
					}			
					?> 
					<div class="box box-<?php echo $class;?> small">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<?php echo $short_recommendation; ?>
						</div>
					</div>							
				</div> <!-- col end -->
			</div>



			<div class="row">							
				<div class="col-xs-12">
					<?php 
					$item="Prioritize Visible Content";				
					$value=json_decode($site_info[0]["prioritize_visible_content_mobile"],true);
					$check=$value["rule_impact"];
					if(is_numeric($check) && $check>0)
					{
						$class="red";
						$status="remove";	
						$short_recommendation='Visible content is not properly prioritized. Learn more about <a href="https://developers.google.com/speed/docs/insights/PrioritizeVisibleContent" target="_blank">prioritizing visible content</a>.';					
			
					}
					else
					{
						$class="green";
						$status="check";	
						$short_recommendation='You have the above-the-fold content properly prioritized. Learn more about <a href="https://developers.google.com/speed/docs/insights/PrioritizeVisibleContent" target="_blank">prioritizing visible content</a>.';
					}			
					?>
					<div class="box box-<?php echo $class;?> tiny">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<?php echo $short_recommendation; ?>
						</div>
					</div>							
				</div> <!-- col end -->
			</div>



		</div>
	</div> 
</div>
</div> <!-- end of mobile -->









<div class="row">
<div class="col-xs-12">		
	<?php 
	$item="Google Page Speed Insight (Desktop)";				
	$class="blue-dark";
	$status="info-circle";				
	?>
	<div class="box box-<?php echo $class;?>">
		<div class="box-header with-border">
			<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
			<div class="box-tools pull-right">
				<i class="fa fa-minus minus"></i>
			</div>
		</div>
		<div class="box-body chart-responsive minus clearfix">
			
			<div class="row">
				<br>
				<?php 
				$item="Page Statistics";				
				$class="blue-dark";
				$status="info-circle";				
				?>
				<div class="col-xs-12 table-responsive">
					<div class="box box-<?php echo $class;?>">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive table-responsive-vertical minus clearfix">
							<?php $curl_response=json_decode($site_info[0]["pagestat"],true) ?>
								<?php 										
									echo "<br><table class='table table-striped table-bordered'>";
									$sl=0;
									foreach ($curl_response as $key=>$value) 
									{
										if(is_array($value)) $value=implode(",", $value);
										$sl++;
										if(($sl+1)%2==0)
										echo "<tr>";
											echo "<td><b>".str_replace("_"," ",$key)."</b></td>";
											echo "<td>".$value."</td>";
										if($sl%2==0)
										echo "</tr>";
									}
									echo "</table><br><br>";
								?>
						</div>
					</div>							
				</div> <!-- col end -->

				<?php 
					$item="Page Speed";				
					$class="blue-dark";
					$status="info-circle";	
					$value=$site_info[0]['speed_score'];			
				?>
				<?php if($load_css_js!=1) { ?>
				<div class="col-xs-12 table-responsive">								
					<div class="box box-<?php echo $class;?>">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<input id="desktop_speed" type="hidden" value="<?php echo $value; ?>" />
						</div>
					</div>							
				</div> <!-- col end -->

				<?php } else { ?>
				<div class="col-xs-12 col-md-4">
					<div style="border:1px solid #0073B7;border-bottom:2px solid #0073B7;" class="info-box">
						<span class="info-box-icon bg-blue"><i class="fa fa-bullseye"></i></span>
						<div class="info-box-content">
							<span class="info-box-text"><?php echo $item;?></span>
							<span class="info-box-number"><?php echo $value; ?></span>
						</div><!-- /.info-box-content -->
					</div>
				</div>
				<?php } ?>

			</div>




			<div class="row">							
				<div class="col-xs-12">
					<?php 
					$item="Landing Page Redirects";				
					$value=json_decode($site_info[0]["avoid_landing_page_redirects"],true);
					$check=$value["redirect_count"];
					if(is_numeric($check) && $check>0)
					{
						$class="red";
						$status="remove";	
						$short_recommendation='Your page has '.$check.' redirects. Redirects introduce additional delays before the page can be loaded. <br><a target="_blank" href="https://developers.google.com/speed/docs/insights/AvoidRedirects">Avoid landing page redirects</a> for the following chain of redirected URLs:';
						$short_recommendation.="<br><br><ul class='highlight_header_content'>";
						foreach($value["urls"] as $val)
						$short_recommendation.="<li>".$val["result"]["args"][0]["value"]."</li>";
						$short_recommendation.="</ul>";
						
					}
					else
					{
						$class="green";
						$status="check";	
						$short_recommendation='Your page has no redirects. Learn more about <a target="_blank" href="https://developers.google.com/speed/docs/insights/AvoidRedirects">avoiding landing page redirects</a>';
					}			
				?>
					<div class="box box-<?php echo $class;?> small">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<?php echo $short_recommendation; ?>
						</div>
					</div>	
				</div> <!-- end col -->
			</div>

			<div class="row">							
				<div class="col-xs-12">
					<?php 
					$item="GZIP Compression";				
					$value=json_decode($site_info[0]["gzip_compression"],true);
					$check=$value["rule_impact"];
					$size_compressable=$value["total_size_compressable"];
					$percent_compressable=$value["total_percentage_compressable"];
					if(is_numeric($check) && $check>0)
					{
						$class="red";
						$status="remove";	
						$short_recommendation='Compressing resources with gzip or deflate can reduce the number of bytes sent over the network.
						<br><a target="_blank" href="https://developers.google.com/speed/docs/insights/EnableCompression">Enable compression</a> for the following resources to reduce their transfer size by <b>'.$size_compressable.' ( '.$percent_compressable.' reduction )</b>.';
						
						$short_recommendation.="<br><br><div class='highlight_header_content'><ul>";
						foreach($value["urls"] as $val)
						$short_recommendation.="<li>".$val["result"]["args"][0]["value"]." could save <b>".$val["result"]["args"][1]["value"]." ( ".$val["result"]["args"][2]["value"]."reduction )</b> </li>";
						$short_recommendation.="</ul></div>";
						
					}
					else
					{
						$class="green";
						$status="check";	
						$short_recommendation='You have compression enabled. Learn more about <a target="_blank" href="https://developers.google.com/speed/docs/insights/EnableCompression">enabling compression</a>.';
					}			
					?>
					<div class="box box-<?php echo $class;?> small">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<?php echo $short_recommendation; ?>
						</div>
					</div>							
				</div> <!-- col end -->
			</div>


			<div class="row">
				<div class="col-xs-12">
					<?php 
					$item="Leverage Browser Caching";				
					$value=json_decode($site_info[0]["leverage_browser_caching"],true);
					$check=$value["rule_impact"];
					if(is_numeric($check) && $check>0)
					{
						$class="red";
						$status="remove";	
						$short_recommendation='Setting an expiry date or a maximum age in the HTTP headers for static resources instructs the browser to load previously downloaded resources from local disk rather than over the network.
						<br><a target="_blank" href="https://developers.google.com/speed/docs/insights/LeverageBrowserCaching">Leverage browser caching</a> for the following cacheable resources:';
						
						$short_recommendation.="<br><br><div class='highlight_header_content'><ul>";
						foreach($value["urls"] as $val)
						$short_recommendation.="<li>".$val["result"]["args"][0]["value"]."</li>";
						$short_recommendation.="</ul></div>";
						
					}
					else
					{
						$class="green";
						$status="check";	
						$short_recommendation='You have enabled browser caching. Learn more about <a target="_blank" href="https://developers.google.com/speed/docs/insights/LeverageBrowserCaching">browser caching recommendations</a>.';
					}			
					?>

					<div class="box box-<?php echo $class;?> small">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<?php echo $short_recommendation; ?>
						</div>
					</div>							
				</div> <!-- col end -->
			</div>


			<div class="row">
				<div class="col-xs-12">
					<?php 
					$item="Server Response Time";				
					$value=json_decode($site_info[0]["main_resource_server_response_time"],true);
					$check=$value["rule_impact"];
					if(is_numeric($check) && $check>0)
					{
						$class="red";
						$status="remove";	
						$short_recommendation='In our test, your server responded in <b>'.$value["response_time"].'</b>. There are many factors that can slow down your server response time. <br><a href="https://developers.google.com/speed/docs/insights/Server" target="_blank">Please read our recommendations</a> to learn how you can monitor and measure where your server is spending the most time.';
					}
					else
					{
						$class="green";
						$status="check";	
						$short_recommendation='Your server responded quickly. Learn more about <a href="https://developers.google.com/speed/docs/insights/Server" target="_blank">server response time optimization</a>.';
					}			
					?>

					<div class="box box-<?php echo $class;?> tiny">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<?php echo $short_recommendation; ?>
						</div>
					</div>							
				</div> <!-- col end -->
			</div>



			<div class="row">							
				<div class="col-xs-12">
					<?php 
					$item="Minify CSS";				
					$value=json_decode($site_info[0]["minify_css"],true);
					$check=$value["rule_impact"];
					if(is_numeric($check) && $check>0)
					{
						$class="red";
						$status="remove";	
						$size_minifiable=$check=$value["total_size_minifiable"];
						$percent_minifiable=$check=$value["total_percentage_minifiable"];
						$short_recommendation='Compacting CSS code can save many bytes of data and speed up download and parse times. <br><a href="https://developers.google.com/speed/docs/insights/MinifyResources" target="_blank">Minify CSS</a> for the following resources to reduce their size by  <b>'.$size_minifiable.' ( '.$percent_minifiable.' reduction )</b>.';
						$short_recommendation.="<br><br><div class='highlight_header_content'><ul>";
						foreach($value["urls"] as $val)
						$short_recommendation.="<li>".$val["result"]["args"][0]["value"]." could save <b>".$val["result"]["args"][1]["value"]." ( ".$val["result"]["args"][2]["value"]."reduction )</b> </li>";
						$short_recommendation.="</ul></div>";
						
					}
					else
					{
						$class="green";
						$status="check";	
						$short_recommendation='Your CSS is minified. Learn more about <a href="https://developers.google.com/speed/docs/insights/MinifyResources" target="_blank">minifying CSS</a>.';
					}			
					?>
					<div class="box box-<?php echo $class;?> small">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<?php echo $short_recommendation; ?>
						</div>
					</div>							
				</div> <!-- col end -->
			</div>



			<div class="row">							
				<div class="col-xs-12">
					<?php 
					$item="Minify HTML";				
					$value=json_decode($site_info[0]["minify_html"],true);
					$check=$value["rule_impact"];
					if(is_numeric($check) && $check>0)
					{
						$class="red";
						$status="remove";	
						$size_minifiable=$check=$value["total_size_minifiable"];
						$percent_minifiable=$check=$value["total_percentage_minifiable"];
						$short_recommendation='Compacting HTML code, including any inline JavaScript and CSS contained in it, can save many bytes of data and speed up download and parse times. <br><a href="https://developers.google.com/speed/docs/insights/MinifyResources" target="_blank">Minify HTML</a> for the following resources to reduce their size by  <b>'.$size_minifiable.' ( '.$percent_minifiable.' reduction )</b>.';
						$short_recommendation.="<br><br><div class='highlight_header_content'><ul>";
						foreach($value["urls"] as $val)
						$short_recommendation.="<li>".$val["result"]["args"][0]["value"]." could save <b>".$val["result"]["args"][1]["value"]." ( ".$val["result"]["args"][2]["value"]."reduction )</b> </li>";
						$short_recommendation.="</ul></div>";
						
					}
					else
					{
						$class="green";
						$status="check";	
						$short_recommendation='Your HTML is minified. Learn more about <a href="https://developers.google.com/speed/docs/insights/MinifyResources" target="_blank">minifying HTML</a>.';
					}			
					?>
					<div class="box box-<?php echo $class;?> small">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<?php echo $short_recommendation; ?>
						</div>
					</div>							
				</div> <!-- col end -->
			</div>


			<div class="row">							
				<div class="col-xs-12">
					<?php 
					$item="Minify JavaScript";				
					$value=json_decode($site_info[0]["minify_javaScript"],true);
					$check=$value["rule_impact"];
					if(is_numeric($check) && $check>0)
					{
						$class="red";
						$status="remove";	
						$size_minifiable=$check=$value["total_size_minifiable"];
						$percent_minifiable=$check=$value["total_percentage_minifiable"];
						$short_recommendation='Compacting JavaScript code can save many bytes of data and speed up downloading, parsing, and execution time. <br><a href="https://developers.google.com/speed/docs/insights/MinifyResources" target="_blank">Minify JavaScript</a> for the following resources to reduce their size by  <b>'.$size_minifiable.' ( '.$percent_minifiable.' reduction )</b>.';
						$short_recommendation.="<br><br><div class='highlight_header_content'><ul>";
						foreach($value["urls"] as $val)
						$short_recommendation.="<li>".$val["result"]["args"][0]["value"]." could save <b>".$val["result"]["args"][1]["value"]." ( ".$val["result"]["args"][2]["value"]."reduction )</b> </li>";
						$short_recommendation.="</ul></div>";
						
					}
					else
					{
						$class="green";
						$status="check";	
						$short_recommendation='Your JavaScript content is minified. Learn more about <a href="https://developers.google.com/speed/docs/insights/MinifyResources" target="_blank">minifying HTML</a>.';
					}			
					?>
					<div class="box box-<?php echo $class;?> small">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<?php echo $short_recommendation; ?>
						</div>
					</div>							
				</div> <!-- col end -->
			</div>


			<div class="row">							
				<div class="col-xs-12">
					<?php 
					$item="Render-blocking JavaScript and CSS";				
					$value=json_decode($site_info[0]["minimize_render_blocking_resources"],true);
					$check=$value["rule_impact"];
					if(is_numeric($check) && $check>0)
					{
						$class="red";
						$status="remove";	
						$blocking_js_count=isset($value["js_urls"]) ? count($value["js_urls"]) : 0;
						$blocking_css_count=isset($value["css_urls"]) ? count($value["css_urls"]) : 0;
						$short_recommendation="<div class='col-xs-12'>Your page has <big><b>(".$blocking_js_count.")</b></big> blocking script resources and <big><b>(".$blocking_css_count.")</b></big> blocking CSS resources. This causes a delay in rendering your page. None of the above-the-fold content on your page could be rendered without waiting for the following resources to load. Try to defer or asynchronously load blocking resources, or inline the critical portions of those resources directly in the HTML.</div>";

						if(isset($value["js_urls"]))
						$short_recommendation.="
						<div class='col-xs-12'>
							<br/><h3 class='highlight_header'>Render-blocking JavaScript</h3>
							<div class='highlight_header_content'>
								<ul>";
									foreach($value["js_urls"] as $val)
									$short_recommendation.="<li>".$val["result"]["args"][0]["value"]."</li>";
									$short_recommendation.="
								</ul>
							</div>
						</div>";

						if(isset($value["css_urls"]))
							$short_recommendation.="
						<div class='col-xs-12'>
							<br><h3 class='highlight_header'>Render-blocking CSS</h3>
							<div class='highlight_header_content'>
								<ul>";
									foreach($value["css_urls"] as $val)
									$short_recommendation.="<li>".$val["result"]["args"][0]["value"]."</li>";
									$short_recommendation.="
								</ul>
							</div>
						</div>";
						
					}
					else
					{
						$class="green";
						$status="check";	
						$short_recommendation='You have no render-blocking resources. Learn more about <a href="https://developers.google.com/speed/docs/insights/BlockingJS" target="_blank">removing render-blocking resources</a>.';
					}			
					?>
					<div class="box box-<?php echo $class;?> large">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<?php echo $short_recommendation; ?>
						</div>
					</div>							
				</div> <!-- col end -->
			</div>



			<div class="row">							
				<div class="col-xs-12">
					<?php 
					$item="Optimize Images";				
					$value=json_decode($site_info[0]["optimize_images"],true);
					$check=$value["rule_impact"];
					$size_compressable=$value["total_size_optimizable"];
					$percent_compressable=$value["total_percentage_optimizable"];
					if(is_numeric($check) && $check>0)
					{
						$class="red";
						$status="remove";	
						$short_recommendation='Properly formatting and compressing images can save many bytes of data.
						<br><a target="_blank" href="https://developers.google.com/speed/docs/insights/OptimizeImages">Optimize the following images</a> to reduce their transfer size by <b>'.$size_compressable.' ( '.$percent_compressable.' reduction )</b>.';
						
						$short_recommendation.="<br><br><div class='highlight_header_content'><ul>";
						foreach($value["urls"] as $val)
						$short_recommendation.="<li>".$val["result"]["args"][0]["value"]." could save <b>".$val["result"]["args"][1]["value"]." ( ".$val["result"]["args"][2]["value"]."reduction )</b> </li>";
						$short_recommendation.="</ul></div>";
						
					}
					else
					{
						$class="green";
						$status="check";	
						$short_recommendation='Your images are optimized. Learn more about <a href="https://developers.google.com/speed/docs/insights/OptimizeImages" target="_blank">optimizing images</a>.';
					}			
					?>
					<div class="box box-<?php echo $class;?> small">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<?php echo $short_recommendation; ?>
						</div>
					</div>							
				</div> <!-- col end -->
			</div>



			<div class="row">							
				<div class="col-xs-12">
					<?php 
					$item="Prioritize Visible Content";				
					$value=json_decode($site_info[0]["prioritize_visible_content"],true);
					$check=$value["rule_impact"];
					if(is_numeric($check) && $check>0)
					{
						$class="red";
						$status="remove";	
						$short_recommendation='Visible content is not properly prioritized. Learn more about <a href="https://developers.google.com/speed/docs/insights/PrioritizeVisibleContent" target="_blank">prioritizing visible content</a>.';					
			
					}
					else
					{
						$class="green";
						$status="check";	
						$short_recommendation='You have the above-the-fold content properly prioritized. Learn more about <a href="https://developers.google.com/speed/docs/insights/PrioritizeVisibleContent" target="_blank">prioritizing visible content</a>.';
					}			
					?>
					<div class="box box-<?php echo $class;?> tiny">
						<div class="box-header with-border">
							<h3 class="box-title <?php echo $class;?>"><i class="fa fa-<?php echo $status;?>"></i> <?php echo $item; ?></h3>
							<div class="box-tools pull-right">
								<i class="fa fa-minus minus"></i>
							</div>
						</div>
						<div class="box-body chart-responsive table-responsive minus clearfix">
							<?php echo $short_recommendation; ?>
						</div>
					</div>							
				</div> <!-- col end -->
			</div>






		</div>
	</div> 
</div>
</div> <!-- end of desktop -->