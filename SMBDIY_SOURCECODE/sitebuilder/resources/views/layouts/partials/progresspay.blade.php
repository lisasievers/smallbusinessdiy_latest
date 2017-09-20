<div class="row">
<div class="user-container">
  
<div class="stepwizard ">
    <div class="stepwizard-row setup-panel">
      <div class="stepwizard-step">
        
        <a href="#step-1" type="button" id="s1" class="btn btn-primary btn-circle">1</a>
    
        <p>Domain Registration</p>
      </div>
      <div class="stepwizard-step">
        <a href="#step-2" type="button" id="s2" class="btn btn-default btn-circle" disabled="disabled">2</a>
        <p>Site Builder</p>
      </div>
      <div class="stepwizard-step">
        <a href="#step-3" type="button" id="s3" class="btn btn-default btn-circle" disabled="disabled">3</a>
        <p class="doo">Website Builder</p>
      </div>
    </div>
  </div> 
<!--
  <div class="progress">
  <div class="circle done stepwizard-step">
    <span class="label">1</span>
    <span class="title">Personal</span>
  </div>
  <span class="bar done stepwizard-step"></span>
  <div class="circle done">
    <span class="label">2</span>
    <span class="title">Address</span>
  </div>
  <span class="bar half"></span>
  <div class="circle active">
    <span class="label">3</span>
    <span class="title">Payment</span>
  </div>
  <span class="bar"></span>
  <div class="circle">
    <span class="label">4</span>
    <span class="title">Review</span>
  </div>
  <span class="bar"></span>
  <div class="circle">
    <span class="label">5</span>
    <span class="title">Finish</span>
  </div>
</div>
-->
  <form role="form" id="sign_form" method="post" action="{{ route('image.upload.ajax') }}" enctype="multipart/form-data">
    <div class=" setup-content" id="step-1">
      <div class="col-xss-6 ">
        
          <h3> Domain Registration</h3>
          <div class="form-group">
            <label class="control-label">Do you have a domain ?</label>
          <input id="input-2" type="checkbox" value="yes" checked >
          </div>
          <div class="domaininfo">
<div class="form-group">
            <label class="control-label">Domain Name</label>
            <input maxlength="200" type="text" required="required" class="form-control domain_url" placeholder="Enter Domain Name">
            <input type="hidden" class="form-control site_url" value="{{$cdata[1]}}/sitebuilder/public/" />
          </div>
          <div class="form-group">
            <label class="control-label">Domain Registered Email</label>
            <input maxlength="200" type="email" required="required" class="form-control domain_email" placeholder="Enter Email">
          </div>
        </div>
        <div class="form-group-footer">
          <button class="btn btn-primary nextBtn btn pull-right" type="button">Next</button>
        </div>
      </div>
    </div>
    <div class=" setup-content" id="step-2">
      <div class="col-xss-6 ">
        
          <h3> Site Builder</h3>
          <div class="form-group">
            <label class="control-label">Sitebuilder development by us / you ?</label>
<div class="controls doradio">
<input type="radio" class="" id="pay_cash" name="whodo" value="iwill" style="margin-left:2px;margin-right:10px" required=""><label for="pay_cash"> I will <span class="diy-color">DO IT</span></label>
<input type="radio" class="" id="pay_adjust" name="whodo" value="youwill" style="margin-left:50px;margin-right:10px;" required=""><label for="pay_adjust"> <span class="diy-color">DO IT</span> for me</label>
</div>
          </div>
           <input type="hidden" name="_token" class="token" value="{{ Session::token() }}">
          <div class="form-group-footer">
          <button class="btn btn-default prevDoBtn btn pull-left" type="button">Previous</button>
          <button class="btn btn-primary DoBtn btn pull-right" type="button">Next</button>
       </div>
      </div>
    </div>
    <div class=" setup-content" id="step-3">
      <div class="col-xss-6">
        
          <h3 class="dod"> Do it for me  </h3>
          <div class="sitebuild">
            <h4>Loading...!! Self Hosted Drag & Drop Website Builder</h4>
            <img class="sitbuild-screen" src="{{url('src/images/sample-img.png')}}" alt="sitebuilder" />
          </div>

          <div class="domaininfo-need">  
        <div class="form-group">
            <label class="control-label">Website Name</label>
            <input maxlength="200" type="text" required="required" class="form-control webname" />
         </div>
         <div class="form-group">
            <label class="control-label">How many pages?</label>
            <input maxlength="200" type="text" required="required" class="form-control webpages" >
            <input type="hidden" name="_token" class="token" value="{{ csrf_token() }}">
         </div>
         <div class="control-group">
            <label class="control-label">Upload your full requirement document</label>
             <div class="import_inst">Please upload your file below. <a href="{{ url('src/excel/webdoc.xls') }}">Click here</a> for sample document import file.</div>
                            
                       <div class="controls">
                                <input type="file" id="imageFile" name="imageFile">
                              
                            </div>

          </div>
               <div class="form-group" style="margin-bottom:10px;"></div>         
        </div>
        <!--payment section -->
          <div class="payinfo">
          <!--<h5> Thanks for initiative sitebuilder with us and we will get back to you withnn 48 hours.</h5>-->
          <h5> Thanks for initiative sitebuilder with us. Find below initial cost of website.</h5>
          <label>Registered Domain: </label> <span class="paydomain"></span>
          <div class="cost-data">
          <table class="table" style="width:60%"><tr><td>Inital Basic cost</td><td>${{$data['doit_cost']}}</td></tr><tr><td>Setup cost</td><td>${{$data['setup_cost']}}</td></tr><tr><td>Total cost</td><td>${{$data['doit_cost']+$data['setup_cost']}}</td></tr></table>
          <input type="hidden" class="doitcost" value="{{$data['doit_cost']+$data['setup_cost']}}" />
          <a href="#" class="btn btn-primary paysubmit">Pay Now</a> <a href="#" class="btn btn-default paylater">Later</a>
          </div>
      </div>
          <div class="form-group-footer lastbtn">
          <button class="btn btn-default prevSubBtn btn pull-left" type="button">Previous</button>
          <button class="btn btn-success btn submit pull-right" type="button">Submit</button> 
          
        </div>
        </div>
      
    </div>
  </form>
  
</div>
   </div>
   <div class="clear"></div>

