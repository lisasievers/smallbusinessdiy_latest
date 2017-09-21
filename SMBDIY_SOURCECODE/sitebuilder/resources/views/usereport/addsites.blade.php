@extends ('layouts.dashboard')

@section('section')
<style>
form#ProSettings .input-group {
    margin-bottom: 20px;
    
}
.web-case{    
color: red;
 font-weight: 400;}
</style>
<div class="col-sm-12 ">
	<h2>Add a New Website</h2>
				<form role="form" id="ProSettings" action="{{ route('user.addsites') }}" method="post" enctype="multipart/form-data">
					
                <div class="input-group {{ $errors->has('site_name') ? 'has-error' : '' }}">
                    <b>Enter Domain Name:</b><br>
                    <p class="web-case">Please type without http://, https:// and www. eg:( google.com )</p>
                    <input type="text" class="form-control" id="site_name" name="site_name"  placeholder="Website name *" />
                </div>
                

                
                <input type="hidden" name="_token" value="{{ Session::token() }}">
               
                <div class="signbtn-group input-group">
                    <button type="submit" class="btn btn-primary btn-block btn-embossed" id="choosesubmit" > Submit </span></button>
                 </div>  
                

            </form>
</div>

@stop
