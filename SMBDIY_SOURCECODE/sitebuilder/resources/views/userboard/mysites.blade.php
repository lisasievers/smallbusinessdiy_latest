@extends ('layouts.dashboard')

@section('section')
<div class="col-sm-12 ">
	 @if( Auth::user()->type == 'admin')
	    @include('layouts.partials.mysites') 
	 @else
	    @include('layouts.partials.mysites') 
	 @endif
     
</div>

@stop
