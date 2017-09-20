@extends ('layouts.dashboard')

@section('section')
<div class="col-sm-12 ">
<?php
$sess=$data['sess'];
$url=$sess['url'];
//dd($sess);
if($sess['whodo']=='iwill'){

  header("refresh:4;url=".$url ); 
}
else
{

  header("refresh:1;url=".$url );  
}

?>
<h3 class="stepscount">Great job ! You are Almost done</h3>
        @include('layouts.partials.progress') 
        <form role="form" id="sign_form" method="post" action="" enctype="multipart/form-data">
        <div class=" setup-content3" id="step-3">
      <div class="col-xss-6">
        
        <!--  <h3 class="dod diy-color"> Do it for me</h3>-->
          <div class="sitebuild">
            <h4>You can now start building your website.</h4>
            <img class="sitbuild-screen" src="{{url('src/images/sample-img.png')}}" alt="sitebuilder" />
          </div>

        </div>
      
    </div>

  </form>
  
</div>
   </div>
   <div class="clear"></div>       
</div>

@stop
