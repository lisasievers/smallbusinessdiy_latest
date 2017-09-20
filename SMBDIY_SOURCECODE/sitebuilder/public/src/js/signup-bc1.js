$(document).ready(function () {
 var navListItems = $('div.setup-panel div a'),
          allWells = $('.setup-content'),
          allNextBtn = $('.nextBtn'),
           allDoBtn = $('.DoBtn'),
           prevDoBtn = $('.prevDoBtn'),
           prevSubBtn = $('.prevSubBtn'),
        allPrevBtn = $('.prevBtn');

  allWells.hide();

  navListItems.click(function (e) {
      e.preventDefault();
      var $target = $($(this).attr('href')),
              $item = $(this);

      if (!$item.hasClass('disabled')) {
          navListItems.removeClass('btn-primary').addClass('btn-default');
          $item.addClass('btn-primary');
          allWells.hide();
          $target.show();
          $target.find('input:eq(0)').focus();
      }
  });
  
  allPrevBtn.click(function(){
      var curStep = $(this).closest(".setup-content"),
          curStepBtn = curStep.attr("id"),
          prevStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().prev().children("a");

          prevStepWizard.removeAttr('disabled').trigger('click');
  });

  allNextBtn.click(function(){
    $('#s1').addClass('btn-success');
    $('.clock').html('2');
    //toastr.info('You will shortly redirect to Sitebuilder!');
      var curStep = $(this).closest(".setup-content"),
          curStepBtn = curStep.attr("id"),
          nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
          curInputs = curStep.find("input[type='text'],input[type='email'],input[type='url']"),
          isValid = true;

      $(".form-group").removeClass("has-error");
      for(var i=0; i<curInputs.length; i++){
          if (!curInputs[i].validity.valid){
              isValid = false;
              $(curInputs[i]).closest(".form-group").addClass("has-error");
          }
      }

      if (isValid)
          nextStepWizard.removeAttr('disabled').trigger('click');
  });

  allDoBtn.click(function(){
    $('#s2').addClass('btn-success');
    $('.clock').html('1');
   var whodo=$('input[name=whodo]:checked').val();
   var domainurl=$('.domain_url').val();
   console.log(whodo);
   
     
      var curStep = $(this).closest(".setup-content"),
          curStepBtn = curStep.attr("id"),
          nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
          curInputs = curStep.find("input[type='text'],input[type='radio'],input[type='url']"),
          isValid = true;

      $(".form-group").removeClass("has-error");
      for(var i=0; i<curInputs.length; i++){
          if (!curInputs[i].validity.valid){
              isValid = false;
              $(curInputs[i]).closest(".form-group").addClass("has-error");
          }
      }

      if (isValid)
          nextStepWizard.removeAttr('disabled').trigger('click');

  if(whodo=='iwill'){ 
    $('.domaininfo-need').hide(); 
    $('.lastbtn').hide();
    $('.doo').html('Website Builder');
    $('.dod').html('Website Builder');
    $('.sitebuild').show();
    toastr.info('You will shortly redirect to Sitebuilder!');
    $('#s3').addClass('btn-success');
    iwilldoit();
    }
   else{
    $('.domaininfo-need').show();
    $('.sitebuild').hide();
    $('.doo').html('Do it for me');
    $('.dod').html('Do it for me');
    $('.webname').val(domainurl);
    $('.lastbtn').show();
   // doitforme(); 
   //window.location.href='userwebsitedoit';
  }
      
  });

prevDoBtn.click(function(){
    $('#s1').removeClass('btn-success');
    $('.clock').html('3');
var curStep = $(this).closest(".setup-content"),
          curStepBtn = curStep.attr("id"),
          prevStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().prev().children("a");

          prevStepWizard.removeAttr('disabled').trigger('click');
});

prevSubBtn.click(function(){
    $('#s2').removeClass('btn-success');
      $('#s3').removeClass('btn-success');
      $('.clock').html('2');
      $('.submit').show();
      $('.payinfo').hide();
var curStep = $(this).closest(".setup-content"),
          curStepBtn = curStep.attr("id"),
          prevStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().prev().children("a");

          prevStepWizard.removeAttr('disabled').trigger('click');
});

$('div.setup-panel div a.btn-primary').trigger('click');

//Domain check condition
$(':checkbox').checkboxpicker();
$('#input-2').change(function(){
 // console.log('red');
  var wh=$(this).val();
   console.log(wh);
  if(wh=='yes'){
    $('#input-2').attr('value','no');
    $('.domaininfo').hide();
    $('.domaininfo input').removeAttr('required','');
  }
  else
  {
    $('#input-2').attr('value','yes');
    $('.domaininfo').show();
    $('.domaininfo input').attr('required','required');
  }

  });

//Do it for me submission
$(".submit").click(function(){
  $('#s3').addClass('btn-success');
  $('.clock').html('1');
 // doitforme();
  $('.domaininfo-need').hide();
  $('.payinfo').show();
  //$('.paydomain').val(domainurl);
  document.getElementById("sign_form").submit();

//$('.sitebuild').html('Thank you for submitting, your website activated within 48 hours!');
//toastr.info('Thank you for submitting, your website activated within 48 hours!');
//return false;
});
/*$('.paysubmit').click(function(){
  event.preventDefault();
  $('#s3').addClass('btn-success');
  $('.clock').html('1');
  doitformepay();
  $('.domaininfo-need').hide();
  $('.payinfo').show();
  var domainurl=$('.webname').val();
  $('.paydomain').val(domainurl);
//$('.sitebuild').html('Thank you for submitting, your website activated within 48 hours!');
//toastr.info('Thank you for submitting, your website activated within 48 hours!');
//return false;
});
*/
});

