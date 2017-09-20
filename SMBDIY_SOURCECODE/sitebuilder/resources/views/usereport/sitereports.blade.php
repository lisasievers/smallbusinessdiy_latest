@extends ('layouts.dashboard')

@section('section')
<div class="col-sm-12 ">
	 @if( Auth::user()->type == 'admin')
	    @include('layouts.reports.sitereports') 
	 @else
	    @include('layouts.reports.sitereports') 
	 @endif
     
</div>

@stop
