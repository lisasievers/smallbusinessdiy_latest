<?php $__env->startSection('section'); ?>
<div class="col-sm-12 ">
<style>
table.table tbody td:nth-child(4){text-align: right;width:12%;}
</style>
<div class="row">
  <div class="sitelist col-md-10">
<h2 class="page-header"> My Payments </h2>
  <table class="table table-bordered  table-striped" id="users-table">
              <thead>
                <tr>
                  <th>Payment Ref. No.</th>
                    <th>Function A/c</th>
                    
                    <th>Paid date</th>
                    <th>Paid Amt. ($)</th>
               </tr>
              </thead>
              <tbody>
               
                <?php foreach($data['user_sub'] as $usc): ?>
                 <tr>
                  <td><?php echo e($usc->stripeToken); ?></td>
                    <td><b><?php echo e($usc->name); ?></b> Subscription - Website Reports</td>
                    
                    <td><?php echo e($usc->date_time); ?></td>
                    <td><?php echo e($usc->amount); ?></td>
               </tr>
               <span style="visibility:hidden"><?php echo e($sum=$sum+$usc->amount); ?></span>
                <?php endforeach; ?>
                <?php foreach($data['user_sites'] as $usc): ?>
                 <tr>
                  <td><?php echo e($usc->stripeToken); ?></td>
                    <td><b><?php echo e($usc->site_name); ?></b> Website Builder</td>
                    
                    <td><?php echo e($usc->date_time); ?></td>
                    <td><?php echo e($usc->amount); ?></td>
               </tr>
             <span style="visibility:hidden"><?php echo e($sum=$sum+$usc->amount); ?></span>
                <?php endforeach; ?>
              </tbody>
              
              <tfoot>
                  <tr>
                <td></td><td></td><td>Grand Total ($)</td><td><?php echo e($sum); ?></td>
              </tr>
              </tfoot>
            </table>


  </div> 
</div>
  
</div>
   </div>
   <div class="clear"></div>       
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>