 <?php if( isset($adminImages) && count( $adminImages ) > 0 ): ?>
<?php foreach($adminImages as $img): ?>
    <div class="image">
        <div class="imageWrap">
            <img src="<?php echo e($adminSrc); ?>/<?php echo e($img); ?>">
            <div class="ribbon-wrapper-red"><div class="ribbon-red">Admin</div></div>
        </div>
        <div class="buttons clearfix">
            <button type="button" class="btn btn-info btn-embossed btn-block btn-sm useImage" data-url="<?php echo e($dataURL); ?>/<?php echo e($img); ?>"><span class="fui-export"></span> Insert Image</button>
        </div>
    </div><!-- /.image -->
<?php endforeach; ?>
 <?php endif; ?>