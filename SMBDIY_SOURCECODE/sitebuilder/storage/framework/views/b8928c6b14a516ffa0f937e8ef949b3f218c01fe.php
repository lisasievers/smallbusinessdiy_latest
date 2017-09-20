<?php $__env->startSection('title'); ?>
Login | Forgot Password
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<style>
p{color:#000;}
body.login .btn > span{margin-left: -5px;}
</style>
<div class="container">

    <div class="row">
        <div class="col-md-12">
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
        </div><!-- /.col -->
    </div><!-- /.row -->

    <div class="row">

        <div class="col-md-4 col-md-offset-4">

            

            <p>Please enter your Email so we can send you an email to reset your password.</p>

            <?php if( isset($message) && $message != '' ):?>
                <div class="alert alert-success">
                    <button data-dismiss="alert" class="close fui-cross" type="button"></button>
                    <?php echo $message;?>
                </div>
            <?php endif;?>

            <form role="form" action="<?php echo e(route('recover.password')); ?>" method="post">

                <div class="input-group">
                    <span class="input-group-btn">
                        <button class="btn"><span class="fui-user"></span></button>
                    </span>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Your email address" required />
                </div>
                <input type="hidden" name="_token" value="<?php echo e(Session::token()); ?>">
                <button type="submit" class="btn btn-primary btn-block">Submit </button>

                <hr class="dashed light">

                <div class="text-center">
                    <a href="<?php echo e(route('home')); ?>" style="font-size: 15px"><span class="fui-arrow-left"></span> back to login</a>
                </div>

            </form>

        </div><!-- /.col -->

    </div><!-- /.row -->

</div><!-- /.container -->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.login', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>