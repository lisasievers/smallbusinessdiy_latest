<?php
	$h1 = json_decode($meta_tag_info[0]['h1']);
	$h2 = json_decode($meta_tag_info[0]['h2']);
	$h3 = json_decode($meta_tag_info[0]['h3']);
	$h4 = json_decode($meta_tag_info[0]['h4']);
	$h5 = json_decode($meta_tag_info[0]['h5']);
	$h6 = json_decode($meta_tag_info[0]['h6']);
	$meta_tag_information = json_decode($meta_tag_info[0]['meta_tag_information']);
	$blocked_by_robot_txt = $meta_tag_info[0]['blocked_by_robot_txt'];
	$blocked_by_meta_robot = $meta_tag_info[0]['blocked_by_meta_robot'];
	$nofollowed_by_meta_robot = $meta_tag_info[0]['nofollowed_by_meta_robot'];
	$one_phrase = json_decode($meta_tag_info[0]['one_phrase']);
	$two_phrase = json_decode($meta_tag_info[0]['two_phrase']);
	$three_phrase = json_decode($meta_tag_info[0]['three_phrase']);
	$four_phrase = json_decode($meta_tag_info[0]['four_phrase']);
	$total_words = $meta_tag_info[0]['total_words'];
	$domain_name = $meta_tag_info[0]['domain_name'];

	$array_spam_keyword = array( "as seen on","buying judgments", "order status", "dig up dirt on friends",
 "additional income", "double your", "earn per week", "home based", "income from home", "money making",
 "opportunity", "while you sleep", "$$$", "beneficiary", "cash", "cents on the dollar", "claims",
 "cost", "discount", "f r e e", "hidden assets", "incredible deal", "loans", "money",
 "mortgage rates", "one hundred percent free", "price", "quote", "save big money", "subject to credit",
 "unsecured debt", "accept credit cards", "credit card offers", "investment decision",
 "no investment", "stock alert", "avoid bankruptcy", "consolidate debt and credit",
 "eliminate debt", "get paid", "lower your mortgage rate", "refinance home", "acceptance",
 "chance", "here", "leave", "maintained", "never", "remove", "satisfaction", "success", 
 "dear [email/friend/somebody]", "ad", "click", "click to remove", "email harvest", "increase sales",
 "internet market", "marketing solutions", "month trial offer", "notspam",
 "open", "removal instructions", "search engine listings", "the following form", "undisclosed recipient",
 "we hate spam", "cures baldness", "human growth hormone", "lose weight spam", "online pharmacy", 
 "stop snoring", "vicodin", "#1", "4u", "billion dollars", "million", "being a member",
 "cannot be combined with any other offer", "financial freedom", "guarantee",
 "important information regarding", "mail in order form", "nigerian", "no claim forms", "no gimmick", 
 "no obligation", "no selling", "not intended", "offer", "priority mail", "produced and sent out",
 "stuff on sale", "they’re just giving it away", "unsolicited", "warranty", "what are you waiting for?",
 "winner", "you are a winner!", "cancel at any time", "get", "print out and fax", "free", 
 "free consultation", "free grant money", "free instant", "free membership", "free preview ",
  "free sample ", "all natural", "certified", "fantastic deal", "it’s effective",  "real thing",
 "access", "apply online", "can't live without", "don't hesitate", "for you", "great offer", "instant", 
 "now", "once in lifetime", "order now", "special promotion", "time limited", "addresses on cd",
 "brand new pager", "celebrity", "legal", "phone", "buy", "clearance", "orders shipped by", 
 "meet singles", "be your own boss", "earn $", "expect to earn", "home employment", "make $",
 "online biz opportunity", "potential earnings", "work at home", "affordable",
 "best price", "cash bonus", "cheap", "collect", "credit", "earn", "fast cash",
 "hidden charges", "insurance", "lowest price", "money back", "no cost", "only '$'", "profits", 
 "refinance",  "save up to",  "they keep your money -- no refund!",  "us dollars",
 "cards accepted", "explode your business", "no credit check", "requires initial investment",
 "stock disclaimer statement ", "calling creditors", "consolidate your debt", "financially independent",
 "lower interest rate", "lowest insurance rates", "social security number", "accordingly", "dormant",
 "hidden", "lifetime", "medium", "passwords", "reverses", "solution", "teen", "friend",
 "auto email removal", "click below", "direct email", "email marketing",
 "increase traffic", "internet marketing", "mass email", "more internet traffic", "one time mailing",
 "opt in", "sale", "search engines", "this isn't junk", "unsubscribe",
 "web traffic", "diagnostics", "life insurance", "medicine", "removes wrinkles",
 "valium", "weight loss", "100% free", "50% off", "join millions",
 "one hundred percent guaranteed", "billing address", "confidentially on all orders", "gift certificate",
 "have you been turned down?", "in accordance with laws", "message contains", "no age restrictions", 
 "no disappointment", "no inventory", "no purchase necessary", "no strings attached", "obligation",
 "per day", "prize", "reserves the right", "terms and conditions", "trial", "vacation",
 "we honor all", "who really wins?", "winning", "you have been selected",
 "compare", "give it away", "see for yourself", "free access", "free dvd", "free hosting",
 "free investment", "free money", "free priority mail", "free trial",
 "all new", "congratulations", "for free", "outstanding values", "risk free",
 "act now!", "call free", "do it today", "for instant access", "get it now",
 "info you requested", "limited time", "now only", "one time", "order today",
 "supplies are limited", "urgent", "beverage", "cable converter", "copy dvds", "luxury car",
 "rolex", "buy direct", "order", "shopper", "score with babes", "compete for your business",
 "earn extra cash", "extra income", "homebased business", "make money", "online degree", 
 "university diplomas", "work from home", "bargain", "big bucks", "cashcashcash",  "check",
 "compare rates", "credit bureaus", "easy terms", 'for just "$xxx"',  "income",  "investment",
 "million dollars", "mortgage", "no fees", "pennies a day", "pure profit",  "save $",
 "serious cash", "unsecured credit", "why pay more?", "check or money order", "full refund",
 "no hidden costs", "sent in compliance", "stock pick", "collect child support",
 "eliminate bad credit", "get out of debt", "lower monthly payment", "pre-approved",
 "your income", "avoid", "freedom", "home",  "lose", "miracle", "problem", "sample",
 "stop", "wife", "hello", "bulk email", "click here", "direct marketing", "form",
 "increase your sales", "marketing", "member", "multi level marketing", "online marketing", 
 "performance", "sales", "subscribe", "this isn't spam", "visit our website", 
 "will not believe your eyes", "fast viagra delivery", "lose weight",
 "no medical exams", "reverses aging", "viagra", "xanax", "100% satisfied",  "billion", 
 "join millions of americans",  "thousands", "call", "deal", "giving away",
 "if only it were that easy", "long distance phone offer", "name brand", "no catch",
 "no experience", "no middleman", "no questions asked",  "no-obligation", "off shore", "per week", 
 "prizes", "shopping spree", "the best rates", "unlimited", "vacation offers",  "weekend getaway",
 "win", "won", "you’re a winner!", "copy accurately", "print form signature",
 "sign up free today", "free cell phone", "free gift", "free installation",
 "free leads", "free offer", "free quote", "free website",  "amazing",  "drastically reduced",
 "guaranteed", "promise you", "satisfaction guaranteed", "apply now",
 "call now", "don't delete", "for only", "get started now",  "information you requested",
 "new customers only", "offer expires", "only", "please read",
 "take action now", "while supplies last", "bonus", "casino",
 "laser printer", "new domain extensions", "stainless steel"
 );
