<div class="signupstage">
<?php $__env->startSection('page_heading',''); ?>
</div>
<?php $__env->startSection('section'); ?>
<style>
form#ProSettings .input-group {
    margin-bottom: 20px;
    width: 100%;
}
</style>
<div class="col-sm-12 ">
	<h2>My Account Settings</h2>
	<div class="col-md-6">
				<form role="form" id="ProSettings" action="<?php echo e(route('user.settings')); ?>" method="post" enctype="multipart/form-data">
					
                <div class="input-group <?php echo e($errors->has('first_name') ? 'has-error' : ''); ?>">
                    First Name
                    <input type="text" class="form-control" id="first_name" name="first_name" tabindex="1" placeholder="First name *" value="<?php echo e($user->first_name); ?>">
                </div>
                
                <div class="input-group <?php echo e($errors->has('last_name') ? 'has-error' : ''); ?>">
                   Last Name 
                    <input type="text" class="form-control" id="last_name" name="last_name" tabindex="2" placeholder="Last name *" value="<?php echo e($user->last_name); ?>">
                </div>

                <div class="input-group <?php echo e($errors->has('email') ? 'has-error' : ''); ?>">
                    Email
                    <input type="email" class="form-control" id="email" name="email" tabindex="3" placeholder="Email *" value="<?php echo e($user->email); ?>" readonly >
                </div>
                <div class="input-group <?php echo e($errors->has('city') ? 'has-error' : ''); ?>">
                	Email Notification
                    <select name="email_notification">
                    <option value="on"  <?php if($user->email_notification=='on'){echo 'selected=selected'; } ?>>ON</option>
                    <option value="off"  <?php if($user->email_notification=='off'){echo 'selected=selected'; } ?>>OFF</option>
                	</select>
                </div>
                <div class="form-group">
		            <label class="control-label">Profile Photo</label>
		            <div class="controls">
		                                <input type="file" id="sliderFile" name="sliderFile">
		                              
		                            </div>
		         </div>
                
                <input type="hidden" name="_token" value="<?php echo e(Session::token()); ?>">
                
                <div class="signbtn-group">
                    <button type="submit" class="btn btn-primary btn-block btn-embossed" id="choosesubmit" tabindex="5"> Update </span></button>
                 </div>  
                <!-- <div class="moresteps">You're on your way to creating an amazing website! Just 3 more steps to go!</div>-->
             </div>
              

            </form>
	</div>
    
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>