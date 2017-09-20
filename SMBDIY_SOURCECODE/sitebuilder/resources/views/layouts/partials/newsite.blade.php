
    <div class="row">
<div class="sitelist col-md-6">
  @if( isset($sites) && count( $sites ) > 0 )
  <div class="form-group">
<label>List of sites</label>
	
<select id="chosensite" data-style="btn-info">
 
@foreach( $sites as $site )
<option value="{{$site['siteData']['id']}}">{{ $site['siteData']['site_name'] }}</option>
 @endforeach

  </select>

</div>
<div class="form-group">
	<label></label>
	
<a href="#" id="whichwebb" class="btn btn-warning">Go to Website</a>
<a href="#" class="btn btn-info">Go to Reports</a>

</div>
 @endif
 </div> 

 <div class="col-md-6">
<h3>Want to create new site with sitebuilder!</h3>
<p>Please cleck below link and create </p>
<a href="userwebsite" class="btn btn-success">Create a website</a>
 </div>
   </div>
<script> 
    $(document).ready(function () {
      /*  $('.selectpicker').selectpicker({
  style: 'btn-info',
  size: 4
}); */
   console.log('rts');     
      var sld= $('#chosensite').val();
      console.log(sld);
    $('#whichwebb').attr('href','site/'+sld);
 $("#chosensite").change(function () {
    //  console.log('rts');
    var sld= $(this).val();
    $('#whichwebb').attr('href','site/'+sld);
    });

 });

</script>
   

