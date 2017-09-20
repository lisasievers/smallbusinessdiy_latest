<?php $__env->startSection('section'); ?>
<style>
.domaininfo-need{width: 60%;}
.tab-img-element p{border-bottom:3px dotted #034f84;}
.tab-img-element,.tab-element{margin-top:10px;}
.sbtn{font-size: 9px;color:#ff0000;font-style: italic;}
pre img{max-height: 200px;height: auto;}
pre a{display: block;}
</style>
<div class="col-sm-12"><h2>View about website</h2> </div>

<div id="exTab2" class="col-sm-12"> 
<ul class="nav nav-tabs">
            <li class="active"><a href="#1" data-toggle="tab">Home Page</a>
            </li>
            <li><a href="#2" data-toggle="tab">About Us</a>
            </li>
            <li><a href="#3" data-toggle="tab">Our Team</a>
            </li>
            <li><a href="#4" data-toggle="tab">Products</a>
            </li>
            <li><a href="#5" data-toggle="tab">Services</a>
            </li>
            <li><a href="#6" data-toggle="tab">Contact Us</a>
            </li>
             <li><a href="#7" data-toggle="tab">Footer Type</a>
            </li>
             <li><a href="#8" data-toggle="tab">Blog </a>
            </li>
            <li><a href="#9" data-toggle="tab">Others </a>
            </li>
        </ul>
        <form role="form" id="sign_form" method="post" action="<?php echo e(route('postwebdataform')); ?>" enctype="multipart/form-data" >
           <input type="hidden" name="_token" value="<?php echo e(Session::token()); ?>">
            <input type="hidden" name="form_id" value="<?php echo e($form['id']); ?>">
            <div class="tab-content ">
              <div class="tab-pane active" id="1">
                <div class="tab-element col-sm-6 ">
          <div class="form-group">
            <label class="control-label">Website Name </label><br>
             <pre><?php echo e($form['site_name']); ?></pre>
         </div>
         <div class="form-group">
            <label class="control-label">Website Logo</label><br>
           <?php if($form['website_logo']!=''): ?> 
           <pre><img class="siteimg" src="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['website_logo'])); ?>" alt="logo" />
            <a download href="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['website_logo'])); ?>" alt="logo" />Download</a></pre>
           <?php endif; ?> 
         </div>
         <div class="form-group">
            <label class="control-label">List of Menus</label><br>
            <pre><?php echo e($form['menu_list']); ?></pre>
         </div>
         <div class="form-group">
            <label class="control-label">Home Slider Image-1</label><br>
            <?php if($form['slider1']!=''): ?> 
           <pre><img class="siteimg" src="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['slider1'])); ?>" alt="logo" />
            <a download href="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['slider1'])); ?>" alt="logo" />Download</a></pre>
           <?php endif; ?> 
         </div>
         <div class="form-group">
            <label class="control-label">Slider's image keyword</label>
              <pre><?php echo e($form['slider1_desc']); ?></pre>
         </div>
         <div class="form-group">
            <label class="control-label">Home Slider Image-2</label>
            <?php if($form['slider2']!=''): ?> 
           <pre><img class="siteimg" src="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['slider2'])); ?>" alt="logo" />
            <a download href="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['slider2'])); ?>" alt="logo" />Download</a></pre>
           <?php endif; ?> 
         </div>
         <div class="form-group">
            <label class="control-label">Slider's image keyword</label>
            
            <pre><?php echo e($form['slider2_desc']); ?></pre>
         </div>
         <div class="form-group">
            <label class="control-label">Home Slider Image-3</label>
            <?php if($form['slider3']!=''): ?> 
           <pre><img class="siteimg" src="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['slider3'])); ?>" alt="logo" />
            <a download href="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['slider3'])); ?>" alt="logo" />Download</a></pre>
           <?php endif; ?> 
         </div>
         <div class="form-group">
            <label class="control-label">Slider's image keyword</label>
            
             <pre><?php echo e($form['slider3_desc']); ?></pre>
         </div>
          <div class="form-group">
            <label class="control-label">Slider Contents</label>
             <pre><?php echo e($form['slider_headline']); ?></pre>
         </div>
                </div>
                <div class="tab-img-element col-sm-6 ">
                </div>
            </div>
                <div class="tab-pane" id="2">
          <div class="tab-element col-sm-6 ">
          <div class="form-group">
            <label class="control-label">About Us contents</label>
             <pre><?php echo e($form['about_us_desc']); ?></pre>
         </div>
         <div class="form-group">
            <label class="control-label">About us image</label>
             
             <?php if($form['about_us_img']!=''): ?> 
           <pre><img class="siteimg" src="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['about_us_img'])); ?>" alt="logo" />
            <a download href="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['about_us_img'])); ?>" alt="logo" />Download</a></pre>
           <?php endif; ?> 
         </div>

                </div>
                <div class="tab-img-element col-sm-6 ">
                </div>
                </div>
        <div class="tab-pane" id="3"><!--our team -->
          <div class="tab-element col-sm-6 ">
             <div class="form-group">
            <label class="control-label">Our Team Headline</label>
             <pre><?php echo e($form['team_headline']); ?></pre>
         </div>
          <div class="form-group">
            <label class="control-label">Our Team Image-1</label>
             
            <?php if($form['team1_img']!=''): ?> 
           <pre><img class="siteimg" src="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['team1_img'])); ?>" alt="logo" />
            <a download href="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['team1_img'])); ?>" alt="logo" />Download</a></pre>
           <?php endif; ?> 
         </div>
         <div class="form-group">
            <label class="control-label">Content-1 </label>
            <span>( Name, Description and other )</span>
            <pre><?php echo e($form['team1_desc']); ?></pre>
         </div>
         <div class="form-group">
            <label class="control-label">Our Team Image-2</label>
            <?php if($form['team2_img']!=''): ?> 
           <pre><img class="siteimg" src="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['team2_img'])); ?>" alt="logo" />
            <a download href="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['team2_img'])); ?>" alt="logo" />Download</a></pre>
           <?php endif; ?> 
         </div>
         <div class="form-group">
            <label class="control-label">Content-2</label>
            <pre><?php echo e($form['team2_desc']); ?></pre>
         </div>
         <div class="form-group">
            <label class="control-label">Our Team Image-3</label>
             <?php if($form['team3_img']!=''): ?> 
           <pre><img class="siteimg" src="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['team3_img'])); ?>" alt="logo" />
            <a download href="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['team3_img'])); ?>" alt="logo" />Download</a></pre>
           <?php endif; ?> 
         </div>
         <div class="form-group">
            <label class="control-label">Content-3</label>
            <pre><?php echo e($form['team3_desc']); ?></pre>
         </div>
         <div class="form-group">
            <label class="control-label">Our Team Image-4</label>
             <?php if($form['team4_img']!=''): ?> 
           <pre><img class="siteimg" src="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['team4_img'])); ?>" alt="logo" />
            <a download href="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['team4_img'])); ?>" alt="logo" />Download</a></pre>
           <?php endif; ?> 
         </div>
         <div class="form-group">
            <label class="control-label">Content-4</label>
            <pre><?php echo e($form['team4_desc']); ?></pre>
         </div>
         <div class="form-group">
            <label class="control-label">Our Team Image-5</label>
             <?php if($form['team5_img']!=''): ?> 
           <pre><img class="siteimg" src="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['team5_img'])); ?>" alt="logo" />
            <a download href="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['team5_img'])); ?>" alt="logo" />Download</a></pre>
           <?php endif; ?> 
         </div>
         <div class="form-group">
            <label class="control-label">Content-5</label>
            <pre><?php echo e($form['team5_desc']); ?></pre>
         </div>
                </div>
                <div class="tab-img-element col-sm-6 ">
                 
                </div>
                </div>
        <div class="tab-pane" id="4"><!--products-->
          <div class="tab-element col-sm-6 ">
            <div class="form-group">
            <label class="control-label">Our Products Headline</label>
             <pre><?php echo e($form['product_headline']); ?></pre>
         </div>
          <div class="form-group">
            <label class="control-label">Our Products Image-1</label>
             <?php if($form['product1_img']!=''): ?> 
           <pre><img class="siteimg" src="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['product1_img'])); ?>" alt="logo" />
            <a download href="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['product1_img'])); ?>" alt="logo" />Download</a></pre>
           <?php endif; ?> 
         </div>
         <div class="form-group">
            <label class="control-label">Content-1</label>
            <pre><?php echo e($form['product1_desc']); ?></pre>
         </div>
         <div class="form-group">
            <label class="control-label">Products Image-2</label>
             <?php if($form['product2_img']!=''): ?> 
           <pre><img class="siteimg" src="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['product2_img'])); ?>" alt="logo" />
            <a download href="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['product2_img'])); ?>" alt="logo" />Download</a></pre>
           <?php endif; ?> 
         </div>
         <div class="form-group">
            <label class="control-label">Content-2</label>
            <pre><?php echo e($form['product2_desc']); ?></pre>
         </div>
         <div class="form-group">
            <label class="control-label">Products Image-3</label>
             <?php if($form['product3_img']!=''): ?> 
           <pre><img class="siteimg" src="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['product3_img'])); ?>" alt="logo" />
            <a download href="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['product3_img'])); ?>" alt="logo" />Download</a></pre>
           <?php endif; ?> 
         </div>
         <div class="form-group">
            <label class="control-label">Content-3</label>
            <pre><?php echo e($form['product3_desc']); ?></pre>
         </div>
         <div class="form-group">
            <label class="control-label">Products Image-4</label>
             <?php if($form['product4_img']!=''): ?> 
           <pre><img class="siteimg" src="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['product4_img'])); ?>" alt="logo" />
            <a download href="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['product4_img'])); ?>" alt="logo" />Download</a></pre>
           <?php endif; ?> 
         </div>
         <div class="form-group">
            <label class="control-label">Content-4</label>
            <pre><?php echo e($form['product4_desc']); ?></pre>
         </div>
         <div class="form-group">
            <label class="control-label">Products Image-5</label>
             <?php if($form['product5_img']!=''): ?> 
           <pre><img class="siteimg" src="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['product5_img'])); ?>" alt="logo" />
            <a download href="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['product5_img'])); ?>" alt="logo" />Download</a></pre>
           <?php endif; ?> 
         </div>
         <div class="form-group">
            <label class="control-label">Content-5</label>
            <pre><?php echo e($form['product5_desc']); ?></pre>
         </div>
                </div>
                <div class="tab-img-element col-sm-6 ">
                  
                </div>
                </div> 
           <div class="tab-pane" id="5"><!--Services -->
          <div class="tab-element col-sm-6 ">
            <div class="form-group">
            <label class="control-label">Our Services Headline</label>
             <pre><?php echo e($form['service_headline']); ?></pre>
         </div>
          <div class="form-group">
            <label class="control-label">Our Services Image-1</label>
             <?php if($form['service1_img']!=''): ?> 
           <pre><img class="siteimg" src="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['service1_img'])); ?>" alt="logo" />
            <a download href="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['service1_img'])); ?>" alt="logo" />Download</a></pre>
           <?php endif; ?> 
         </div>
         <div class="form-group">
            <label class="control-label">Content-1</label>
            <pre><?php echo e($form['service1_desc']); ?></pre>
         </div>
         <div class="form-group">
            <label class="control-label">Services Image-2</label>
             <?php if($form['service2_img']!=''): ?> 
           <pre><img class="siteimg" src="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['service2_img'])); ?>" alt="logo" />
            <a download href="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['service2_img'])); ?>" alt="logo" />Download</a></pre>
           <?php endif; ?> 
         </div>
         <div class="form-group">
            <label class="control-label">Content-2</label>
            <pre><?php echo e($form['service2_desc']); ?></pre>
         </div>
         <div class="form-group">
            <label class="control-label">Services Image-3</label>
             <?php if($form['service3_img']!=''): ?> 
           <pre><img class="siteimg" src="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['service3_img'])); ?>" alt="logo" />
            <a download href="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['service3_img'])); ?>" alt="logo" />Download</a></pre>
           <?php endif; ?> 
         </div>
         <div class="form-group">
            <label class="control-label">Content-3</label>
            <pre><?php echo e($form['service3_desc']); ?></pre>
         </div>
         <div class="form-group">
            <label class="control-label">Services Image-4</label>
             <?php if($form['service4_img']!=''): ?> 
           <pre><img class="siteimg" src="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['service4_img'])); ?>" alt="logo" />
            <a download href="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['service4_img'])); ?>" alt="logo" />Download</a></pre>
           <?php endif; ?> 
         </div>
         <div class="form-group">
            <label class="control-label">Content-4</label>
            <pre><?php echo e($form['service4_desc']); ?></pre>
         </div>
         <div class="form-group">
            <label class="control-label">Services Image-5</label>
             <?php if($form['service5_img']!=''): ?> 
           <pre><img class="siteimg" src="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['service5_img'])); ?>" alt="logo" />
            <a download href="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['service5_img'])); ?>" alt="logo" />Download</a></pre>
           <?php endif; ?> 
         </div>
         <div class="form-group">
            <label class="control-label">Content-5</label>
            <pre><?php echo e($form['service5_desc']); ?></pre>
         </div>
                </div>
                <div class="tab-img-element col-sm-6 ">
                    
                 </div>
                </div>   
               <div class="tab-pane" id="6"><!--contact us-->
          <div class="tab-element col-sm-6 ">
          <div class="form-group">
            <label class="control-label">Company Address</label>
             <pre><?php echo e($form['company_address']); ?></pre>
         </div>
         <div class="form-group">
            <label class="control-label">Phone Number</label>
             <pre><?php echo e($form['phone_number']); ?></pre>
         </div>
         <div class="form-group">
            <label class="control-label">Email Address</label>
             <pre><?php echo e($form['email_address']); ?></pre>
         </div>
         <div class="form-group">
            <label class="control-label">Contents</label>
            <pre><?php echo e($form['contact_desc']); ?></pre>
         </div>
         
                </div>
                <div class="tab-img-element col-sm-6 ">
                </div>
                </div>  
                <div class="tab-pane" id="7"><!--Footer-->
          <div class="tab-element col-sm-6 ">
          <div class="form-group">
            <p class="control-label">Select Footer Type</p><br>
             <input type="radio" name="footer_type" value="footer1.png" id="f1" <?php if($form['footer_type']=='footer1.png'): ?>{ checked } <?php endif; ?> > <label for="f1"><img class="sitbuild-screen" src="<?php echo e(url('elements/images/thumbs/footer1.png')); ?>" alt="sitebuilder" /></label><br>
             <input type="radio" name="footer_type" value="footer2.png" id="f2" <?php if($form['footer_type']=='footer2.png'): ?>{ checked } <?php endif; ?> > <label for="f2"><img class="sitbuild-screen" src="<?php echo e(url('elements/images/thumbs/footer2.png')); ?>" alt="sitebuilder" /></label><br>
             <input type="radio" name="footer_type" value="footer3.png" id="f3" <?php if($form['footer_type']=='footer3.png'): ?>{ checked } <?php endif; ?> > <label for="f3"><img class="sitbuild-screen" src="<?php echo e(url('elements/images/thumbs/footer3.png')); ?>" alt="sitebuilder" /></label><br>
         </div>
         
         <div class="form-group">
            <label class="control-label">Contents</label>
            <pre><?php echo e($form['footer_desc']); ?></pre>
         </div>
         <div class="form-group">
            <label class="control-label">Social Media Links</label>
            <pre><?php echo e($form['social_media']); ?></pre>
         </div>
         
                </div>
                <div class="tab-img-element col-sm-6 ">
                    </div>
                </div>
                <div class="tab-pane" id="8"><!--Blog-->
          <div class="tab-element col-sm-6 ">
          <div class="form-group">
            <label class="control-label">Blog-1 Title</label>
            <pre><?php echo e($form['blog1_title']); ?></pre>
             
         </div>
         <div class="form-group">
            <label class="control-label">Blog Description</label>
             <pre><?php echo e($form['blog1_desc']); ?></pre>
         </div>
         <div class="form-group">
            <label class="control-label">Blog Image</label>
             
             <?php if($form['blog1_img']!=''): ?> 
           <pre><img class="siteimg" src="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['blog1_img'])); ?>" alt="logo" />
            <a download href="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['blog1_img'])); ?>" alt="logo" />Download</a></pre>
           <?php endif; ?> 
         </div>
           <div class="form-group">
            <label class="control-label">Blog-2 Title</label>
            <pre><?php echo e($form['blog2_title']); ?></pre>
             
         </div>
         <div class="form-group">
            <label class="control-label">Blog Description</label>
             <pre><?php echo e($form['blog2_desc']); ?></pre>
         </div>
         <div class="form-group">
            <label class="control-label">Blog Image</label>
              <?php if($form['blog2_img']!=''): ?> 
           <pre><img class="siteimg" src="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['blog2_img'])); ?>" alt="logo" />
            <a download href="<?php echo e(url('src/uploads/doitforme/'.$form['id'].'/'.$form['blog2_img'])); ?>" alt="logo" />Download</a></pre>
           <?php endif; ?> 
         </div>
         
                </div>
                <div class="tab-img-element col-sm-6 ">
                    
                    </div>
                </div>   
             <div class="tab-pane" id="9"><!--Other-->
          <div class="tab-element col-sm-6 ">
          <div class="form-group">
            <label class="control-label">Other Informations</label>
            <pre><?php echo e($form['other_desc']); ?></pre>
             
         </div>
         
         </div>
         <div class="tab-img-element col-sm-6 ">
                    
                    </div>
         </div>         
            </div><!-- tab content -->

        </form>
  </div>



   </div>
   <div class="clear"></div>       
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>