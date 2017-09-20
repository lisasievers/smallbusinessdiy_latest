<!DOCTYPE html>
<html class="" lang="en">
<head>
<meta charset="utf-8">
</head>
<body>
Dear <?php echo e($name); ?>,<br/><br/>

Thank you for your Sitebuilder payment. Find below the details:<br/><br/>

Payment ID: <?php echo e($payid); ?><br/>

Payment Amount ($): <?php echo e($amount); ?><br/>

Payment Date : <?php echo e($date); ?><br/>

Payment against Site : <?php echo e($site); ?><br/>

Payment status : Success<br/>
	
</body>
</html>