@extends ('layouts.dashboard')

@section('section')
<style>
.tickmark {
color:#27ae60;
font-size: 32px;
}
.loader {
  height: 4px;
  width: 100%;
  position: relative;
  overflow: hidden;
  background-color: #ddd;
}
/*
.loader:before{
  display: block;
  position: absolute;
  content: "";
  left: -200px;
  width: 200px;
  height: 4px;
  background-color: #2980b9;
  animation: loading 2s linear infinite;
}

@keyframes loading {
    from {left: -200px; width: 30%;}
    50% {width: 30%;}
    70% {width: 70%;}
    80% { left: 50%;}
    95% {left: 120%;}
    to {left: 100%;}
}*/
.iwill {
  border:2px solid #ddd;
  animation: color-me-in 4s infinite;
}
.youwill {
  border:2px solid #ddd;
  animation: color-me-in 6s infinite;
}

@keyframes color-me-in {
  0% {
    border:2px solid #FF6347;
  }
  100% {
    border:2px solid #ddd;
  }
}
.youwilltick,.iwilltick{display: none;}
</style>

<div class="col-sm-12 ">
<h3 class="stepscount">You have just <span class="clock">2</span> more steps to complete</h3>
        @include('layouts.partials.progress') 
        <form role="form" id="choose_form" method="post" action="{{ route('userwebsite.builtby') }}" >
           <div class=" setup-content2" id="step-2">
      <div class="col-xss-6 ">
        
          <h3 class="diy-color"> Pick a plan to get started.</h3>
          <div class="col-md-12">
            <label class="control-label">Do you want to build the website yourself? else, We will help you!!</label>
<div class="col-md-6">
  <div class="box-content youwill">
<div class="col-md-4">
                 <img src="{{ URL::to('src/images/icons/clipboard.svg') }}" />
             </div>    
             <div class="col-md-8">
                 <h3>Do IT For Me</h3>
               </div>
              <div class="col-md-nxt">If you'd rather leave the job to experts, just choose a template and our design team will whip up a magical site for you.</div>
<div class="col-md-121">
<div class="radiobox"><i class="fa fa-check-square tickmark youwilltick" aria-hidden="true"></i>  </div>

</div>
</div>
</div>

<div class="col-md-6">
  <div class="box-content iwill">
<div class="col-md-4">
                 <img src="{{ URL::to('src/images/icons/user-interface.svg') }}" />
             </div>    
             <div class="col-md-8">
                 <h3>I Will Do IT</h3>
               </div>
            <div class="col-md-nxt">Design your website from scratch with our easy-to-use drag and drop tools. No technical skills required.</div>   
<div class="col-md-121">
<div class="radiobox">
<i class="fa fa-check-square tickmark iwilltick" aria-hidden="true"></i>
</div>
</div>

</div>



</div>
<!--<div class="controls doradio">
<input type="radio" class="" id="pay_cash" name="whodo" value="iwill" style="margin-left:2px;margin-right:10px" required=""><label for="pay_cash"> I will <span class="diy-color">DO IT</span></label>
<input type="radio" class="" id="pay_adjust" name="whodo" value="youwill" style="margin-left:50px;margin-right:10px;" required=""><label for="pay_adjust"> <span class="diy-color">DO IT</span> for me</label>
</div>-->
          </div>
            <input type="hidden" name="_token" value="{{ Session::token() }}">
            <input type="hidden" name="whodo" id="whodo" value="" />
            <?php if(isset($_REQUEST['domain'])){$dname=$_REQUEST['domain'];}else{$dname=$data['sess']['site_name'];} ?>
            <input type="hidden" name="domain_name" id="domain_name" value="{{$dname}}" />
          <div class="form-group-footer">
          <a href="{{ route('userwebsite.domain') }}" class="btn btn-default prevDoBtn btn pull-left">Previous</a>
          <button class="btn btn-primary  btn pull-right" id="choosesubmit" type="submit">Next</button>
       </div>
      </div>
    </div>

  </form>
  
</div>
   </div>
   <div class="clear"></div>       
</div>
<script>
$(document).ready(function () {
$("#choosesubmit").click(function() {
  var chval=$('#whodo').val();
  console.log(chval);
  if(chval!=""){
  $( "#choose_form" ).submit();
  }
  else
  {
    //$('.youwill,.iwill').css('border','1px solid #ff0000');
    return false;

  }
});

});
</script>
@stop
