

  <div class="row">

    <div class="col-md-6">
 <h2 class="diy-color"> Payment Gateway</h2>
 <?php if($message = Session::get('error')): ?>
                <div class="custom-alerts alert alert-danger fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                    <?php echo $message; ?>

                </div>
                <?php Session::forget('error');?>
                <?php endif; ?>
    <form role="form" id="ReportSite" action="<?php echo e(route('user-reports-addition')); ?>" method="post">

      

                <div class="payment-inputs">
                                      
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Payment Details
                    </h3>
                    <!--<div class="checkbox pull-right">
                        <label>
                            <input type="checkbox" />
                            Remember
                        </label>
                    </div>-->
                </div>
                <div class="panel-body">
                    <div class="form-group">

                        <label for="site_name">
                            WEBSITE NAME</label>
                        <div class="form-group<?php echo e($errors->has('site_name') ? ' has-error' : ''); ?>">
                             <input type="hidden" name="_token" value="<?php echo e(Session::token()); ?>">
                             <input id="email" type="hidden" class="form-control" name="email" value="<?php echo e(Session::get('email')); ?>" />  
            
                             <input id="site_name" type="text" class="form-control" placeholder="Website Name" name="site_name" value="<?php echo e(old('site_name')); ?>" autofocus>
                                <?php if($errors->has('site_name')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('site_name')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cardNumber">
                            CARD NUMBER</label>
                        <div class="input-group<?php echo e($errors->has('card_no') ? ' has-error' : ''); ?>">
                             <input id="card_no" type="text" class="form-control" placeholder="Valid Card Number" name="card_no" value="<?php echo e(old('card_no')); ?>" autofocus>
                                <?php if($errors->has('card_no')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('card_no')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-8 col-md-8">
                            <label for="expityMonth">
                                    EXPIRY DATE</label>
                            <div class="form-group">
                                
                                <div class="col-xs-6 col-lg-6 pl-ziro<?php echo e($errors->has('ccExpiryMonth') ? ' has-error' : ''); ?>">
                                    <select id="ccExpiryMonth" class="form-control" name="ccExpiryMonth" autofocus >
                                        <option>Month</option>
                                        <option value="01">Jan (01)</option>
                                        <option value="02">Feb (02)</option>
                                        <option value="03">Mar (03)</option>
                                        <option value="04">Apr (04)</option>
                                        <option value="05">May (05)</option>
                                        <option value="06">June (06)</option>
                                        <option value="07">July (07)</option>
                                        <option value="08">Aug (08)</option>
                                        <option value="09">Sep (09)</option>
                                        <option value="10">Oct (10)</option>
                                        <option value="11">Nov (11)</option>
                                        <option value="12">Dec (12)</option>
                                      </select>
                                      <?php if($errors->has('ccExpiryMonth')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('ccExpiryMonth')); ?></strong>
                                    </span>
                                <?php endif; ?>
                                </div>
                                <div class="col-xs-6 col-lg-6 pl-ziro<?php echo e($errors->has('ccExpiryYear') ? ' has-error' : ''); ?>">
                                    <select id="ccExpiryYear" class="form-control" name="ccExpiryYear" autofocus>
                                            <option value="">Year</option>
                                              <?php
                                                  for($i = 2017; $i < date("Y")+20; $i++){
                                                      echo '<option value="'.$i.'">'.$i.'</option>';
                                                  }
                                            ?>
                                            </select>
                                   <?php if($errors->has('ccExpiryYear')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('ccExpiryYear')); ?></strong>
                                    </span>
                                <?php endif; ?>         
                            </div>
                        </div>
                        </div>
                        <div class="col-xs-4 col-md-4 pull-right">
                            <div class="form-group<?php echo e($errors->has('cvvNumber') ? ' has-error' : ''); ?>">
                                <label for="cvvNumber">
                                    CV CODE</label>
                                <input id="cvvNumber" type="text" class="form-control" name="cvvNumber" value="<?php echo e(old('cvvNumber')); ?>" autofocus>
                                <?php if($errors->has('cvvNumber')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('cvvNumber')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                       <div class="form-group<?php echo e($errors->has('amount') ? ' has-error' : ''); ?>">
                            
                            <div class="col-md-6">
                                <input id="amount" type="hidden" class="form-control" name="amount" value="<?php echo e($ncost); ?>" />
                                <?php if($errors->has('amount')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('amount')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    

                    
                </div>
            </div>
        </div>
            <ul class="nav nav-pills nav-stacked">
                <li class="active"><a href="#"><span class="badge pull-right"><span class="glyphicon glyphicon-usd"></span><?php echo e($ncost); ?></span> Final Payment</a>
                </li>
            </ul>
            <br/>
            <button type="submit" class="btn btn-success btn-lg btn-block">Pay</button>
            </form>
        </div>

                

            </form>

</div>
  <!--<div class="col-md-6">
 <div class="tools-details">
      <h3>DIY Doctor</h3>
      <P>SideDoctor can check your website’s health status within a minute. Follow the suggestion provided by the SiteDoctor and make your site more SEO friendly. </P>
  </div> 

 <div class="tools-details">
      <h3>DIY Spy</h3>
      <P>SideDoctor can check your website’s health status within a minute. Follow the suggestion provided by the SiteDoctor and make your site more SEO friendly. </P>
  </div> 

 <div class="tools-details">
      <h3>DIY Site Review</h3>
      <P>SideDoctor can check your website’s health status within a minute. Follow the suggestion provided by the SiteDoctor and make your site more SEO friendly. </P>
  </div> 

 <div class="tools-details">
      <h3>DIY A2Z SEO</h3>
      <P>SideDoctor can check your website’s health status within a minute. Follow the suggestion provided by the SiteDoctor and make your site more SEO friendly. </P>
  </div> 
  </div> -->
</div>

