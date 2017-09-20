<link rel="stylesheet" href="{{ asset('src/css/jquery.dataTables.min.css') }}">
<style>
#chosensite{width:350px;height: 42px;padding: 2px;}
.act-group{margin-top: 10px;}
</style>
<div class="row">
  <h2 class="page-header"><i class="fa fa-list-ul" aria-hidden="true"></i> Your Sites</h2>
  @if( isset($sites) && count( $sites ) > 0 )
  <div class="sitelist col-md-6">
    
<select id="chosensite" data-style="btn-info">
 
@foreach( $sites as $site )
<option value="{{$site['id']}}">{{ $site['site_name'] }}</option>
 @endforeach

  </select>
  <div class="act-group">
<a href="#" id="whichwebb" class="btn btn-lg btn-warning btn-embossed margin-top-40">Go to Website</a>  
</div>
 <!--<img src="{{ URL::to('src/images/db.jpg') }}" /> -->
  </div> 
  <div class="col-md-6">
<h2>Want to start new site?</h2>
<p>Build static HTML websites by combining pre-designed blocks together and export your site or publish it directly to a live server.</p>
<a href="{{ route('site-create') }}" class="btn btn-lg btn-primary btn-embossed btn-wide margin-top-40"><span class="fui-plus"></span> Create New Site</a>
 </div>
 @else
<div class="col-md-6">
<h2>Don't have a website?</h2>
<p>Build static HTML websites by combining pre-designed blocks together and export your site or publish it directly to a live server.</p>
<a href="{{ route('site-create') }}" class="btn btn-lg btn-primary btn-embossed btn-wide margin-top-40"><span class="fui-plus"></span> Create New Site</a>
 </div>
 <div class="col-md-6">
  <h2>Features</h2>

 </div>
 @endif
</div>
<script src="{{ asset('src/js/jquery.dataTables.min.js') }}"></script> 
<script>
    $(document).ready(function () {
      /*  $('.selectpicker').selectpicker({
  style: 'btn-info',
  size: 4
}); */
   //console.log('rts');     
      var sld= $('#chosensite').val();
      //console.log(sld);
    $('#whichwebb').attr('href','site/'+sld);
 $("#chosensite").change(function () {
    //  console.log('rts');
    var sld= $(this).val();
    $('#whichwebb').attr('href','site/'+sld);
    });

 });
</script>   

