<div class="space"></div>
<div class="space" id="contact_space"></div>
<div class="space"></div>
<div class="space"></div>
<div class="space"></div>

<h1 class="title text-center" id="contact"><?php echo $this->lang->line("contact");?></h1>
<p class="slogan text-center"><p class="text-center slogan"><?php echo $this->lang->line('feel free to contact us'); ?></p></p>
<div class="space"></div>
<!-- footer start -->			
<footer id="footer">
	<!-- .footer start -->
	<!-- ================ -->
	<div class="footer section">
		<div class="space"></div>
		<div class="container">
			<div class="row">
				<div class="col-sm-6">
					<div class="footer-content">
						<ul class="list-icons">
							<li><i class="fa fa-hand-o-right pr-10"></i> <?php echo $this->config->item("product_name"); ?></li>
							<li><i class="fa fa-building pr-10"></i> <?php echo $this->config->item("institute_address1"); ?></li>
							<li><i class="fa fa-map-marker pr-10"></i> <?php echo $this->config->item("institute_address2"); ?></li>
							<li><i class="fa fa-phone pr-10"></i> <?php echo $this->config->item("institute_mobile"); ?></li>
							<li><i class="fa fa-envelope-o pr-10"></i> <?php echo $this->config->item("institute_email"); ?></li>
						</ul>
					</div>
				</div>
				<div class="col-sm-6">
					<div>
						<?php 
						if($this->session->userdata('mail_sent') == 1) {
							echo "<div class='alert alert-success text-center'>".$this->lang->line("we have received your email. we will contact you through email as soon as possible")."</div>";
							$this->session->unset_userdata('mail_sent');
						}
						?>
					</div>

					<div class="footer-content">
						<form role="form" method="post" id="footer-form" action="<?php echo site_url("home/email_contact"); ?>">
							<div class="form-group has-feedback">
								<label class="sr-only" for="email"><?php echo $this->lang->line("email");?>* </label>
								<input type="email" class="form-control" required id="email" <?php echo set_value("email"); ?> placeholder="<?php echo $this->lang->line("email");?>" name="email">
								<i class="fa fa-envelope form-control-feedback"></i>
								<span class="red"><?php echo form_error("email") ?></span>
							</div>
							<div class="form-group has-feedback">
								<label class="sr-only" for="subject"><?php echo $this->lang->line("message subject");?>* </label>
								<input type="text" class="form-control" required id="subject" <?php echo set_value("subject"); ?> placeholder="<?php echo $this->lang->line("message subject");?>" name="subject">
								<i class="fa fa-tag form-control-feedback"></i>
								<span class="red"><?php echo form_error("subject") ?></span>
							</div>

							<div class="form-group has-feedback">
								<label class="sr-only" for="message"><?php echo $this->lang->line("message");?>* </label>
								<textarea class="form-control" rows="3" required id="message" <?php echo set_value("message"); ?> placeholder="<?php echo $this->lang->line("message");?>" name="message"></textarea>
								<i class="fa fa-pencil form-control-feedback"></i>
								<span class="red"><?php echo form_error("message") ?></span>
							</div>

							<div class="form-group has-feedback">
								<label  class="sr-only" for="captcha"><?php echo $this->lang->line("captcha");?> *  </label>
								<input type="number" class="form-control" step="1" required id="captcha" <?php echo set_value("captcha"); ?> placeholder="<?php echo $contact_num1. "+". $contact_num2." = ?"; ?>" name="captcha">
								<i class="fa fa-android form-control-feedback"></i>
								<span class="red">
									<?php 
									if(form_error('captcha')) 
										echo form_error('captcha'); 
									else  
									{ 
										echo $this->session->userdata("contact_captcha_error"); 
										$this->session->unset_userdata("contact_captcha_error"); 
									} 
									?>
								</span>
							</div>

							<input type="submit" value="<?php echo $this->lang->line("send email");?>" class="btn btn-primary">
						</form>
					</div>
				</div>
			</div>
		</div>					
		<div class="space"></div>
	</div>
	<!-- .footer end -->
</footer>