function reject_delay(){
var delay=3000;//3 seconds
setTimeout(function(){
  window.location.href='site-create';
},delay);
}

function doitforme(){
   // var formData = new FormData($(this)[0]);
    var imageFile = new FormData($("#sign_form")[0]);
      var CSRF_TOKEN = $('input[name="_token"]').val();  
  //  console.log(uploadfile); 
    var siteUrl = $('.site_url').val();
    var form_url = $("form[id='sign_form']").attr("action");
    var domaincost=$('doitcost').val();
   // var domainurl=$('.webname').val();
    var domainemail=$('.domain_email').val();
    var token=$('.token').val();
    var whodo=$('input[name=whodo]:checked').val();
    var domainpage=$('.webpages').val();

    //console.log(domainemail);
    //console.log(whodo);
    //var formdata = {"domainck": 'yes',"domainurl": domainurl,"domainmail": domainemail,"whodo": whodo,"domaincost": domaincost,"_token": token};
      //var formdata={"domaindata": formData,"_token": token }; 
        
      $.ajax({
        url:  form_url+ '?_token=' + CSRF_TOKEN,
        type: 'POST',
        data: {
            _token: CSRF_TOKEN,
            file: imageFile
        },
        contentType: false, 
        processData: false,
        dataType: 'JSON',
        success: function (result) {
            console.log(result);
            if(result === "error"){
                // Update HTML status_area with an error message
            }
            if(result === "ok"){
                // Update HTML status_area with an success message
            }
        }
    });

       return false; 

}
function doitformepay(){
   var formdata = new FormData($(this)[0]);
    var siteUrl = $('.site_url').val();
    var domaincost=$('doitcost').val();
    //var domainurl=$('.webname').val();
    var domainemail=$('.domain_email').val();
    var token=$('.token').val();
    var whodo=$('input[name=whodo]:checked').val();
    var domainpage=$('.webpages').val();
    var domaindoc=$('#excel').val();
    
    
    //var formdata = {"domainck": 'yes',"domainurl": domainurl,"domainmail": domainemail,"whodo": whodo,"domaincost": domaincost,"_token": token};
        $.ajax({
           type: "POST",
           url: siteUrl+"updateDomainData",
           data: formdata,
          dataType: "json",
           success: function(res){
            if(res=='iwill'){
              //window.location.href='site-create';
            }
            else
            {
              //window.location.href='site-estimate';
            }
           }

         });

}
 
function iwilldoit(){
    var siteUrl = $('.site_url').val();
    var domainurl=$('.domain_url').val();
    var domainemail=$('.domain_email').val();
    var token=$('.token').val();
    var whodo=$('input[name=whodo]:checked').val();
    console.log(domainemail);
    console.log(whodo);
    var formdata = {"domainck": 'yes',"domainurl": domainurl,"domainmail": domainemail,"whodo": whodo,"_token": token};
        $.ajax({
           type: "POST",
           url: siteUrl+"updateDomainData",
           data: formdata,
          dataType: "json",
           success: function(res){
            if(res=='iwill'){
              reject_delay();
            }
            else
            {
              //window.location.href='site-estimate';
              
            }
           }

         });

}