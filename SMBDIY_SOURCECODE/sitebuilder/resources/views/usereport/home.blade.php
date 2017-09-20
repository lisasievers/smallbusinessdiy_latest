@extends ('layouts.dashboard')

@section('section')
<div class="col-sm-12 ">
	 @if( Auth::user()->type == 'admin')
	    @include('layouts.reports.adminreports') 
	 @else
	    @include('layouts.reports.usereports') 
	 @endif
     
</div>

@stop
