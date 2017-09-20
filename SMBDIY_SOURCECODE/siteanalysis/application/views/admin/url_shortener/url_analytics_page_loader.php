<div class="row" style="margin-top:30px;">
	<div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3 well">
		<form method="POST" action="<?php echo base_url('url_shortener/url_analytics_result'); ?>">
		  <div class="form-group">
		    <label for="short_url"><?php echo $this->lang->line('enter your short url here'); ?></label>
		    <input type="text" required class="form-control" id="short_url" name="short_url" placeholder="<?php echo $this->lang->line('short url'); ?>">
		  </div>
		  <div class="form-group text-center">
			  <button type="submit" class="btn btn-success"><?php echo $this->lang->line('submit'); ?></button>
		  </div>
		</form>
	</div>
</div>