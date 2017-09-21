<?php $__env->startSection('section'); ?>
<link href="<?php echo e(URL::to('src/css/bootstrap-fileupload.css')); ?>" rel="stylesheet">
<script src="<?php echo e(URL::to('src/js/bootstrap-fileupload.js')); ?>"></script>
<style>
form#ProSettings .input-group {
    margin-bottom: 20px;
    width: 100%;
}
</style>
<div class="col-sm-12 ">
	<h2>My Account Settings</h2>
            <?php if( Session::has('message') ): ?>
            <div class="alert alert-success">
                <button type="button" class="close fui-cross" data-dismiss="alert"></button>
                <?php echo e(Session::get('message')); ?>

            </div>
            <?php endif; ?>
	<div class="col-md-6">
				<form role="form" id="ProSettings" action="<?php echo e(route('user.settings')); ?>" method="post" enctype="multipart/form-data">
					
                <div class="input-group <?php echo e($errors->has('first_name') ? 'has-error' : ''); ?>">
                    First Name
                    <input type="text" class="form-control" id="first_name" name="first_name"  placeholder="First name *" value="<?php echo e($user->first_name); ?>">
                </div>
                
                <div class="input-group <?php echo e($errors->has('last_name') ? 'has-error' : ''); ?>">
                   Last Name 
                    <input type="text" class="form-control" id="last_name" name="last_name"  placeholder="Last name *" value="<?php echo e($user->last_name); ?>">
                </div>

                <div class="input-group <?php echo e($errors->has('email') ? 'has-error' : ''); ?>">
                    Email
                    <input type="email" class="form-control" id="email" name="email"  placeholder="Email *" value="<?php echo e($user->email); ?>" readonly >
                </div>
                <div class="input-group <?php echo e($errors->has('city') ? 'has-error' : ''); ?>">
                	Email Notification
                    <select name="email_notification">
                    <option value="on"  <?php if($user->email_notification=='on'){echo 'selected=selected'; } ?>>ON</option>
                    <option value="off"  <?php if($user->email_notification=='off'){echo 'selected=selected'; } ?>>OFF</option>
                	</select>
                </div>
                <!--<div class="form-group">
		            <label class="control-label">Profile Photo</label>
                    [ Width & Height Less than 100 pixel ]
		            <div class="controls">
		                                <input type="file" id="profile_icon" name="profile_icon">
		                              
		                            </div>
		         </div>-->
                
                <input type="hidden" name="_token" value="<?php echo e(Session::token()); ?>">
               <!-- <hr>
                <div class="input-group <?php echo e($errors->has('old_password') ? 'has-error' : ''); ?>">
                   Old Password 
                    <input type="password" class="form-control" id="old_password" name="old_password"  placeholder="Old Password *">
                </div>
            -->
         
                <div class="control-group">
                          <label class="control-label" for="product_name">Profile Logo<Mandatory></Mandatory> [ Prefer Size: 100 x 80 ]</label>
                          <div class="controls">
                          <input type="hidden" class="addlogo" name="addlogo" />
                          <?php if($user->profile_icon!=''): ?>
                          <p class="promsg">Click 'Close (X)' to change profile picture. Otherwise leave it.</p>
                           <div class="alert alert-default alert-dismissable " style="background:#ddd">
                            <a href="#" class="close noaddlogo" data-dismiss="alert" aria-label="close">×</a>
                            <img id="companylogo2c" alt="" width="84" src="<?php echo e(url('/')); ?>/src/uploads/users/<?php echo e($user->profile_icon); ?>" />
                          </div>
                          <?php endif; ?>
                          <div>
                   </div>
                           <div class="fileupload fileupload-new" data-provides="fileupload">
                                    <div class="input-append">
                                        <div class="uneditable-input span3"><i class="icon-file fileupload-exists"></i> <span class="fileupload-preview"></span></div><span class="btn btn-info btn-file"><span class="fileupload-new">Select file</span><span class="fileupload-exists">Change</span><input type="file" name="profile_icon" id="profile_icon" /></span><a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>
                                    </div>
                                    
                                </div>
                          </div>
                </div>
                <div class="input-group <?php echo e($errors->has('password') ? 'has-error' : ''); ?>">
                    New Password
                    <input type="password" class="form-control" id="password" name="password"  placeholder="New Password *"  >
                </div>
                <div class="signbtn-group">
                    <button type="submit" class="btn btn-primary btn-block btn-embossed" id="choosesubmit" > Update </span></button>
                 </div>  
                <!-- <div class="moresteps">You're on your way to creating an amazing website! Just 3 more steps to go!</div>-->
             </div>
              

            </form>
	</div>
    
</div>
<script type="text/javascript">
$(document).ready(function() {
    var exists='<?php echo $user->profile_icon; ?>';
    if(exists!=''){$('.fileupload').hide();}else{$('.fileupload').show();}
  $(".noaddlogo").click(function(){
        $( ".addlogo" ).attr("value","yes");
        $('.fileupload').show();
      });
  $('#profile_icon').change(function(){
        var isname=$(this).attr('name');
        var fname=$('.fileupload-preview').html();
        if(isname.length > 0){
        $( ".addlogo" ).attr("value","");
        
        }
        else
        {
            $( ".addlogo" ).attr("value","yes");
        }
            
        
    }); 
});


</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>