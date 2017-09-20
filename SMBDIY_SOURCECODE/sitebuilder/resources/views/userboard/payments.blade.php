@extends ('layouts.dashboard')

@section('section')
<div class="col-sm-12 ">
	 @if( Auth::user()->type == 'admin')
	    @include('layouts.partials.admin_payments') 
	 @else
	    @include('layouts.partials.userdash') 
	 @endif
     
</div>

@stop