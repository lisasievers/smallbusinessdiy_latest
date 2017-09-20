@extends ('layouts.dashboard')

@section('section')
<style>

</style>
<div class="col-sm-12 ">
  <h2>Success!</h2>   
  <?php
//if(isset($data['sess'])){ print_r($data); }
  ?>
  
        @if( Session::has('success') )
        <div class="alert alert-success" style="width:97%; margin-left: auto; margin-right: auto">
            <button data-dismiss="alert" class="close fui-cross" type="button"></button>
            <h4>All good!</h4>
            <p>
                {{ Session::get('success') }} Thank you for submitting, your website will activated within 48 hours!
            </p>
        </div>
        @endif
</div>
   </div>
   <div class="clear"></div>       
</div>

@stop
