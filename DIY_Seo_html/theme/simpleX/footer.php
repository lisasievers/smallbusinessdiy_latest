<?php
defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));
/*
 * @author Balaji
 * @name: A to Z SEO Tools - PHP Script
 * @Theme: Default Style
 * @copyright © 2016 ProThemes.Biz
 *
 */
?>

<footer class="footer">
 		<!-- Widget Area -->
		<div class="b-widgets">
			<div class="container layout">
				<div class="row">
					<!-- Links -->
					<div class="row-item col-md-3">
						<h3><?php echo $lang['316']; ?></h3>
						<ul class="b-list just-links m-dark">
                            <?php
                            foreach($footerLinks as $footerLink)
                            echo $footerLink;
                            ?>
						</ul>
					</div>
					<!-- End Links -->
					<!-- Latest Tweets -->
					<div class="row-item col-md-3">
						<h3><?php echo $lang['315']; ?></h3>
						<div class="b-twitter m-footer">
							<ul>
								<!-- Twitter Message 1 -->
								<li>
									<span>Lorem Ipsum is simply dummy text of the printing and typesetting.. 
                                    <a class="link" href="#">Read More</a>
                                    
                                    </span>
									<span class="twit-date">May 21, 2015</span>
								</li>
								<!-- Twitter Message 2 -->
								<li>
									<span>Lorem Ipsum is simply dummy text of the printing and typesetting.. 
                                    <a class="link" href="#">Read More</a></span>
									<span class="twit-date">May 20, 2015</span>
								</li>
								<!-- Twitter Message 3 -->
								<li>
									<span>Lorem Ipsum is simply dummy text of the printing and typesetting.. 
                                    <a class="link" href="#">Read More</a></span>
									<span class="twit-date">May 17, 2015</span>
								</li>
							</ul>
						</div>
					</div>
					<!-- End Latest Tweets -->
					<!-- Tag Cloud -->
					<div class="row-item col-md-3">
						<h3><?php echo $lang['314']; ?></h3>
						<div class="b-tag-cloud m-dark">
                        <?php
                        foreach($footer_tags as $footer_tag){
                            echo '<a href="#">'.Trim($footer_tag).'</a>';
                        }
                        ?>
						</div>
					</div>
					<!-- End Tag Cloud -->
					<!-- Contact Form -->
					<div class="row-item col-md-3">
						<h3><?php echo $lang['244']; ?></h3>
						<!-- Success Message -->
						<div class="form-message"></div>
                        <div id="index_alert1"> 
                        <br /><br /><br /><br />
                        <div class="alertNew alertNew-block alertNew-success">
                        <button data-dismiss="alert" class="close" type="button">X</button>
	
                        <i class="fa fa-check green"></i>							
                        <b>Alert!!</b> <?php echo $lang['229']; ?>.
                        </div>
                        </div>  
                        
						<!-- Form -->
                         <div id="indexContact">  
						<form method="POST" action="#" class="b-form b-contact-form">
							<div class="input-wrap m-full-width">
								<i class="fa fa-user"></i>
								<input type="text" placeholder="<?php echo $lang['245']; ?>" class="form-control" name="c_name" />
							</div>
							<div class="input-wrap m-full-width">
								<i class="fa fa-envelope"></i>
								<input type="text" placeholder="<?php echo $lang['246']; ?>" class="form-control" name="c_email" />
							</div>
							<div class="textarea-wrap">
								<i class="fa fa-pencil"></i>
								<textarea placeholder="<?php echo $lang['238']; ?>" class="form-control" name="email_message"></textarea>
							</div>
                            <button class="btn btn-info" onclick="smallContactDoc()" type="button"><?php echo $lang['247']; ?></button>
						</form>
                        </div> 
						<!-- End Form -->
					</div>
					<!-- End Contact Form -->
				</div>
			</div>
		</div>
		<!-- End Widget Area -->
           <div class="container" style="margin-top:10px;" >
                    <div class="row">
                        <div class="none" style="float:left; margin-top: 14px;">
                        <!-- Powered By ProThemes.Biz --> 
                        <!-- Contact Us: http://prothemes.biz/index.php?route=information/contact --> 
                        <?php echo $copyright; ?>
                        </div>
                        <div class="none" style="float:right;">

		<a class="ultm ultm-facebook ultm-32 ultm-color-to-gray" href="<?php echo $face; ?>" target="_blank" rel="nofollow" data-toggle="tooltip" data-placement="top" title="Facebook Page"></a>
		<a class="ultm ultm-twitter ultm-32 ultm-color-to-gray" href="<?php echo $twit; ?>" target="_blank" rel="nofollow" data-toggle="tooltip" data-placement="top" title="Twitter Account"></a>
		<a class="ultm ultm-google-plus-1 ultm-32 ultm-color-to-gray" href="<?php echo $gplus; ?>" target="_blank" rel="nofollow" data-toggle="tooltip" data-placement="top" title="Google+ Profile"></a>

                        </div>
                    </div>
                </div>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', '<?php echo $ga; ?>', 'auto');
  ga('send', 'pageview');

