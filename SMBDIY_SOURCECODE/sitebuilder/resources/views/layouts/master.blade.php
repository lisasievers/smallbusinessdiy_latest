<!DOCTYPE html>
<html>
<head><!--Sitebuilder page -->
	<title>{{$cdata[0]}}</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	    <link rel="shortcut icon" href="https://storage.googleapis.com/assets-sitebuilder/images/favicon.ico" type="image/icon" >
    <link rel="icon" href="https://storage.googleapis.com/assets-sitebuilder/images/favicon.ico" type="image/ico" >
    
	<link href="{{ URL::to('src/css/vendor/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ URL::to('src/css/flat-ui-pro.css') }}" rel="stylesheet">
	<link href="{{ URL::to('src/css/style.css') }}" rel="stylesheet">
	<link href="{{ URL::to('src/css/login.css') }}" rel="stylesheet">
	<link href="{{ URL::to('src/css/font-awesome.css') }}" rel="stylesheet">

	<link href="{{ URL::to('src/css/builder.css') }}" rel="stylesheet">
	<link href="{{ URL::to('src/css/spectrum.css') }}" rel="stylesheet">
	<link href="{{ URL::to('src/css/chosen.css') }}" rel="stylesheet">
	<link href="{{ URL::to('src/css/summernote.css') }}" rel="stylesheet">

	<!--<link rel="stylesheet" href="{{ URL::to('src/css/build-main.min.css') }}">-->
	<script>
		var baseUrl = '<?php echo url('/'); ?>/';
		var siteUrl = '<?php echo url('/'); ?>/';
	</script>
</head>
<body>
	<?php
    header('X-Frame-Options: GOFORIT'); 
	?>
	@yield('content')

	<script>
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		})
	</script>
</body>
</html>