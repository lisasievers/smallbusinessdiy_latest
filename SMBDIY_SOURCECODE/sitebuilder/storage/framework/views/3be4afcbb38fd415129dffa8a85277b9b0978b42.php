<?php $__env->startSection('section'); ?>
<style>

</style>
<div class="col-sm-12 ">
  <h2>Success!</h2>   
  <?php
//if(isset($data['sess'])){ print_r($data); }
  ?>
  
        <?php if( Session::has('success') ): ?>
        <div class="alert alert-success" style="width:97%; margin-left: auto; margin-right: auto">
            <button data-dismiss="alert" class="close fui-cross" type="button"></button>
            <h4>All good!</h4>
            <p>
                <?php echo e(Session::get('success')); ?> Thank you for submitting, your website will activated within 48 hours!
            </p>
        </div>
        <?php endif; ?>
</div>
   </div>
   <div class="clear"></div>       
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>