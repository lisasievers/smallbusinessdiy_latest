<?php $__env->startSection('section'); ?>
<div class="col-sm-12 ">
<style>
table.table tbody td:nth-child(4){text-align: center;}
</style>
<div class="row">
  <div class="sitelist col-md-12">
<h2 class="page-header"> List of Mail Jobs </h2>
  <table class="table table-bordered  table-striped" id="users-table">
              <thead>
                <tr>
                   <th>Job No.</th>
                  <th>Name of Mail Job</th>
                  <th>Description</th> 
                  <th>Last activity</th> 
                  <th>Trigger Mail</th>
               </tr>
              </thead>
              <tbody>
               
                 <tr>
                  <td>1</td>
                  <td>New User</td>
                  <td>New user, who recently signed in, but not yet to start website builder</td>
                  <td><?php echo e($data['newuser']); ?></td>
                  <td><a class="btn btn-info triggermail" data-val="1" >Send Mail</a></td>
               </tr>
               <tr>
                  <td>2</td>
                  <td>Payment Pending User</td>
                  <td>Member started website builder, but not yet complete their payments</td>
                  <td><?php echo e($data['pendingpay']); ?></td>
                  <td><a class="btn btn-info triggermail" data-val="2" >Send Mail</a></td>
               </tr>
             </tbody>
               </table>
  </div> 
  <div class="col-md-4">
   
  </div>
</div>
  
</div>
   </div>
   <div class="clear"></div>       
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>