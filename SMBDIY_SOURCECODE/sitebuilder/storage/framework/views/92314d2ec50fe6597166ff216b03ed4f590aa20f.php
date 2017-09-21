<?php $__env->startSection('section'); ?>
<style>
form#ProSettings .input-group {
    margin-bottom: 20px;
    
}
</style>
<div class="col-sm-12 ">
	<h2>Add a New Website</h2>
				<form role="form" id="ProSettings" action="<?php echo e(route('user.addsites')); ?>" method="post" enctype="multipart/form-data">
					
                <div class="input-group <?php echo e($errors->has('site_name') ? 'has-error' : ''); ?>">
                    <b>Enter Domain Name:</b><br>
                    <p>Please type without http://, https:// and www. eg:( google.com )</p>
                    <input type="text" class="form-control" id="site_name" name="site_name"  placeholder="Website name *" />
                </div>
                

                
                <input type="hidden" name="_token" value="<?php echo e(Session::token()); ?>">
               
                <div class="signbtn-group input-group">
                    <button type="submit" class="btn btn-primary btn-block btn-embossed" id="choosesubmit" > Submit </span></button>
                 </div>  
                

            </form>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>