<div class="proname-left">
        <div class="col-md-4">
                 <img src="{{ URL::to('src/images/dude.png') }}" />
             </div>    
             <div class="col-md-8">
                 <p>Welcome! </br><span>{{Session::get('name')}}</span></p>
               </div>
           </div>
 <div class="clear"></div>          
<ul class="side-nav metismenu nav" id="side-menu">
            
    <?php if(Request::path() ==  'userwebsite') { ?> 
        <li class="<?php if($page=='User Dashboard'){ echo 'active'; } ?>">
            <a href="{{ route('userdashboard') }}"><i class="fa fa-th-large" aria-hidden="true"></i> Back to Dashboard</a>
            
        </li>

<?php } if(Request::path() !=  'userwebsite') { //$data['page']='';  ?> 
       
            <li class="<?php if($page=='User Dashboard'){ echo 'active'; } ?>">
                <a href="{{ route('userdashboard') }}"> <i class="fa fa-th-large" aria-hidden="true"></i> Dashboard</a>
            
             </li>

        
        
        @if( Auth::user()->type != 'admin') 
       
        <li class="<?php if($data['page']=='My Websites' || $data['page']=='Domain Registration' || $data['page']=='Website Build' || $data['page']=='Getstart Website' || $data['page']=='Templates' || $data['page']=='Website Form'){ echo 'active'; } ?>">
            <a id="whichweb" href="{{ route('mysites') }}"><i class="fa fa-desktop" aria-hidden="true"></i> Website</a>
            
        </li>
        <li class="<?php if($page=='Reports Home' || $page=='Website Addition'){ echo 'active'; } ?>">
            <a id="whichreport" href="{{ route('user.reportshome') }}"><i class="fa fa-newspaper-o" aria-hidden="true"></i> Reports Dashboard</a>
            
        </li>
         <li class="<?php if($page=='Free Report Tools'){ echo 'active'; } ?>"><a href="{{ route('user.freereports') }}"><i class="fa fa-bar-chart" aria-hidden="true"></i> FREE Reports </a></li>   
         <li class="<?php if($page=='Paid Report Tools'){ echo 'active'; } ?>"><a href="{{ route('user.paidreports') }}"><i class="fa fa-bar-chart" aria-hidden="true"></i> PAID Reports </a></li>    
             
        <!--
        <li class="treeview <?php //if($page=='Reports Tools' || $page=='Free Report Tools' || $page=='Paid Report Tools'){ echo 'active'; } ?>">
            <a id="reportstools" href="#"><i class="fa fa-cogs" aria-hidden="true"></i> Reports Tools <i class="fa fa-angle-left pull-right"></i></a>
			
            <ul class="treeview-menu">
              <li><a href="{{ route('user.freereports') }}"><i class="fa fa-taxi" aria-hidden="true"></i> FREE </a></li>   
              <li><a href="{{ route('user.paidreports') }}"><i class="fa fa-bus" aria-hidden="true"></i> PAID </a></li>    
             </ul> 
        </li>-->
         <li class="<?php if($data['page']=='Packages'){ echo 'active'; } ?>">
            <a id="reportstools" href="{{ route('pricing') }}"><i class="fa fa-cogs" aria-hidden="true"></i> Subscription Packages</a>
            
        </li>
        <li class="<?php if($data['page']=='User Payments'){ echo 'active'; } ?>">
            <a id="reportstools" href="{{ route('user.payments') }}"><i class="fa fa-dollar" aria-hidden="true"></i> My Payments</a>
            
        </li>
        <li class="<?php if($page=='User Settings'){ echo 'active'; } ?>">
            <a id="reportstools" href="{{ route('user.settings') }}"><i class="fa fa-cogs" aria-hidden="true"></i> Settings</a>
            
        </li>
        @endif
        @if( Auth::user()->type == 'admin') 
        <li class="<?php if($data['page']=='I will do IT'){ echo 'active'; } ?>">
            <a id="iwilldoit" href="{{ route('iwilldoituser') }}"><i class="fa fa-tty" aria-hidden="true"></i></i> I Will Do IT</a>
            
        </li>
        <li class="<?php if($data['page']=='Do IT for me'){ echo 'active'; } ?>">
            <a id="doituser" href="{{ route('doitforuser') }}"><i class="fa fa-cubes" aria-hidden="true"></i> Do IT For Me</a>
            
        </li>
        <li class="<?php if($data['page']=='Payments'){ echo 'active'; } ?>">
            <a id="payments" href="{{ route('payments') }}"><i class="fa fa-usd" aria-hidden="true"></i> Payments</a>
            
        </li>
        <li class="<?php if($page=='Reports Home' || $page=='Website Addition'){ echo 'active'; } ?>">
            <a id="whichreport" href="{{ route('user.reportshome') }}"><i class="fa fa-newspaper-o" aria-hidden="true"></i> Reports Dashboard</a>
            
        </li>
        <!--
         <li class="<?php //if($page=='Free Report Tools'){ echo 'active'; } ?>"><a href="{{ route('user.freereports') }}"><i class="fa fa-bar-chart" aria-hidden="true"></i> FREE Reports </a></li>   
         <li class="<?php //if($page=='Paid Report Tools'){ echo 'active'; } ?>"><a href="{{ route('user.paidreports') }}"><i class="fa fa-bar-chart" aria-hidden="true"></i> PAID Reports </a></li>    
       -->
        <li class="<?php if($data['page']=='Mail Jobs'){ echo 'active'; } ?>">
            <a id="emailjob" href="{{ route('mailjob') }}"><i class="fa fa-reply" aria-hidden="true"></i> Email Job</a>
            
        </li>
       
        <li class="<?php if($page=='Reports Tools'){ echo 'active'; } ?>">
            <a id="reportstools" href="#"><i class="fa fa-envelope" aria-hidden="true"></i> Email Marketing</a>
            
        </li> 
        <li class="<?php if($data['page']=='Template Category'){ echo 'active'; } ?>">
            <a id="emailjob" href="{{ route('templates') }}"><i class="fa fa-table" aria-hidden="true"></i> Template Category </a>
            
        </li>
        <li class="<?php if($page=='Users'){ echo 'active'; } ?>">
            <a id="reportstools" href="{{ route('users') }}"><i class="fa fa-users" aria-hidden="true"></i> Users</a>
            
        </li>
        @endif
 <?php } ?>       
</ul>