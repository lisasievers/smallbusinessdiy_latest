<?php $__env->startSection('section'); ?>
<div class="col-sm-12 ">
<?php
$sess=$data['sess'];
$url=$sess['url'];
//dd($sess);
if($sess['whodo']=='iwill'){

  header("refresh:4;url=".$url ); 
}
else
{

  header("refresh:1;url=".$url );  
}

?>
<h3 class="stepscount">Great job ! You are Almost done</h3>
        <?php echo $__env->make('layouts.partials.progress', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?> 
        <form role="form" id="sign_form" method="post" action="" enctype="multipart/form-data">
        <div class=" setup-content3" id="step-3">
      <div class="col-xss-6">
        
        <!--  <h3 class="dod diy-color"> Do it for me</h3>-->
          <div class="sitebuild">
            <h4>You can now start building your website.</h4>
            <img class="sitbuild-screen" src="<?php echo e(url('src/images/sample-img.png')); ?>" alt="sitebuilder" />
          </div>

        </div>
      
    </div>

  </form>
  
</div>
   </div>
   <div class="clear"></div>       
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>