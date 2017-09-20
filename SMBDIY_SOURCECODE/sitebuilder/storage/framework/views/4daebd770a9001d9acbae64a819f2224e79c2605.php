<html>
<body>
	<h1>Reset Password for <?php echo e($email); ?></h1>
	<p>Please click this link to <a href="<?php echo e(route('user-pw-reset-code', ['code' => $code])); ?>">Reset Your Password</a></p>
</body>
</html>