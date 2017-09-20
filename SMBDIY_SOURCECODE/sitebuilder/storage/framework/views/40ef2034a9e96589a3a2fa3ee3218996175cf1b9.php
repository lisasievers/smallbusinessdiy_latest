<div class="images masonry-3" id="myImages">
	 <?php if( isset($userImages) && count( $userImages ) > 0 ): ?>
<?php foreach($userImages as $img): ?>
<div class="image">
	<div class="imageWrap">
		<img src="<?php echo e($userSrc); ?>/<?php echo e($img); ?>">
	</div>
	<div class="buttons clearfix">
		<button type="button" class="btn btn-info btn-embossed btn-block btn-sm useImage" data-url="<?php echo e($dataURL); ?>/<?php echo e(Auth::user()->id); ?>/<?php echo $img;?>"><span class="fui-export"></span> Insert Image</button>
	</div>
</div><!-- /.image -->
<?php endforeach; ?>
 <?php endif; ?>
</div><!-- /.images -->