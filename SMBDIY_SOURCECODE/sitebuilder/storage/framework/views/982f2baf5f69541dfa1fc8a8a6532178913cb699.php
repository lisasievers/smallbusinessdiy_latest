<?php $__env->startSection('content'); ?>
<style>
.loader{display: block;}
.loader:before{
  display: block;
  position: absolute;
  content: "";
  left: -200px;
  width: 200px;
  height: 4px;
  background-color: #2980b9;
  animation: loading 2s linear infinite;
}

@keyframes  loading {
    from {left: -200px; width: 30%;}
    50% {width: 30%;}
    70% {width: 70%;}
    80% { left: 50%;}
    95% {left: 120%;}
    to {left: 100%;}
}
</style>
<div class="container">

    <div class="row needweb">
        <!--
<div class="col-md-5">
<div class="pricing ">
<div class="pricing-head">
<h3>NEED A WEBSITE</h3>
<h4><i>$</i><?php echo e($cost_month); ?><span> Per Month</span><span> <span class="tcolor">(or)</span> </span><i>$</i><?php echo e($cost_year); ?><span> Per Annual</span></h4>
<div class="dived-line"></div>
<h5><span>+</span><i>$</i><?php echo e($setup_cost); ?><span> Setup Costs</span></h5>
</div>
<ul class="pricing-content list-unstyled">
<li><i class="fa fa-gift"></i> Need a Makeover ?</li>
<div class="dived-line"></div>
<li><i class="fa fa-desktop"></i> New Responsive Website ?</li>
<div class="dived-line"></div>
<li><i class="fa fa-credit-card"></i> NO CREDIT CARD NEEDED! </li>
<div class="dived-line"></div>
<li><i class="fa fa-external-link"></i><a href="#" target="_blank"> Terms & Conditions</a> </li>
<div class="dived-line"></div>
</ul>
<div class="pricing-footer">
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cloud Storage magna psum olor .</p></div>
</div>
</div>
<div class="col-md-2">
<div class="new_site_ribbon">SIGN UP NOW</div>
</div>-->
   <div class="col-md-5">
    
    
           
            <div class="regform">
            <?php if($errors->any()): ?>
            <?php foreach($errors->all() as $error): ?>
                <?php if($error =='The email has already been taken.' ): ?>
            <div class="alert alert-danger">
                <button type="button" class="close fui-cross" data-dismiss="alert"></button>
                Sorry! This email already registered with us!! 
                 </div>
                <?php endif; ?>            
            <?php endforeach; ?>
            
            <?php else: ?>
             <!--<div class="loader"></div>       -->
            <?php endif; ?>

            <?php if( Session::has('message') ): ?>
            <div class="alert alert-success">
                <button type="button" class="close fui-cross" data-dismiss="alert"></button>
                <?php echo e(Session::get('message')); ?>

            </div>
            <?php endif; ?>
                <h6>Register Here - Need a website !!</h6>
                <div class="moresteps">You're on your way to creating an amazing website! Just 3 more steps to go!</div>
            <form role="form" id="SignUp" action="<?php echo e(route('signup')); ?>" method="post">

                <div class="input-group <?php echo e($errors->has('first_name') ? 'has-error' : ''); ?>">
                    
                    <input type="text" class="form-control" id="first_name" name="first_name" tabindex="1" placeholder="First name *" value="<?php echo e(Request::old('first_name')); ?>">
                </div>

                <div class="input-group <?php echo e($errors->has('last_name') ? 'has-error' : ''); ?>">
                    
                    <input type="text" class="form-control" id="last_name" name="last_name" tabindex="2" placeholder="Last name *" value="<?php echo e(Request::old('last_name')); ?>">
                </div>

                <div class="input-group <?php echo e($errors->has('email') ? 'has-error' : ''); ?>">
                    
                    <input type="email" class="form-control" id="email" name="email" tabindex="3" placeholder="Email *"  >
                </div>
                <!--<div class="input-group <?php echo e($errors->has('city') ? 'has-error' : ''); ?>">
                    <span class="input-group-btn">
                        <button class="btn"><span class="fui-arrow-right"></span></button>
                    </span>
                    <input type="text" class="form-control" id="city" name="city" tabindex="4" placeholder="City *" value="<?php echo e(Request::old('city')); ?>">
                </div>
                <div class="input-group <?php echo e($errors->has('state') ? 'has-error' : ''); ?>">
                    <span class="input-group-btn">
                        <button class="btn"><span class="fui-arrow-right"></span></button>
                    </span>
                    <input type="text" class="form-control" id="state" name="state" tabindex="5" placeholder="State *" value="<?php echo e(Request::old('state')); ?>">
                </div>-->
                
                <div class="input-group <?php echo e($errors->has('password') ? 'has-error' : ''); ?>">
                    
                    <input type="password" class="form-control" id="password" name="password" tabindex="4" placeholder="Password *" >
                </div>
                <!--
                <div class="input-group <?php echo e($errors->has('password_confirmation') ? 'has-error' : ''); ?>">
                    <span class="input-group-btn">
                        <button class="btn"><span class="fui-arrow-right"></span></button>
                    </span>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" tabindex="5" placeholder="Confirm password *" value="">
                </div>
                -->
               
                <!--
                <div class="input-group">
                    <span class="input-group-btn">
                        <button class="btn"><span class="fui-arrow-right"></span></button>
                    </span>
                    <input type="text" class="form-control" id="captcha" name="CaptchaCode" tabindex="6" placeholder="Enter the letters from the image *">
                </div>-->
                <input type="hidden" name="_token" value="<?php echo e(Session::token()); ?>">
                <input type="hidden" name="userfrom" value="needweb">
                <div class="signbtn-group">
                    <button type="submit" class="btn btn-primary btn-block btn-embossed" id="choosesubmit" tabindex="5"> Register </span></button>
                 </div>  
                <!-- <div class="moresteps">You're on your way to creating an amazing website! Just 3 more steps to go!</div>-->
             </div>
               <!-- <hr class="dashed light">-->

                <div class="text-center">
                   Already registered? <a href="<?php echo e(route('home')); ?>" style="font-size: 15px"> Login</a>
                <!--<img src="<?php echo e(URL::to('src/images/test.jpg')); ?>" /> -->
                </div>

            </form>

        </div><!-- /.col -->
