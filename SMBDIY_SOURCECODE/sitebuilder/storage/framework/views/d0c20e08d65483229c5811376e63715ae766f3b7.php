<?php $__env->startSection('section'); ?>
 <link href="<?php echo e(asset('src/css/style_site_list.css')); ?>" rel="stylesheet" type="text/css">
 <style>
 .cats-section{margin-bottom: 10px;float: left;width: 100%;}
 </style>
        <!--<div class="row">
            <h2></h2>
            <div class="col-md-2">Select Category</div>
            <div class="col-md-4">
            <select name="sitecats" class="form-control" id="sitecats"> 
               <option value="all">All</option> 
             <?php foreach( $cats as $ca ): ?> 
             <option value="<?php echo e($ca['id']); ?>" <?php //if(Request::segment(2)==$ca['id']){ echo 'selected=selected'; } ?> ><?php echo e($ca['name']); ?></option>
             <?php endforeach; ?>
         </select>
     </div>
         </div>  --> 
        <div class="row">
            
        <?php if( isset($sites) && count( $sites ) > 0 ): ?>
        <div class="col-md-12">
            <form role="form" id="templ_form" method="get" action="<?php echo e(route('webdataform')); ?>" >
                  <h2>Please choose a template and <input type="submit" class="btn btn-success" value="Click" /> to continue..</h2>
        <div class="cats-section">
        <div class="col-md-2">Select Category</div>
            <div class="col-md-4">
            <select name="sitecats" class="form-control" id="sitecats"> 
               <option value="0">All</option> 
             <?php foreach( $cats as $ca ): ?> 
             <option value="<?php echo e($ca['id']); ?>" <?php if(Request::segment(2)==$ca['id']){ echo 'selected=selected'; } ?> ><?php echo e($ca['name']); ?></option>
             <?php endforeach; ?>
         </select>
        </div>
    </div><!-- categroy selection -->
      <div class="clear"></div>
           
            <div class="col-md-cover sites" id="sitesss">
                 <input type="hidden" name="_token" value="<?php echo e(Session::token()); ?>">

            <?php //print_r($sites); ?>
            <div class="masonry-3 sites" id="sites">
                <?php foreach( $sites as $site ): ?>
                <?php //print_r($site); ?>
                <div class="site" data-name="<?php echo e($site['siteData']['user']['first_name']); ?> <?php echo e($site['siteData']['user']['last_name']); ?>" data-pages="<?php echo e($site['nrOfPages']); ?>" data-created="<?php echo e(date('Y-m-d', strtotime($site['siteData']['created_at']))); ?>" data-update="<?php echo e(date('Y-m-d', strtotime($site['siteData']['updated_at']))); ?>" id="site_<?php echo e($site['siteData']['id']); ?>">
                    <div class="window">
                        <div class="top">
                            <div class="buttons clearfix">
                                <span class="left red"></span>
                                <span class="left yellow"></span>
                                <span class="left green"></span>
                            </div>
                            <b><?php echo e($site['siteData']['site_name']); ?></b>
                        </div><!-- /.top -->

                        <div class="viewport">
                            <?php if( $site['lastFrame'] != '' ): ?>
                            <iframe src="<?php echo e(route('getframe', ['frame_id' => $site['lastFrame']['id']])); ?>" frameborder="0" scrolling="0" data-height="500" data-siteid="<?php echo e($site['siteData']['id']); ?>"></iframe>
                            <?php else: ?>
                            <a href="#" class="placeHolder">
                                <span>This site is empty</span>
                            </a>
                            <?php endif; ?>
                        </div><!-- /.viewport -->

                        <div class="bottom"></div><!-- /.bottom -->
                    </div><!-- /.window -->

                   <div class="siteDetails">
                        

                        <hr class="dashed light">

                        <div class="cselect" for="<?php echo e($site['siteData']['id']); ?>">
                            <p for="site_<?php echo e($site['siteData']['id']); ?>" class="btn btn-primary btn-embossed btn-block ">
                           <input type="radio" id="site_<?php echo e($site['siteData']['id']); ?>" name="choose_template" value="<?php echo e($site['siteData']['id']); ?>" required=""> Please Select</p>
                        </div>
                    </div><!-- /.siteDetails -->
                </div><!-- /.site -->
                <?php endforeach; ?>
            </div><!-- /.masonry -->
        </form>
        </div><!-- /.col -->
        <?php endif; ?>
    </div><!-- /.row -->
<script>
$(document).ready(function () {

 $('#sitecats').change(function() {
        var sid = $('#sitecats').val();
        var APP_URL = <?php echo json_encode(url('/')); ?>

        window.open(sid,'_self');

  });
  $('.cselect').change(function() {
    var cdi=$(this).attr('for');
    console.log(cdi);

  });
});        
</script>

<script src="<?php echo e(URL::to('src/js/vendor/flat-ui-pro.min.js')); ?>"></script>
<script src="<?php echo e(URL::to('src/js/vendor/jquery.zoomer.js')); ?>"></script>
<script src="<?php echo e(URL::to('src/js/build/sites.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>