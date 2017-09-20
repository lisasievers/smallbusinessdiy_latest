<?php foreach($users as $user): ?>
<div class="user" data-name="<?php echo e($user['userData']->first_name); ?> <?php echo e($user['userData']->last_name); ?>">
	<div class="topPart clearfix">
		<img src="<?php echo e(URL::to('src/images/dude.png')); ?>" class="pic">
		<div class="details">
			<h4><?php echo e($user['userData']->first_name); ?> <?php echo e($user['userData']->last_name); ?></h4>
			<p>
				<span class="fui-mail"></span> <a href=""><?php echo e($user['userData']->email); ?></a>
			</p>
		</div><!-- /.details -->
	</div><!-- /.topPart -->
	<div class="bottom">
		<div class="loader" style="display: none;">
			<img src="<?php echo e(URL::to('src/images/loading.gif')); ?>" alt="Loading...">
		</div>
		<div class="alerts"></div>
		<ul class="nav nav-tabs nav-append-content">
			<li class="active"><a href="#<?php echo e($user['userData']->id); ?>_account"><span class="fui-user"></span> Account</a></li>
			<li><a href="#<?php echo e($user['userData']->id); ?>_sites"><span class="fui-window"></span> Sites (<span class="text-primary"><?php echo e(count($user['site'])); ?></span>)</a></li>
		</ul><!-- /tabs -->
		<div class="tab-content clearfix">
			<div class="tab-pane clearfix" id="<?php echo e($user['userData']->id); ?>_sites">
				<div class="userSites">
					<?php if(count($user['site']) == 0): ?>
					<!-- Alert Info -->
					<div class="alert alert-info">
						<button type="button" class="close fui-cross" data-dismiss="alert"></button>
						This user has not created any sites yet.
					</div>
					<?php endif; ?>

					<?php foreach($user['site'] as $site): ?>
					<div class="userSite site">
						<div class="window">
							<div class="top">
								<div class="buttons clearfix">
									<span class="left red"></span>
									<span class="left yellow"></span>
									<span class="left green"></span>
								</div>
								<?php //dd($site); ?>
								<b><?php echo e($site['siteData']['site_name']); ?></b>
							</div><!-- /.top -->
							<div class="viewport">
								<?php if($site['lastFrame'] != ''): ?>
								<iframe src="<?php echo e(route('getframe', ['frame_id' => $site['lastFrame']['id']])); ?>" frameborder="0" scrolling="0" data-height="<?php echo e($site['lastFrame']['height']); ?>" data-siteid="<?php echo e($site['lastFrame']['site_id']); ?>"></iframe>
								<?php else: ?>
								<a href="<?php echo e(route('site', ['site_id' => $site['siteData']['id']])); ?>" class="placeHolder">
									<span>This site is empty</span>
								</a>
								<?php endif; ?>
							</div><!-- /.viewport -->
						</div><!-- /.window -->
						<div class="siteButtons clearfix">
							<a href="<?php echo e(route('site', ['site_id' => $site['siteData']['id']])); ?>" class="btn btn-primary btn-sm btn-embossed"><span class="fui-new"></span> Edit</a>
							<a href="#" class="btn btn-info btn-sm btn-embossed siteSettingsModalButton" data-siteid="<?php echo e($site['siteData']['id']); ?>"><span class="fui-gear"></span> Settings</a>
							<a href="#deleteSiteModal" class="btn btn-danger btn-sm btn-embossed deleteSiteButton" data-siteid="<?php echo e($site['siteData']['id']); ?>"><span class="fui-trash"></span> Delete</a>
						</div>
					</div><!-- /.userSite -->
					<?php endforeach; ?>
				</div><!-- /.userSites -->
			</div><!-- /.tab-pane -->
			<div class="tab-pane active" id="<?php echo e($user['userData']->id); ?>_account">
				<?php echo $__env->make('partials.userdetails', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
				<hr class="dashed">
				<div class="actions clearfix">
					<a href="#" class="btn btn-info btn-embossed btn-block passwordReset" data-userid="<?php echo e($user['userData']->id); ?>"><span class="fui-mail"></span> Send Password Reset Email</a>
					<div>
						<a href="<?php echo e(route('user-delete', ['user_id' => $user['userData']->id])); ?>" class="btn btn-danger btn-embossed deleteUserButton"><span class="fui-cross-inverted"></span> Delete Account</a>
						<span>
							<?php if($user['userData']->active == 1): ?>
							<a href="<?php echo e(route('user-enable-disable', ['user_id' => $user['userData']->id])); ?>" class="btn btn-default btn-embossed"><span class="fui-power"></span> Disable</a>
							<?php else: ?>
							<a href="<?php echo e(route('user-enable-disable', ['user_id' => $user['userData']->id])); ?>" class="btn btn-inverse btn-embossed"><span class="fui-power"></span> Enable</a>
							<?php endif; ?>
						</span>
					</div>
				</div><!-- /.actions -->
			</div><!-- /.tab-pane -->
		</div> <!-- /tab-content -->
	</div><!-- /.bottom -->
	<?php if($user['userData']->active == 0): ?>
	<div class="ribbon-wrapper"><div class="ribbon">disabled</div></div>
	<?php endif; ?>
</div><!-- /.user -->
<?php endforeach; ?>