<div class="col-md-7">
<div id="tv_container">
    <video width="555" height="309" controls>
        <source src="https://storage.googleapis.com/assets-sitebuilder/video/sitebuilder-lite.mp4" type="video/mp4" />
    </video>
</div>
   </div>
   
    </div><!-- /.row -->
    <div class="clear"></div>
    <div class="row">
<div class="col-md-12">
<div class="pricing ">
<div class="pricing-head">
<h3>NEED A WEBSITE</h3>
<h4><i>$</i><?php echo e($cost_month); ?><span> Per Month</span><span> <span class="tcolor">(or)</span> </span><i>$</i><?php echo e($cost_year); ?><span> Per Annual</span></h4>
<div class="dived-line"></div>
<h5><span>+</span><i>$</i><?php echo e($setup_cost); ?><span> Setup Costs</span></h5>
</div>
<div class="pricing-content list-unstyled">
<div class="col-md-3"><i class="fa fa-gift"></i> Need a Makeover ?</div>
<!--<div class="dived-line2"></div>-->
<div class="col-md-3"><i class="fa fa-desktop"></i> New Responsive Website ?</div>
<!--<div class="dived-line2"></div>-->
<div class="col-md-3"><i class="fa fa-credit-card"></i> NO CREDIT CARD NEEDED! </div>
<!--<div class="dived-line2"></div>-->
<div class="col-md-3"><i class="fa fa-external-link"></i><a href="#" target="_blank"> Terms & Conditions</a> </div>
<!--<div class="dived-line2"></div>-->
</div>
<div class="pricing-footer">
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cloud Storage magna psum olor .</p></div>
</div>
</div>
</div>
</div><!-- /.container -->
<script>
$(document).ready(function () {
$("#choosesubmit").click(function() {
   // alert('ss');
  $( "#SignUp" ).submit();
  $('.loader').show();
  //toastr.info("You're on your way to creating an amazing website! Just 3 more steps to go!")

});

});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.login', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>