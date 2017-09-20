<?php $__env->startSection('title'); ?>
Dashboard | Settings
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<?php echo $__env->make('includes.nav-bar', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<div class="container-fluid">

	<div class="row">
		<div class="col-md-9 col-sm-8">
			<h1><span class="fui-gear"></span> Settings</h1>
		</div><!-- /.col -->

		<div class="col-md-3 col-sm-4 text-right">

		</div><!-- /.col -->
	</div><!-- /.row -->

	<hr class="dashed margin-bottom-30">

	<div class="row">

		<div class="col-md-12">

			<ul class="nav nav-tabs nav-append-content">
				<li class="active"><a href="#appSettings"><span class="fui-gear"></span> Application Settings</a></li>
			</ul> <!-- /tabs -->

			<div class="tab-content">

				<div class="tab-pane active" id="appSettings">

					<div class="row">

						<div class="col-md-8">

							<?php if( Session::has('success') || Session::has('error') ): ?>
							<?php if( Session::has('error') ): ?>
							<div class="alert alert-error">
								<button type="button" class="close fui-cross" data-dismiss="alert"></button>
								<h4>Error</h4>
								<?php echo e(Session::get('error')); ?>

							</div>
							<?php else: ?>
							<div class="alert alert-success">
								<button type="button" class="close fui-cross" data-dismiss="alert"></button>
								<h4>Success!</h4>
								<?php echo e(Session::get('success')); ?>

							</div>
							<?php endif; ?>
							<?php else: ?>
							<div class="alert alert-warning">
								<button type="button" class="close fui-cross" data-dismiss="alert"></button>
								<h4>Be careful please!</h4>
								<p>Please be cautious when making changes to the settings below. Unless you know what you're doing, don't make changes to any of these.</p>
							</div>
							<?php endif; ?>

							<form class="form-horizontal settingsForm" role="form" id="settingsForm" method="post" action="<?php echo e(route('edit-settings')); ?>">
								<?php foreach( $settings as $configItem ): ?>
								<div class="form-group">
									<label for="<?php echo $configItem->config_name;?>" class="col-sm-3 control-label"><?php echo e($configItem->name); ?> <?php if( $configItem->required == 1 ): ?>*<?php endif; ?></label>
									<div class="col-sm-9">
										<textarea class="form-control" name="<?php echo $configItem->name;?>" id="<?php echo e($configItem->name); ?>"><?php echo e($configItem->value); ?></textarea>
										<div class="settingDescription">
											<?php echo $configItem->description; ?>

										</div>
									</div>
								</div>
								<?php endforeach; ?>
								<div class="form-group">
									<div class="col-sm-offset-3 col-sm-9">
										<p class="text-danger">* required fields, can not be empty!</p>
										<button type="submit" class="btn btn-primary btn-wide"><span class="fui-check"></span> Update Settings</button>
									</div>
								</div>
								<input type="hidden" name="_token" value="<?php echo e(Session::token()); ?>">
							</form>

						</div><!-- /.col -->

						<div class="col-md-4">

							<div class="alert alert-info configHelp" id="configHelp">
								<button type="button" class="close fui-cross" data-dismiss="alert"></button>
								<div>
									<h4>Config help</h4>
									<p>Click any of the setting boxes will display details about that setting in this box here :)</p>
								</div>
							</div>

						</div><!-- /.col -->

					</div><!-- /.row -->

				</div>

			</div> <!-- /tab-content -->

		</div><!-- /.col -->

	</div><!-- /.row -->

</div><!-- /.container -->

<!-- Modal -->

<?php echo $__env->make('includes.modal-account', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<!-- /modals -->

<!-- Load JS here for greater performance -->
<script src="<?php echo e(URL::to('src/js/vendor/jquery.min.js')); ?>"></script>
<script src="<?php echo e(URL::to('src/js/vendor/flat-ui-pro.min.js')); ?>"></script>
<script src="<?php echo e(URL::to('src/js/build/settings.js')); ?>"></script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>