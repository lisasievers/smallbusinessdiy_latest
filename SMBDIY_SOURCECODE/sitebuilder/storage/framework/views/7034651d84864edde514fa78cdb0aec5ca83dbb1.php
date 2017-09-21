<?php $__env->startSection('title'); ?>
Welcome Dashboard!
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<?php echo $__env->make('includes.nav-bar', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<div class="container-fluid">

    <?php if( Session::has('error') ): ?>
    <div class="row margin-top-20">
        <div class="col-md-12">
            <div class="alert alert-danger margin-bottom-0">
                <button type="button" class="close fui-cross" data-dismiss="alert"></button>
                <?php echo e(Session::get('error')); ?>

            </div>
        </div><!-- /.col -->
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-9 col-sm-8">
            <h1><span class="fui-windows"></span> Sites</h1>
        </div><!-- /.col -->

        <div class="col-md-3 col-sm-4 text-right">
            <a href="<?php echo e(route('site-create')); ?>" class="btn btn-lg btn-primary btn-embossed btn-wide margin-top-40"><span class="fui-plus"></span> Create New Site</a>
        </div><!-- /.col -->
    </div><!-- /.row -->

    <hr class="dashed">

    <div class="row margin-bottom-30">
        <?php if(Auth::user()->type == 'admin'): ?>
        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <select name="userDropDown" id="userDropDown" class="form-control select select-inverse btn-block mbl ">
                    <option value="">Filter By User</option>
                    <option value="All">All</option>
                    <?php foreach($users as $user): ?>
                    <option value="<?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?>"><?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div><!-- /.col -->
        <?php endif; ?>

        <div class="col-md-3 col-sm-6">
            <div class="form-group">
                <select name="sortDropDown" id="sortDropDown" class="form-control select select-inverse select-block mbl" >
                    <option value="">Sory by</option>
                    <option value="CreationDate">Creation date</option>
                    <option value="LastUpdate">Last updated</option>
                    <option value="NoOfPages">Number of pages</option>
                </select>
            </div>
        </div><!-- /.col -->
    </div><!-- /.row -->

    <div class="row">
        <?php if( isset($sites) && count( $sites ) > 0 ): ?>
        <div class="col-md-12">
            <?php //print_r($sites); ?>
            <div class="masonry-4 sites" id="sites">
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
                            <a href="<?php echo e(route('site', ['site_id' => $site['siteData']['id']])); ?>" class="placeHolder">
                                <span>This site is empty</span>
                            </a>
                            <?php endif; ?>
                        </div><!-- /.viewport -->

                        <div class="bottom"></div><!-- /.bottom -->
                    </div><!-- /.window -->

                    <div class="siteDetails">
                        <p>
                            Owner: <b><?php echo e($site['siteData']['user']['first_name']); ?> <?php echo e($site['siteData']['user']['last_name']); ?></b>, <?php echo e($site['nrOfPages']); ?> Page(s)<br>
                            Created on: <b><?php echo e(date('Y-m-d', strtotime($site['siteData']['created_at']))); ?></b><br>
                            Last edited on: <b><?php echo e(date('Y-m-d', strtotime($site['siteData']['updated_at']))); ?></b>
                        </p>

                        <p class="siteLink">
                            <?php if( $site['siteData']['ftp_published'] == 1 ): ?>
                            <?php if( $site['siteData']['remote_url'] != '' ): ?>
                            <span class="fui-link"></span> <a href="<?php echo e($site['siteData']['remote_url']); ?>" target="_blank"><?php echo e($site['siteData']['remote_url']); ?></a>
                            <?php else: ?>
                            Site was published on <?php echo e(date('Y-m-d', strtotime($site['siteData']['publish_date']))); ?>

                            <?php endif; ?>
                            <?php else: ?>
                            <span class="pull-left text-danger">
                                <b>Site has not been published</b>
                            </span> &nbsp;&nbsp;
                            <?php if( $site['siteData']['ftp_ok'] == 1 ): ?>
                            <a class="btn btn-inverse btn-xs" href="<?php echo e(route('site', ['site_id' => $site['siteData']['id']])); ?>#publish">
                                <span class="fui-export"></span> Publish Now
                            </a>
                            <?php endif; ?>
                            <?php endif; ?>
                        </p>

                        <hr class="dashed light">

                        <div class="clearfix">
                            <a href="<?php echo e(route('site', ['site_id' => $site['siteData']['id']])); ?>" class="btn btn-primary btn-embossed btn-block"><span class="fui-new"></span> Edit This Site</a>
                            <a href="#" class="btn btn-info btn-embossed btn-block btn-half pull-left btn-sm siteSettingsModalButton first" data-siteid="<?php echo e($site['siteData']['id']); ?>"><span class="fui-gear"></span> Settings</a>
                            <a href="#deleteSiteModal" class="btn btn-danger btn-embossed btn-block btn-half pull-left deleteSiteButton btn-sm second" id="deleteSiteButton" data-siteid="<?php echo e($site['siteData']['id']); ?>"><span class="fui-trash"></span> Delete</a>
                        </div>
                    </div><!-- /.siteDetails -->
                </div><!-- /.site -->
                <?php endforeach; ?>
            </div><!-- /.masonry -->
        </div><!-- /.col -->
        <?php else: ?>
        <div class="col-md-6 col-md-offset-3">
            <div class="alert alert-info" style="margin-top: 30px">
                <button type="button" class="close fui-cross" data-dismiss="alert"></button>
                <h2>Well, hello there!</h2>
                <p>
                    It appears you might be new around these parts. At the moment, you don't have any sites to call your own just yet, so why not get started and build yourself one awesome little website?
                </p>
                <br><br>
                <a href="<?php echo e(route('site-create')); ?>" class="btn btn-primary btn-lg btn-wide">Yes, I want to build a website!</a>
                <a href="#" class="btn btn-default btn-lg btn-wide" data-dismiss="alert">Nah, maybe later</a>
            </div>
        </div><!-- ./col -->
        <?php endif; ?>

    </div><!-- /.row -->

</div><!-- /.container -->

<!-- modals -->

<?php echo $__env->make('includes.modal-sitesettings', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php echo $__env->make('includes.modal-account', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php echo $__env->make('includes.modal-deletesite', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<!-- /modals -->

<!-- Load JS here for greater performance -->
<script src="<?php echo e(URL::to('src/js/vendor/jquery.min.js')); ?>"></script>
<script src="<?php echo e(URL::to('src/js/vendor/jquery-ui.min.js')); ?>"></script>
<script src="<?php echo e(URL::to('src/js/vendor/flat-ui-pro.min.js')); ?>"></script>
<script src="<?php echo e(URL::to('src/js/vendor/jquery.zoomer.js')); ?>"></script>
<script src="<?php echo e(URL::to('src/js/build/sites.js')); ?>"></script>

<!--[if lt IE 10]>
<script>
$(function(){
	var msnry = new Masonry( '#sites', {
    	// options
    	itemSelector: '.site',
    	"gutter": 20
    });

})
</script>
<![endif]-->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>