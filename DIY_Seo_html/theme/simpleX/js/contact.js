//Contact Form

function contactDoc() {
var xmlhttp;
var user_name = $('input[name=c_name]').val();
var user_email = $('input[name=c_email]').val();
var user_sub = $('input[name=c_subject]').val();
var user_mes = $('textarea[name=email_message]').val();
if (user_name == '' || user_email == '')
{
    alert(msg1);
    return false;
}
if (user_sub == '' || user_mes == '')
{
    alert(msg1);
    return false;
}
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    }
  }
$.post("../?route=ajax", {contact:"1", name:user_name,email:user_email,subject:user_sub,message:user_mes}, function(results){
if (results == 1) {
     $("#c_alert1").show();
     alert(msg2);
}
else
{
     $("#c_alert2").show();
     alert(results);
}
});
}

function contactDocX() {
var xmlhttp;
var user_name = $('input[name=c_name]').val();
var user_email = $('input[name=c_email]').val();
var user_sub = $('input[name=c_subject]').val();
var user_mes = $('textarea[name=email_message]').val();
var capc = $('input[name=scode]').val();
if (user_name == '' || user_email == '')
{
    alert(msg1);
    return false;
}
if (user_sub == '' || user_mes == '')
{
    alert(msg1);
    return false;
}
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    }
  }
$.post("../?route=ajax", {capthca:'1',scode:capc}, function(results){
if (results == 1) {
$.post("../?route=ajax", {contact:"1",name:user_name,email:user_email,subject:user_sub,message:user_mes}, function(results){
if (results == 1) {
     $("#c_alert1").show();
     alert(msg2);
}
else
{
     $("#c_alert2").show();
     alert(results);
}
});
}
else
{
alert(msg3);
}
});
}
/*!
 * A to Z SEO Tools v1.3
 !**/