<!DOCTYPE html>
<html class="" lang="en">
<head>
<meta charset="utf-8">
</head>
<body>
Dear Team,<br/><br/>

The following payment received in our Stripe account.<br/><br/>

Payment ID: <?php echo e($payid); ?><br/>

Payment Amount ($): <?php echo e($amount); ?><br/>

Payment Date : <?php echo e($date); ?><br/>

Payment against Site : <?php echo e($site); ?><br/>

Payment User: <?php echo e($name); ?><br/>

Payment from email : <?php echo e($email); ?><br/>

Payment status : Success<br/>
	
</body>
</html>