</script>

</footer>             


<!-- Latest compiled and minified Bootstrap JavaScript -->
<script src="https://netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>

<!-- Bootstrap -->
<script src="<?php echo $theme_path; ?>js/bootstrap.min.js" type="text/javascript"></script>

<!-- App -->
<script src="<?php echo $theme_path; ?>js/app/app.js" type="text/javascript"></script>

 

<!-- Sign in -->
<div class="modal fade loginme" id="signin" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php echo $lang['263']; ?></h4>
			</div>
            <form method="POST" action="/?route=account&login" class="loginme-form">
			<div class="modal-body">
				<div class="alert alert-warning">
					<button type="button" class="close dismiss">&times;</button><span></span>
				</div>
                <?php if($enable_oauth){ ?>
				<div class="form-group connect-with">
					<div class="info"><?php echo $lang['267']; ?></div>
					<a href="/?route=facebook&login" class="connect facebook" title="<?php echo $lang['268']; ?>">Facebook</a>
		        	<a href="/?route=google&login" class="connect google" title="<?php echo $lang['269']; ?>">Google</a>  			        
			    </div>
                <?php } ?>
				<div class="info"><?php echo $lang['270']; ?></div>
				<div class="form-group">
					<label><?php echo $lang['271']; ?> <br />
						<input type="text" name="username" class="form-input"  style=" width: 96%;"/>
					</label>
				</div>	
				<div class="form-group">
					<label><?php echo $lang['272']; ?> <br />
						<input type="password" name="password" class="form-input"  style=" width: 96%;" />
					</label>
				</div>
			</div>
			<div class="modal-footer"> <br />
				<button type="submit" class="btn btn-primary  pull-left"><?php echo $lang['263']; ?></button>
				<div class="pull-right align-right">
				    <a style="color: #3C81DE;" href="/?route=account&forget"><?php echo $lang['273']; ?></a><br />
					<a style="color: #3C81DE;" href="/?route=account&resend"><?php echo $lang['274']; ?></a>
				</div>
			</div>
			 <input type="hidden" name="signin" value="<?php echo md5($date.$ip); ?>" />
			</form> 
		</div>
	</div>
</div>  

<!-- Sign up -->
<div class="modal fade loginme" id="signup" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php echo $lang['264']; ?></h4>
			</div>
			<form action="/?route=account&register" method="POST" class="loginme-form">
			<div class="modal-body">
				<div class="alert alert-warning">
					<button type="button" class="close dismiss">&times;</button><span></span>
				</div>
                <?php if($enable_oauth){ ?>
				<div class="form-group connect-with">
					<div class="info"><?php echo $lang['267']; ?></div>
					<a href="/?route=facebook&login" class="connect facebook" title="<?php echo $lang['268']; ?>">Facebook</a>
		        	<a href="/?route=google&login" class="connect google" title="<?php echo $lang['269']; ?>">Google</a>  		        
			    </div>
                <?php } ?>
				<div class="info"><?php echo $lang['277']; ?></div>
								<div class="form-group">
					<label><?php echo $lang['271']; ?> <br />
						<input type="text" name="username" class="form-input" />
					</label>
				</div>	
								<div class="form-group">
					<label><?php echo $lang['275']; ?> <br />
						<input type="text" name="email" class="form-input" />
					</label>
				</div>
								<div class="form-group">
					<label><?php echo $lang['276']; ?> <br />
						<input type="text" name="full" class="form-input" />
					</label>
				</div>
								<div class="form-group">
					<label><?php echo $lang['272']; ?> <br />
						<input type="password" name="password" class="form-input" />
					</label>
				</div>
				</div>
			<div class="modal-footer"> <br />
				<button type="submit" class="btn btn-primary"><?php echo $lang['264']; ?></button>	
			</div>
			 <input type="hidden" name="signup" value="<?php echo md5($date.$ip); ?>" />
			</form>
		</div>
	</div>
</div>

</body>
</html>