?>
<div class="row">
	<div class="col-xs-12">
		<h3><div class="well text-center"><?php echo "Domain Name - ".$domain_name; ?></div></h3>
	</div>
</div>
<div class="row" style="margin-top: 10px;">
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title" style="color: blue; word-spacing: 3px;">TITLE & METATAGS</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div><!-- /.box-tools -->
			</div><!-- /.box-header -->
			<div class="box-body">
				<?php foreach($meta_tag_information as $key=>$value): ?>
					<div class="label label-primary" style="font-size: 12px;"><?php echo ucfirst($key); ?> :</div>
					<div style="word-spacing: 3px;margin-bottom: 5px;"><?php echo $value; ?></div>
				<?php endforeach; ?>
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>

	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
		<div class="well row" style="border-left:2px solid maroon;-webkit-border-radius:5px;border-radius:5px;margin-left:0px;margin-right:0px;">
			<div class="col-lg-8"><div class="box-body-content"> BLOCKED BY ROBOTS.TXT</div></div>
			<div class="col-lg-4">
				<div class="box-body-content">: 
					<span> 
						<?php 
						if($blocked_by_robot_txt == 'No') 
							echo '<span class="label label-success">No</span>'; 
						if($blocked_by_robot_txt == 'Yes') 
							echo '<span class="label label-danger">Yes</span>';
						?> 
					</span>
				</div>
			</div>
		</div>
		<div class="well row" style="border-left:2px solid maroon;-webkit-border-radius:5px;border-radius:5px;margin-left:0px;margin-right:0px;">
			<div class="col-lg-8"><div class="box-body-content"> BLOCKED BY META-ROBOTS</div></div>
			<div class="col-lg-4">
				<div class="box-body-content">: 
					<span> 
						<?php 
						if($blocked_by_meta_robot == 'No') 
							echo '<span class="label label-success">No</span>'; 
						if($blocked_by_meta_robot == 'Yes') 
							echo '<span class="label label-danger">Yes</span>';
						?> 
					</span>
				</div>
			</div>
		</div>
		<div class="well row" style="border-left:2px solid maroon;-webkit-border-radius:5px;border-radius:5px;margin-left:0px;margin-right:0px;">
			<div class="col-lg-8"><div class="box-body-content"> LINKS NOFOLLOWED BY META-ROBOTS</div></div>
			<div class="col-lg-4">
				<div class="box-body-content">: 
					<span> 
						<?php 
						if($nofollowed_by_meta_robot == 'No') 
							echo '<span class="label label-success">No</span>'; 
						if($nofollowed_by_meta_robot == 'Yes') 
							echo '<span class="label label-danger">Yes</span>';
						?> 
					</span>
				</div>
			</div>
		</div>
		<div class="well row" style="border-left:2px solid maroon;-webkit-border-radius:5px;border-radius:5px;margin-left:0px;margin-right:0px;">
			<div class="col-lg-8"><div class="box-body-content"> TOTAL KEYWORDS</div></div>
			<div class="col-lg-4">
				<div class="box-body-content">: 
					<span> 
						<?php 
							echo '<span class="label label-success">'.$total_words.'</span>';
						?> 
					</span>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row" style="margin-top: 15px;">
	<div class="col-xs-12">
		<div class="box bg-blue box-solid">
			<div class="box-header with-border">
				<h3 class="box-title">HTML HEADINGS</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div><!-- /.box-tools -->
			</div><!-- /.box-header -->
			<div class="box-body" style="background: white; color: black;">
				<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
					<h3 style="background: #E1E1E1;padding-left: 10px;">H1(<?php echo count($h1) ?>)</h3>
					<div style="height: 230px; overflow-y: auto; padding-left: 10px;">
						<?php foreach($h1 as $key=>$value): ?>
							<p><?php echo $value; ?></p>
						<?php endforeach; ?>
					</div>
				</div>					
				<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
					<h3 style="background: #E1E1E1;padding-left: 10px;">H2(<?php echo count($h2) ?>)</h3>
					<div style="height: 230px; overflow-y: auto; padding-left: 10px;">
						<?php foreach($h2 as $key=>$value): ?>
							<p><?php echo $value; ?></p>
						<?php endforeach; ?>
					</div>
				</div>					
				<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
					<h3 style="background: #E1E1E1;padding-left: 10px;">H3(<?php echo count($h3) ?>)</h3>
					<div style="height: 230px; overflow-y: auto; padding-left: 10px;">
						<?php foreach($h3 as $key=>$value): ?>
							<p><?php echo $value; ?></p>
						<?php endforeach; ?>
					</div>
				</div>					
				<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
					<h3 style="background: #E1E1E1;padding-left: 10px;">H4(<?php echo count($h4) ?>)</h3>
					<div style="height: 230px; overflow-y: auto; padding-left: 10px;">
						<?php foreach($h4 as $key=>$value): ?>
							<p><?php echo $value; ?></p>
						<?php endforeach; ?>
					</div>
				</div>					
				<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
					<h3 style="background: #E1E1E1;padding-left: 10px;">H5(<?php echo count($h5) ?>)</h3>
					<div style="height: 230px; overflow-y: auto; padding-left: 10px;">
						<?php foreach($h5 as $key=>$value): ?>
							<p><?php echo $value; ?></p>
						<?php endforeach; ?>
					</div>
				</div>					
				<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
					<h3 style="background: #E1E1E1;padding-left: 10px;">H6(<?php echo count($h6) ?>)</h3>
					<div style="height: 230px; overflow-y: auto; padding-left: 10px;">
						<?php foreach($h6 as $key=>$value): ?>
							<p><?php echo $value; ?></p>
						<?php endforeach; ?>
					</div>
				</div>					
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>		
</div>

