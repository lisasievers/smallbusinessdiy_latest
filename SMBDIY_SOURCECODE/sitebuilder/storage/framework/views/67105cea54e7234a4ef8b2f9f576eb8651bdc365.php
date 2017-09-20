<?php $__env->startSection('section'); ?>
<div class="col-sm-12 ">

        <?php echo $__env->make('layouts.partials.stripe_form_doitforme', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?> 
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>