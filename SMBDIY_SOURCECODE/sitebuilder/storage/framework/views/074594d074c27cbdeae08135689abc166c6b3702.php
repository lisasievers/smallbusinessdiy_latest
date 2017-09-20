<?php $__env->startSection('section'); ?>


<div class="col-sm-12 ">
<h3 class="stepscount">You have just <span class="clock">3</span> more steps to complete</h3>
        <?php echo $__env->make('layouts.partials.progress', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?> 
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
          <form role="form" id="sign_form" method="post" action="<?php echo e(route('userwebsite.domain')); ?>" >
          <div class="domaininfo">
          <div class="form-group">
   
            <label class="control-label">Domain Name</label>
            <input maxlength="200" type="text" required="required" name="domain_name" class="form-control domain_url" placeholder="Enter Domain Name">
            
             <input type="hidden" name="_token" value="<?php echo e(Session::token()); ?>">
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
<script src="https://apis.google.com/js/api.js"></script>
 
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>