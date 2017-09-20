
<!-- country list -->
<script src="<?php echo base_url();?>assets/site/plugins/ms-dropdown/js/jquery-1.9.0.min.js"></script>
<script src="<?php echo base_url();?>assets/site/plugins/ms-dropdown/js/jquery.dd.js"></script>
<script>
    	var $j= jQuery.noConflict();
</script> 
<!-- Jquery and Bootstap core js files -->
<script type="text/javascript" src="<?php echo base_url();?>assets/site/plugins/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/site/bootstrap/js/bootstrap.min.js"></script>
<!-- Modernizr javascript -->
<script type="text/javascript" src="<?php echo base_url();?>assets/site/plugins/modernizr.js"></script>
<!-- Isotope javascript -->
<script type="text/javascript" src="<?php echo base_url();?>assets/site/plugins/isotope/isotope.pkgd.min.js"></script>

<script type="text/javascript" src="<?php echo base_url();?>assets/site/plugins/jquery.appear.js"></script>
<!-- Initialization of Plugins -->
<script type="text/javascript" src="<?php echo base_url();?>assets/site/js/template.js"></script>
<!-- Custom Scripts -->
<script type="text/javascript" src="<?php echo base_url();?>assets/site/js/speedometer.js"></script>
<!-- SpeedMeter Scripts -->
<script type="text/javascript" src="<?php echo base_url();?>assets/site/js/custom.js"></script>

<script type="text/javascript">

	$j(document).ready(function() {
		
		$j("#countries").msDropdown();
		$j("#countries2").msDropdown();
		$j("#countries,#countries2").change(function(){
			var language=$(this).val();
			$.ajax({
				url: '<?php echo site_url("home/language_changer");?>',
				type: 'POST',
				data: {language:language},
				success:function(response){
					location.reload(); 
				}
			})

		});
	});
</script>