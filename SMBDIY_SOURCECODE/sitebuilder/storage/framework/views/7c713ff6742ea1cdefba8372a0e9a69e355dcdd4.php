<?php $__env->startSection('title'); ?>
Login | SMBDIY
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4 loginbox">
            
            <?php if( Session::has('success') ): ?>
            <div class="alert alert-success">
                <button type="button" class="close fui-cross" data-dismiss="alert"></button>
                <?php echo e(Session::get('success')); ?>

            </div>
            <?php endif; ?>
            <?php if( Session::has('error') ): ?>
            <div class="alert alert-error">
                <button type="button" class="close fui-cross" data-dismiss="alert"></button>
                <?php echo e(Session::get('error')); ?>

            </div>
            <?php endif; ?>
            <?php if( Session::has('message') ): ?>
            <div class="alert alert-error">
                <button type="button" class="close fui-cross" data-dismiss="alert"></button>
                <?php echo e(Session::get('message')); ?>

            </div>
            <?php endif; ?>
            <form role="form" action="<?php echo e(route('signin')); ?>" method="post">
                <div class="input-group <?php echo e($errors->has('email') ? 'has-error' : ''); ?>">
                    
                    <input type="email" class="form-control" id="email" name="email" tabindex="1" autofocus placeholder="Your email address *" />
                </div>
                <div class="input-group">
                   
                    <input type="password" class="form-control" id="password" name="password" tabindex="2" placeholder="Your password *" />
                </div>
                <!--<label class="checkbox margin-bottom-20" for="checkbox1">
                    <input type="checkbox" value="1" id="remember" name="remember" tabindex="3" data-toggle="checkbox">
                    Remember me
                </label>-->
                <input type="hidden" name="_token" value="<?php echo e(Session::token()); ?>">
                <button type="submit" class="btn btn-primary btn-block btn-embossed" tabindex="3">Log me in<span class="fui-arrow-right"></span></button>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <a href="<?php echo e(route('forgot.password')); ?>">Lost your password?</a>
                    </div>
                </div><!-- /.row -->
            </form>
            <div class="divider">
                <span>OR</span>
            </div>
            <h2 class="text-center margin-bottom-25">
                <a class="btn-facebook" href="facebook"> Login with Facebook</a>
            </h2>
            <div class="text-center">Don't have account? <a href="<?php echo e(route('signup')); ?>" class="">Signup Now </a></div>
        </div><!-- /.col -->
    </div><!-- /.row -->
</div><!-- /.container -->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.login', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>