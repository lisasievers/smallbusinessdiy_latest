@extends ('layouts.dashboard')

@section('section')


<div class="col-sm-12 ">
<h3 class="stepscount">You have just <span class="clock">3</span> more steps to complete</h3>
        @include('layouts.partials.progress') 
  <?php
$re="";
$site_id="";
$re=Session::get('state');
$site_id=Session::get('site_id');
?>    
 <!--<button id="buydomain" onclick="showGoogleDomainsFlow()" >Buy a domain</button>-->
  
    <div class="setup-content1" id="step-1">
      
      <div class="col-xss-6 ">
        
          <h3 class="diy-color"> Domain Registration</h3>
          <div class="form-group">
            <label class="control-label">Do you have a domain ?</label>
          <input id="input-2" type="checkbox" value="yes" checked >
          </div>
          <form role="form" id="sign_form" method="post" action="{{ route('userwebsite.domain') }}" >
          <div class="domaininfo">
          <div class="form-group">
   
            <label class="control-label">Domain Name</label>
            <input maxlength="200" type="text" required="required" name="domain_name" class="form-control domain_url" placeholder="Enter Domain Name">
            
             <input type="hidden" name="_token" value="{{ Session::token() }}">
          </div>
          <div class="form-group">
            <label class="control-label">Domain Registered Email</label>
            <input maxlength="200" type="email" required="required" name="domain_email" class="form-control domain_email" placeholder="Enter Email">
          </div>
       
        </div><!-- onclick="showGoogleDomainsFlow()" -->
        
        <div class="form-group-footer">
          <button class="btn btn-warning nextBtna btn " type="submit">Next</button>
        </div>
        </form>
      <div class="domainbuyinfo">Here, your text placements.... text placements..<br><br><span class="domainbuybtn"></span><br></div>         
      </div>
    </div>
</div>
   </div>
   <div class="clear"></div>       
</div>
@stop
