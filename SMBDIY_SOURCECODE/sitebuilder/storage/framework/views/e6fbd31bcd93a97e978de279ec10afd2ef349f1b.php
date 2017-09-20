<?php $__env->startSection('section'); ?>
<div class="col-sm-12 ">
<style>
table.table tbody td:nth-child(4){text-align: right;width:12%;}
</style>
<div class="row">
  <div class="sitelist col-md-8">
<h2 class="page-header"> List of Category for Website Template </h2>
  <table class="table table-bordered  table-striped" id="users-table">
              <thead>
                <tr>
                   <th>Sl. No.</th>
                  <th>Name of Category</th>
                   
               </tr>
              </thead>
              <tbody>
               
                <?php foreach($data['packages'] as $key => $usc): ?>
                 <tr>
                  <td><?php echo e($key+1); ?></td>
                    <td><?php echo e($usc['name']); ?></td>
                    
                    
               </tr>
               <?php endforeach; ?>
            </table>
  </div> 
  <div class="col-md-4">
    <h2 class="page-header">Add a New Category</h2>
        <form role="form" id="ProSettings" action="<?php echo e(route('templates')); ?>" method="post" enctype="multipart/form-data">
          
                <div class="form-group <?php echo e($errors->has('site_name') ? 'has-error' : ''); ?>">
                    <b>Enter Category Name:</b><br>
                    
                    <input type="text" class="form-control" id="site_name" name="site_name"  placeholder="Category name *" />
                </div>
                

                
                <input type="hidden" name="_token" value="<?php echo e(Session::token()); ?>">
               
                <div class="signbtn-group input-group">
                    <button type="submit" class="btn btn-primary btn-block btn-embossed" id="choosesubmit" > Submit </span></button>
                 </div>  
                

            </form>
  </div>
</div>
  
</div>
   </div>
   <div class="clear"></div>       
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>