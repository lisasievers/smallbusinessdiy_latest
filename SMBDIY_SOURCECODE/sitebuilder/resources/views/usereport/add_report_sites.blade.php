@extends ('layouts.dashboard')

@section('section')
<div class="col-sm-12 ">
	 @if( Auth::user()->type == 'admin')
	    @include('layouts.reports.add_site_for_reports') 
	 @else
	    @include('layouts.reports.add_site_for_reports') 
	 @endif
     
</div>

@stop
