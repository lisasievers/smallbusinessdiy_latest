<?php $__env->startSection('section'); ?>
<link rel="stylesheet" href="<?php echo e(asset('src/css/pricing/pricing_v3.css')); ?>">
<div class="col-sm-12 ">
  
<!-- Pricing Table v3-->
      <div class="row pricing-table-v3 no-space-pricing">
       <?php if( isset($data['user_sub']) && count( $data['user_sub'] ) > 0 ): ?>
       <h2>Your Active Subscribe Package</h2>
        <div class="col-md-4 col-sm-4 col-xs-6">
           <?php foreach($data['user_sub'] as $s): ?>
          <div class="pricing-v3 pricing-v3-dark-blue<?php echo e($s->id); ?>">
            <div class="pricing-v3-head text-center">
             
              <h4><?php echo e($s->name); ?></h4>
              <h5><span>$</span><?php echo e($s->amount); ?><i></i></h5>
            </div>
            <ul class="list-unstyled pricing-v3-content">
              
              <li>Paid on<i><?php echo e($s->date_time); ?></i></li>
              <li>Expire<i><?php echo e($s->exdate_time); ?></i></li>
               <li>Status<i>Active</i></li> 
            </ul>
            <p><?php echo e($s->about); ?></p>
            
          </div>
          <?php endforeach; ?>
        </div>
       <?php else: ?>
       <h2>Find a plan that's right for you</h2>
        <?php foreach($data['packages'] as $p): ?>
        <div class="col-md-4 col-sm-4 col-xs-6">
          <div class="pricing-v3 pricing-v3-dark-blue<?php echo e($p['id']); ?>">
            <div class="pricing-v3-head text-center">
              <h4><?php echo e($p['name']); ?></h4>
              <h5><span>$</span><?php echo e($p['price']); ?><i>P/Month</i></h5>
            </div>
            <ul class="list-unstyled pricing-v3-content">
              <li>Validity<i><?php echo e($p['validity']); ?></i></li>
              <li>Months<i>3</i></li>
             
           
            </ul>
            <p><?php echo e($p['about']); ?></p>
            <div class="pricing-v3-footer text-center">
              <a href="<?php echo e(route('user-reports-addition',['sub_id' => $p['id']])); ?>" class="btn-u<?php echo e($p['id']); ?> btn-u-dark-blue btn-block">Purchase Now</a>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
        
        <?php endif; ?>
      </div><!--/row-->
  
</div>
   </div>
   <div class="clear"></div>       
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>