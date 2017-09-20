<?php $__env->startSection('section'); ?>

<!-- if( isset($sites) && count( $sites ) > 0 ) -->
 <?php if( isset($sub_state) && count( $sub_state ) > 0 ): ?>
<div class="col-sm-12 ">
	 <h2>Paid Report Tools</h2>
	<div class="col-md-4">
		<div class="tools-box">
		<a href="<?php echo e($tools['sitedoctor']); ?>" class="btn btn-primary btn-embossed btn-block">
<img src="<?php echo e(URL::to('src/images/icons/responsive.svg')); ?>" />
Site Responsive
		</a>
	</div>
	</div>
     <div class="col-md-4">
     	<div class="tools-box">
     	<a href="<?php echo e($tools['sitespy']); ?>" class="btn btn-primary btn-embossed btn-block">
<img src="<?php echo e(URL::to('src/images/icons/flask.svg')); ?>" />
Site Analysis
		</a>
     </div>
	</div>
      <div class="col-md-4">
     	<div class="tools-box">
     	<a href="<?php echo e($tools['website-review']); ?>" class="btn btn-primary btn-embossed btn-block">
<img src="<?php echo e(URL::to('src/images/icons/search.svg')); ?>" />
Review Tool
		</a>
     </div>
	</div>
</div>
<div class="clearfix" style="margin:10px 0;"></div>
<div class="col-sm-12 ">
	 
	<div class="col-md-4">
		<div class="tools-box">
		<a href="<?php echo e($tools['atoz']); ?>" class="btn btn-primary btn-embossed btn-block" target="_blank">
<img src="<?php echo e(URL::to('src/images/icons/rocket.svg')); ?>" />
SEO Tool
		</a>
	</div>
	</div>
     <div class="col-md-4">
     	<div class="tools-box">
     	<a href="<?php echo e($tools['sitedoctor_compare']); ?>" class="btn btn-primary btn-embossed btn-block">
<img src="<?php echo e(URL::to('src/images/icons/pig.svg')); ?>" />
Competitor Tool
		</a>
     </div>
	</div>
     
</div>
<?php else: ?>

<!--<h2>You are yet to add websites! </h2><p>We have below tools for improve your website. Please active a subscription <a href="<?php echo e(route('pricing')); ?>"> here</a> and continue.</p>-->

     <h2>Our Paid Report Tools</h2>
    <div class="col-md-4">
        <div class="tools-box">
        <a href="<?php echo e(route('pricing')); ?>" class="btn btn-primary btn-embossed btn-block">
<img src="<?php echo e(URL::to('src/images/icons/responsive.svg')); ?>" />
Site Responsive
        </a>
    </div>
    </div>
     <div class="col-md-4">
        <div class="tools-box">
        <a href="<?php echo e(route('pricing')); ?>" class="btn btn-primary btn-embossed btn-block">
<img src="<?php echo e(URL::to('src/images/icons/flask.svg')); ?>" />
Site Analysis
        </a>
     </div>
    </div>
      <div class="col-md-4">
        <div class="tools-box">
        <a href="<?php echo e(route('pricing')); ?>" class="btn btn-primary btn-embossed btn-block">
<img src="<?php echo e(URL::to('src/images/icons/search.svg')); ?>" />
Review Tool
        </a>
     </div>
    </div>

<div class="clearfix" style="margin:10px 0;"></div>
     
    <div class="col-md-4">
        <div class="tools-box">
        <a href="<?php echo e(route('pricing')); ?>" class="btn btn-primary btn-embossed btn-block">
<img src="<?php echo e(URL::to('src/images/icons/rocket.svg')); ?>" />
SEO Tool
        </a>
    </div>
    </div>
     <div class="col-md-4">
        <div class="tools-box">
        <a href="<?php echo e(route('pricing')); ?>" class="btn btn-primary btn-embossed btn-block">
<img src="<?php echo e(URL::to('src/images/icons/pig.svg')); ?>" />
Competitor Tool
        </a>
     </div>
     
</div>

<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>