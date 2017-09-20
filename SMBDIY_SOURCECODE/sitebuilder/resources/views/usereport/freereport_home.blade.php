@extends ('layouts.dashboard')

@section('section')
<div class="col-sm-12 ">
	<h2>Free Report Tools</h2>
	<div class="col-md-4">
		<div class="tools-box">
		
		<a href="{{ route('user.qrcode.generation') }}" class="btn btn-success btn-embossed btn-block">
<img src="{{ URL::to('src/images/icons/qrcode.png') }}" />
QR Code Generator
		</a>
	</div>
	</div>
     <div class="col-md-4">
     	<div class="tools-box">
     	
     	<a href="{{ route('user.gmobiletest') }}" class="btn btn-success btn-embossed btn-block">
<img src="{{ URL::to('src/images/icons/responsive.svg') }}" />
Mobile Friendly Test
		</a>
     </div>
	</div>
</div>

@stop
