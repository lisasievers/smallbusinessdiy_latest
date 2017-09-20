
<script >

	$("document").ready(function(){	
		$("a.share_button").click(function(e) {
	    var width = window.innerWidth * 0.66 ;
	    var height = width * window.innerHeight / window.innerWidth ;
	    window.open(this.href , 'newwindow', 'width=' + width + ', height=' + height + ', top=' + ((window.innerHeight - height) / 2) + ', left=' + ((window.innerWidth - width) / 2));
		e.preventDefault();
	
	});
	
	});

</script>

<?php 	$share_current_url=current_url();     /***This will be current URL ***/ ?>




<!-- Facebook -->
<a class="share_button" href="http://www.facebook.com/sharer.php?u=<?php echo $share_current_url;  ?>" target="_blank">
<img src="<?php echo base_url("assets/images/button");?>/facebook.png" alt="Facebook" />
</a>

<!-- Twitter -->
<a class="share_button" href="https://twitter.com/share?url=<?php echo $share_current_url;  ?>" target="_blank">
<img src="<?php echo base_url("assets/images/button");?>/twitter.png" alt="Twitter" />
</a>

<!-- Google+ -->
<a class="share_button" href="https://plus.google.com/share?url=<?php echo $share_current_url;  ?>" target="_blank">
<img src="<?php echo base_url("assets/images/button");?>/google.png" alt="Google" />
</a>

<!-- LinkedIn -->
<a class="share_button" href="http://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo $share_current_url;  ?>" target="_blank">
<img src="<?php echo base_url("assets/images/button");?>/linkedin.png" alt="LinkedIn" />
</a>






