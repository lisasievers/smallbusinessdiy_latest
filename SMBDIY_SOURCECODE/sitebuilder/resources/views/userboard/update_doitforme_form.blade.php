@extends ('layouts.dashboard')

@section('section')
<style>
.domaininfo-need{width: 60%;}
.tab-img-element p{border-bottom:3px dotted #034f84;}
.tab-img-element,.tab-element{margin-top:10px;}

</style>
<div class="col-sm-12"><h2>Update your form about website</h2> </div>

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
            <li><a href="#9" data-toggle="tab">Additional Info. <span class="sbtn">Submit</span></a>
            </li>
        </ul>
        <form role="form" id="sign_form" method="post" action="{{ route('postwebdataform') }}" enctype="multipart/form-data" >
           <input type="hidden" name="_token" value="{{ Session::token() }}">
            <input type="hidden" name="form_id" value="{{ $form['id']}}">
            <div class="tab-content ">
              <div class="tab-pane active" id="1">
                <div class="tab-element col-sm-6 ">
          <div class="form-group">
            <label class="control-label">Website Name *</label>
             <input type="text" required="required" name="site_name" class="form-control webname" value="{{ $form['site_name']}}" />
         </div>
         <div class="form-group">
            <label class="control-label">Website Logo</label>
            <p><img class="siteimg" src="{{url('src/uploads/doitforme/'.$form['id'].'/BTN.jpg')}}" alt="logo" /></p>
            <input type="file" name="website_logo" class="form-control" value="{{ $form['website_logo']}}"/>
         </div>
         <div class="form-group">
            <label class="control-label">List of Menus (use comma)</label>
            <input type="text" name="menu_list" class="form-control" value="{{ $form['menu_list']}}"/>
         </div>
         <div class="form-group">
            <label class="control-label">Home Slider Image-1</label>
            <input type="file" name="slider1" />
         </div>
         <div class="form-group">
            <label class="control-label">Slider's image keyword</label>
            <span>( It may describes your business, product or service )</span>
             <input type="text" name="slider1_desc" class="form-control" value="{{ $form['slider1_desc']}}"/>
         </div>
         <div class="form-group">
            <label class="control-label">Home Slider Image-2</label>
            <input type="file" name="slider2" />
         </div>
         <div class="form-group">
            <label class="control-label">Slider's image keyword</label>
            
             <input type="text" name="slider2_desc" class="form-control" value="{{ $form['slider2_desc']}}"/>
         </div>
         <div class="form-group">
            <label class="control-label">Home Slider Image-3</label>
            <a download href="{{url('src/uploads/doitforme/'.$form['id'].'/BTN.jpg')}}" alt="logo" />Click to download</a>
            <input type="file" name="slider3" />
         </div>
         <div class="form-group">
            <label class="control-label">Slider's image keyword</label>
            
             <input type="text" name="slider3_desc" class="form-control" value="{{ $form['slider3_desc']}}" />
         </div>
          <div class="form-group">
            <label class="control-label">Slider Contents</label>
             <textarea name="slider_headline" class="form-control">{{ $form['slider_headline']}}</textarea>
         </div>
                </div>
                <div class="tab-img-element col-sm-6 ">
                    <p><img class="sitbuild-screen webnameimg" src="{{url('elements/images/thumbs/header1.png')}}" alt="sitebuilder" /></p>
                    <p><img class="sitbuild-screen" src="{{url('elements/images/thumbs/header2.png')}}" alt="sitebuilder" /></p>
                    <p><img class="sitbuild-screen" src="{{url('elements/images/thumbs/header3.png')}}" alt="sitebuilder" /></p>
                </div>
            </div>
                <div class="tab-pane" id="2">
          <div class="tab-element col-sm-6 ">
          <div class="form-group">
            <label class="control-label">About Us contents</label>
             <textarea name="about_us_desc" class="form-control">{{ $form['about_us_desc']}}</textarea>
         </div>
         <div class="form-group">
            <label class="control-label">About us image</label>
             <input type="file" name="about_us_img" />
         </div>

                </div>
                <div class="tab-img-element col-sm-6 ">
                    <p><img class="sitbuild-screen" src="{{url('elements/images/thumbs/content_section5.png')}}" alt="sitebuilder" /></p>
                 </div>
                </div>
        <div class="tab-pane" id="3"><!--our team -->
          <div class="tab-element col-sm-6 ">
             <div class="form-group">
            <label class="control-label">Our Team Headline</label>
             <input type="text" name="team_headline" class="form-control" value="{{ $form['team_headline']}}" />
         </div>
          <div class="form-group">
            <label class="control-label">Our Team Image-1</label>
             <input type="file" name="team1_img" />
         </div>
         <div class="form-group">
            <label class="control-label">Content-1 </label>
            <span>( Name, Description and other )</span>
            <textarea name="team1_desc" class="form-control">{{ $form['team1_desc']}}</textarea>
         </div>
         <div class="form-group">
            <label class="control-label">Our Team Image-2</label>
             <input type="file" name="team2_img" />
         </div>
         <div class="form-group">
            <label class="control-label">Content-2</label>
            <textarea name="team2_desc" class="form-control">{{ $form['team2_desc']}}</textarea>
         </div>
         <div class="form-group">
            <label class="control-label">Our Team Image-3</label>
             <input type="file" name="team3_img" />
         </div>
         <div class="form-group">
            <label class="control-label">Content-3</label>
            <textarea name="team3_desc" class="form-control">{{ $form['team3_desc']}}</textarea>
         </div>
         <div class="form-group">
            <label class="control-label">Our Team Image-4</label>
             <input type="file" name="team4_img" />
         </div>
         <div class="form-group">
            <label class="control-label">Content-4</label>
            <textarea name="team4_desc" class="form-control">{{ $form['team4_desc']}}</textarea>
         </div>
         <div class="form-group">
            <label class="control-label">Our Team Image-5</label>
             <input type="file" name="team5_img" />
         </div>
         <div class="form-group">
            <label class="control-label">Content-5</label>
            <textarea name="team5_desc" class="form-control">{{ $form['team5_desc']}}</textarea>
         </div>
                </div>
                <div class="tab-img-element col-sm-6 ">
                    <p><img class="sitbuild-screen" src="{{url('elements/images/thumbs/team1.png')}}" alt="sitebuilder" /></p>
                </div>
                </div>
        <div class="tab-pane" id="4"><!--products-->
          <div class="tab-element col-sm-6 ">
            <div class="form-group">
            <label class="control-label">Our Products Headline</label>
             <input type="text" name="product_headline" class="form-control" value="{{ $form['product_headline']}}" />
         </div>
          <div class="form-group">
            <label class="control-label">Our Products Image-1</label>
             <input type="file" name="product1_img" />
         </div>
         <div class="form-group">
            <label class="control-label">Content-1</label>
            <textarea name="product1_desc" class="form-control">{{ $form['product1_desc']}}</textarea>
         </div>
         <div class="form-group">
            <label class="control-label">Products Image-2</label>
             <input type="file" name="product2_img" />
         </div>
         <div class="form-group">
            <label class="control-label">Content-2</label>
            <textarea name="product2_desc" class="form-control">{{ $form['product2_desc']}}</textarea>
         </div>
         <div class="form-group">
            <label class="control-label">Products Image-3</label>
             <input type="file" name="product3_img" />
         </div>
         <div class="form-group">
            <label class="control-label">Content-3</label>
            <textarea name="product3_desc" class="form-control">{{ $form['product3_desc']}}</textarea>
         </div>
         <div class="form-group">
            <label class="control-label">Products Image-4</label>
             <input type="file" name="product4_img" />
         </div>
         <div class="form-group">
            <label class="control-label">Content-4</label>
            <textarea name="product4_desc" class="form-control">{{ $form['product4_desc']}}</textarea>
         </div>
         <div class="form-group">
            <label class="control-label">Products Image-5</label>
             <input type="file" name="product5_img" />
         </div>
         <div class="form-group">
            <label class="control-label">Content-5</label>
            <textarea name="product5_desc" class="form-control">{{ $form['product5_desc']}}</textarea>
         </div>
                </div>
                <div class="tab-img-element col-sm-6 ">
                    <p><img class="sitbuild-screen" src="{{url('elements/images/thumbs/portfolio3.png')}}" alt="sitebuilder" /></p>
                </div>
                </div> 
           <div class="tab-pane" id="5"><!--Services -->
          <div class="tab-element col-sm-6 ">
            <div class="form-group">
            <label class="control-label">Our Services Headline</label>
             <input type="text" name="service_headline" class="form-control" value="{{ $form['service_headline']}}"/>
         </div>
          <div class="form-group">
            <label class="control-label">Our Services Image-1</label>
             <input type="file" name="service1_img" />
         </div>
         <div class="form-group">
            <label class="control-label">Content-1</label>
            <textarea name="service1_desc" class="form-control">{{ $form['service1_desc']}}</textarea>
         </div>
         <div class="form-group">
            <label class="control-label">Services Image-2</label>
             <input type="file" name="service2_img" />
         </div>
         <div class="form-group">
            <label class="control-label">Content-2</label>
            <textarea name="service2_desc" class="form-control">{{ $form['service2_desc']}}</textarea>
         </div>
         <div class="form-group">
            <label class="control-label">Services Image-3</label>
             <input type="file" name="service3_img" />
         </div>
         <div class="form-group">
            <label class="control-label">Content-3</label>
            <textarea name="service3_desc" class="form-control">{{ $form['service3_desc']}}</textarea>
         </div>
         <div class="form-group">
            <label class="control-label">Services Image-4</label>
             <input type="file" name="service4_img" />
         </div>
         <div class="form-group">
            <label class="control-label">Content-4</label>
            <textarea name="service4_desc" class="form-control">{{ $form['service4_desc']}}</textarea>
         </div>
         <div class="form-group">
            <label class="control-label">Services Image-5</label>
             <input type="file" name="service5_img" />
         </div>
         <div class="form-group">
            <label class="control-label">Content-5</label>
            <textarea name="service5_desc" class="form-control">{{ $form['service5_desc']}}</textarea>
         </div>
                </div>
                <div class="tab-img-element col-sm-6 ">
                    <p><img class="sitbuild-screen" src="{{url('elements/images/thumbs/portfolio1.png')}}" alt="sitebuilder" /></p>
                 </div>
                </div>   
               <div class="tab-pane" id="6"><!--contact us-->
          <div class="tab-element col-sm-6 ">
          <div class="form-group">
            <label class="control-label">Company Address</label>
             <textarea name="company_address" class="form-control">{{ $form['company_address']}}</textarea>
         </div>
         <div class="form-group">
            <label class="control-label">Phone Number</label>
             <input type="text" class="form-control" name="phone_number" value="{{ $form['phone_number']}}" />
         </div>
         <div class="form-group">
            <label class="control-label">Email Address</label>
             <input type="text" class="form-control" name="email_address" value="{{ $form['email_address']}}" />
         </div>
         <div class="form-group">
            <label class="control-label">Contents</label>
            <textarea name="contact_desc" class="form-control">{{ $form['contact_desc']}}</textarea>
         </div>
         
                </div>
                <div class="tab-img-element col-sm-6 ">
                    <p><img class="sitbuild-screen" src="{{url('elements/images/thumbs/contact1.png')}}" alt="sitebuilder" /></p>
                    <p><img class="sitbuild-screen" src="{{url('elements/images/thumbs/contact2.png')}}" alt="sitebuilder" /></p>
                   </div>
                </div>  
                <div class="tab-pane" id="7"><!--Footer-->
          <div class="tab-element col-sm-6 ">
          <div class="form-group">
            <p class="control-label">Select Footer Type</p><br>
             <input type="radio" name="footer_type" value="footer1.png" id="f1" @if($form['footer_type']=='footer1.png'){ checked } @endif > <label for="f1"><img class="sitbuild-screen" src="{{url('elements/images/thumbs/footer1.png')}}" alt="sitebuilder" /></label><br>
             <input type="radio" name="footer_type" value="footer2.png" id="f2" @if($form['footer_type']=='footer2.png'){ checked } @endif > <label for="f2"><img class="sitbuild-screen" src="{{url('elements/images/thumbs/footer2.png')}}" alt="sitebuilder" /></label><br>
             <input type="radio" name="footer_type" value="footer3.png" id="f3" @if($form['footer_type']=='footer3.png'){ checked } @endif > <label for="f3"><img class="sitbuild-screen" src="{{url('elements/images/thumbs/footer3.png')}}" alt="sitebuilder" /></label><br>
         </div>
         
         <div class="form-group">
            <label class="control-label">Contents</label>
            <textarea name="footer_desc" class="form-control">{{ $form['footer_desc']}}</textarea>
         </div>
         <div class="form-group">
            <label class="control-label">Social Media Links (Facebook,Twitter,Google+ ...)</label>
            <textarea name="social_media" class="form-control" placeholder="Facebook,Twitter,Google+ ...">{{ $form['social_media']}}</textarea>
         </div>
         
                </div>
                <div class="tab-img-element col-sm-6 ">
                    </div>
                </div>
                <div class="tab-pane" id="8"><!--Blog-->
          <div class="tab-element col-sm-6 ">
          <div class="form-group">
            <label class="control-label">Blog-1 Title</label>
            <input type="text" class="form-control" name="blog1_title" value="{{ $form['blog1_title']}}" />
             
         </div>
         <div class="form-group">
            <label class="control-label">Blog Description</label>
             <textarea name="blog1_desc" class="form-control">{{ $form['blog1_desc']}}</textarea>
         </div>
         <div class="form-group">
            <label class="control-label">Blog Image</label>
             <input type="file" class="form-control" name="blog1_img" />
         </div>
           <div class="form-group">
            <label class="control-label">Blog-2 Title</label>
            <input type="text" class="form-control" name="blog2_title" value="{{ $form['blog2_title']}}"/>
             
         </div>
         <div class="form-group">
            <label class="control-label">Blog Description</label>
             <textarea name="blog2_desc" class="form-control">{{ $form['blog2_desc']}}</textarea>
         </div>
         <div class="form-group">
            <label class="control-label">Blog Image</label>
             <input type="file" class="form-control" name="blog2_img" />
         </div>
         
                </div>
                <div class="tab-img-element col-sm-6 ">
                    <p><img class="sitbuild-screen" src="{{url('elements/images/thumbs/contact1.png')}}" alt="sitebuilder" /></p>
                    </div>
                </div>   
             <div class="tab-pane" id="9"><!--Other-->
          <div class="tab-element col-sm-6 ">
          <div class="form-group">
            <label class="control-label">Other Informations</label>
            <textarea name="other_desc" class="form-control">{{ $form['other_desc']}}</textarea>
             
         </div>
         <div class="form-group">
            <label class="control-label"></label>
             <input type="submit" class="form-control btn btn-success" name="home_title" value="Final Submit"/>
         </div>
         </div>
         <div class="tab-img-element col-sm-6 ">
                    <p><img class="sitbuild-screen" src="{{url('elements/images/thumbs/content_section11.png')}}" alt="sitebuilder" /></p>
                    </div>
         </div>         
            </div><!-- tab content -->

        </form>
  </div>



   </div>
   <div class="clear"></div>       
</div>

@stop
