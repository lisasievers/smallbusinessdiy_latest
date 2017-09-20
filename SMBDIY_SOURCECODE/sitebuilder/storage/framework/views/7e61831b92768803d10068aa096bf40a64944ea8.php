<div class="row">
<div class="user-container">
 <!-- 
<div class="stepwizard ">
    <div class="stepwizard-row setup-panel">
      <ul><li>
      <div class="stepwizard-step">    
        <a href="#step-1" class="">1</a>

        <p>Domain Registration</p>
      </div></li><li>
      <div class="stepwizard-step">
        <a href="#step-2" type="button" id="s2" class="btn btn-default btn-circle" disabled="disabled">2</a>
        <p>Site Builder</p>
      </div></li><li>
      <div class="stepwizard-step">
        <a href="#step-3" type="button" id="s3" class="btn btn-default btn-circle" disabled="disabled">3</a>
        <p class="doo">Website Builder</p>
      </div></li>
    </ul>
    </div>
  </div> 
   
-->
  <div class="progress">
  <div class="circle stepwizard-step <?php if($data['page']=='Domain Registration'){ echo 'done'; } elseif($data['page']=='Website Build'){ echo 'predone';} elseif($data['page']=='Getstart Website'){ echo 'predone';} ?>">
    <span class="label">1</span>
    <span class="title hladjust1">Domain<span style="color:#fff">|</span>Registration</span>
  </div>
  
  <span class="bar stepwizard-step <?php if($data['page']=='Website Build'){ echo 'done'; } elseif($data['page']=='Getstart Website'){ echo 'predone';}else{echo '';} ?>"></span>
  <div class="circle <?php if($data['page']=='Website Build'){ echo 'done'; } elseif($data['page']=='Getstart Website'){ echo 'predone';}else{echo '';} ?>">
    <span class="label">2</span>
    <span class="title hladjust2">Pick<span style="color:#fff">_</span>a<span style="color:#fff">_</span>plan<span style="color:#fff">_</span>to<span style="color:#fff">_</span>get<span style="color:#fff">_</span>started.</span>
  </div>
 
  <span class="bar stepwizard-step <?php if($data['page']=='Getstart Website'){ echo 'done'; } else{echo '';} ?>"></span>
  <div class="circle <?php if($data['page']=='Getstart Website'){ echo 'done'; } else{echo '';} ?>">
    <span class="label">3</span>
    <span class="title hladjust3">Website<span style="color:#fff">|</span>Builder</span>
  </div>
</div>

 

