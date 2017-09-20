$(document).ready(function () {
//Domain check condition
$(':checkbox').checkboxpicker();
$('.domainbuyinfo').hide();
$('#input-2').change(function() {
 // console.log('red');
  var wh=$(this).val();
   console.log(wh);
  if(wh=='yes'){
    $('#input-2').attr('value','no');
    $('.domaininfo').hide();
    $('.domainbuyinfo').show();
     $('.domainbuybtn').html('<button id="buydomain" onclick="showGoogleDomainsFlow()" >Buy a domain</button>');
    $('.domaininfo input').removeAttr('required','');
  }
  else
  {
    $('#input-2').attr('value','yes');
    $('.domaininfo').show();
    $('.domainbuyinfo').hide();
    $('.domaininfo input').attr('required','required');
  }

  });
    /*
        $("input[name=whodo]:radio").click(function () {
            if ($('input[name=whodo]:checked').val() == "iwill") {
          $('.iwill').css('border-bottom','2px solid #8bc435');
          $('.youwill').css('border-bottom','2px solid #ddd');
            } else if ($('input[name=whodo]:checked').val() == "youwill") {
          $('.youwill').css('border-bottom','2px solid #8bc435');
          $('.iwill').css('border-bottom','2px solid #ddd');
           }
  });*/
        $(".iwill").click(function (){
          $('.iwill').css('border-bottom','2px solid #8bc435');
          $('.youwill').css('border-bottom','2px solid #ddd');
          $('#whodo').val('iwill');
          $('.iwilltick').show();
          $('.youwilltick').hide(); 
          $('.iwill,.youwill').css('animation','none').css('border','2px solid #ddd');
        
       });
         $(".youwill").click(function (){
          $('.youwill').css('border-bottom','2px solid #8bc435');
          $('.iwill').css('border-bottom','2px solid #ddd');
          $('#whodo').val('youwill'); 
          $('.youwilltick').show(); 
          $('.iwilltick').hide();
          $('.youwill,.iwill').css('animation','none').css('border','2px solid #ddd');
       });
$('.logouts').click(function (){
        var ss=$('.sbu').attr('href');
        console.log(ss);
        $('.sdoc').click();
        $('.sspy').click();
        $('.sbu').click();
        $(".sbu").trigger("click");
        deleteAllCookies();
        }); 
$( ".webname" ).focus(function() {
  $('.webnameimg').css('border','1px solid #5cb85c');
  $('.webnameimg').attr('class','sitbuild-screen imgzoom ');
});

});
 $(".triggermail").click(function (){
  var ref=$(this).attr('data-val');
  console.log(ref);
  cronmail_job(ref);
  });
function deleteAllCookies() {
    var cookies = document.cookie.split(";");

    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i];
        var eqPos = cookie.indexOf("=");
        var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
        document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
    }
}

function reject_delay(){
var delay=3000;//3 seconds
setTimeout(function(){
  window.location.href='site-create';
},delay);
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
function cronmail_job(ref){
    var formdata = {"ref": ref};
        $.ajax({
           type: "POST",
           url: siteUrl+"mailjob",
           data: formdata,
          dataType: "json",
           success: function(res){
            if(res=='11'){
            //  reject_delay();
            }
            else
            {
              //window.location.href='site-estimate';
              
            }
           }

         });

}