<div class="row" style="margin-top: 15px;">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<div class="box box-warning">
			<div class="box-header with-border">
				<h3 class="box-title" style="color: blue; word-spacing: 3px;">KEYWORD ANALYSIS</h3>
				<div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div><!-- /.box-tools -->
			</div><!-- /.box-header -->
			<div class="box-body">
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6" style="height: 300px; overflow-y: auto;">
					<table class="table table-condensed table-striped">
						<tr>
							<th>SINGLE KEYWORDS</th>
							<th>OCCURRENCES</th>
							<th>DENSITY</th>
							<th>POSSIBLE SPAM</th>
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
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6" style="height: 300px; overflow-y: auto;">
					<table class="table table-condensed table-striped">
						<tr>
							<th>2 WORD PHRASES</th>
							<th>OCCURRENCES</th>
							<th>DENSITY</th>
							<th>POSSIBLE SPAM</th>
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
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6" style="height: 300px; overflow-y: auto; margin-top: 30px;">
					<table class="table table-condensed table-striped">
						<tr>
							<th>3 WORD PHRASES</th>
							<th>OCCURRENCES</th>
							<th>DENSITY</th>
							<th>POSSIBLE SPAM</th>
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
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6" style="height: 300px; overflow-y: auto; margin-top: 30px;">
					<table class="table table-condensed table-striped">
						<tr>
							<th>4 WORD PHRASES</th>
							<th>OCCURRENCES</th>
							<th>DENSITY</th>
							<th>POSSIBLE SPAM</th>
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
				
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
</div>
