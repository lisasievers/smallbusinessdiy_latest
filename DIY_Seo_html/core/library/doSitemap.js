
//XML Sitemap Generator
var countLinks = 0;
var strTime = 800;
var limitAdmin = 5000;
var linksArr =[];
var maxLinksCrawl = 50;

function doSitemap(){

    var myUrl=$("#url").val();
    var maxLinks=$("#mapPages").val();
    var checkDate=$("#mapDate").val();
    var customDate=$("#mapdateBox").val();
    var defPriority=$("#mapPri").val();
    var defFreq=$("#mapFre").val();
    if(checkDate == '2'){
        if(customDate=="" || customDate == null){
            alert('Custom date field cannot be empty');
            return false;
        }
    }
	if(myUrl==null || myUrl=="") {
		alert('Enter a domain name!');
        return false;
	}
	else {
	   
    //Fix URL
    if (myUrl.indexOf("http://") == 0) {
    myUrl=myUrl.substring(7);
    }
    
    if (myUrl.indexOf("https://") == 0) {
    myUrl=myUrl.substring(8);
    }
    
    if ($("#capCode").length > 0){
        var sCode= jQuery.trim($('input[name=scode]').val());
        if (sCode==null || sCode=="") {
        alert("Image verification field can't empty!");
        return false;
        } else {
        jQuery.post('../?route=ajax',{capthca:'1', scode:sCode},function(data){
        if (data == '1') {
            jQuery("#mainBox").fadeOut();
   	        jQuery("#resultBox").css({"display":"block"});
   	        jQuery(".percentimg").css({"display":"block"});
   	        jQuery("#resultBox").show();
   	        jQuery("#resultBox").fadeIn();
	        var pos = $('.topBox').offset();
	        $('body,html').animate({ scrollTop: pos.top },strTime);
            jQuery("#resultList").append('&lt;?xml version="1.0" encoding="UTF-8"?&gt; \n');
            jQuery("#resultList").append('&lt;urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"&gt; \n');
            var countMe = 0;
            processSitemap(myUrl,maxLinks,countMe,checkDate,customDate,defPriority,defFreq);
            }
            else {
                alert("Image verification code is wrong!");
                return false;
            }
        });   
        }
   }else{
    jQuery("#mainBox").fadeOut();
   	jQuery("#resultBox").css({"display":"block"});
   	jQuery(".percentimg").css({"display":"block"});
   	jQuery("#resultBox").show();
   	jQuery("#resultBox").fadeIn();
	var pos = $('.topBox').offset();
	$('body,html').animate({ scrollTop: pos.top },strTime);
    jQuery("#resultList").append('&lt;?xml version="1.0" encoding="UTF-8"?&gt; \n');
    jQuery("#resultList").append('&lt;urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"&gt; \n');
    var countMe = 0;
    processSitemap(myUrl,maxLinks,countMe,checkDate,customDate,defPriority,defFreq);
    }
    }
}

function processSitemap(myUrl,maxLinks,countMe,checkDate,customDate,defPriority,defFreq){
        jQuery.post('../?route=ajax',{sitemap:'1', url:myUrl},function(data){
        var resData = data.split("::|::"); 
        var resCount = parseInt(resData[0]);
        var resLinkData = resData[1];
        var resLinks = resLinkData.split("\n"); 
        var ccLinks = [];
        jQuery(".linksCount").html('<br/>Crawling Link: '+ myUrl +'<br/>Links Found: ' + resLinks.length);
        
        for (var i = 0; i < resLinks.length; i++) {
        var ccData = resLinks[i].trim();
        if(jQuery.inArray(ccData, linksArr) == -1){
        ccLinks.push(ccData);  
        countLinks++;
        if(countLinks != maxLinks){
        jQuery("#resultList").append('&lt;url&gt;'+'\n'); 
        jQuery("#resultList").append('  &lt;loc&gt;'+ ccData + '&lt;/loc&gt;' + '\n'); 
        if(defPriority != 'N/A'){
        jQuery("#resultList").append('  &lt;priority&gt;'+ defPriority + '&lt;/priority&gt;' + '\n'); 
        }
        if(defFreq != 'N/A'){
        defFreqT = defFreq.toString().toLowerCase();
        jQuery("#resultList").append('  &lt;changefreq&gt;'+ defFreqT + '&lt;/changefreq&gt;' + '\n'); 
        }
        if(checkDate != 'N/A'){
            if(checkDate == '1'){
            var fullDate = new Date();
            var twoDigitMonth = fullDate.getMonth()+1+"";if(twoDigitMonth.length==1)  twoDigitMonth="0" +twoDigitMonth;
            var twoDigitDate = fullDate.getDate()+"";if(twoDigitDate.length==1) twoDigitDate="0" +twoDigitDate;
            var currentDate = fullDate.getFullYear() + "-" + twoDigitMonth + "-" + twoDigitDate;
            jQuery("#resultList").append('  &lt;lastmod&gt;'+ currentDate + '&lt;/lastmod&gt;' + '\n'); 
            }
            if(checkDate == '2'){
            customDate = customDate.trim();customDate=customDate.split('/');
            customDate = customDate[2] + "-" + customDate[1] + "-" + customDate[0];
            jQuery("#resultList").append('  &lt;lastmod&gt;'+ customDate + '&lt;/lastmod&gt;' + '\n');   
            }
        }
        jQuery("#resultList").append('&lt;/url&gt;'+'\n');
        }else{
          jQuery(".percentimg").fadeOut();
          jQuery("#resultList").append('&lt;/urlset&gt;');
          jQuery(".genCount").html('<br/>Sitemap generated for ' + countLinks + ' links!');
          break;
        }
        }
        }
        if(countLinks == maxLinks){
            return false;
        }
        if (countMe == maxLinksCrawl){
          jQuery(".percentimg").fadeOut();
          jQuery("#resultList").append('&lt;/urlset&gt;');
          jQuery(".genCount").html('<br/>Crawler Limit Reached! <br/> Sitemap generated for ' + countLinks + ' links!');
          return false;
        }
        else{
        linksArr = linksArr.concat(ccLinks);
        myUrl= linksArr[countMe];
        countMe++;
        if (parseInt(countMe) < parseInt(linksArr.length)) {
        processSitemap(myUrl,maxLinks,countMe,checkDate,customDate,defPriority,defFreq);
        }
        else{
          jQuery(".percentimg").fadeOut();
          jQuery("#resultList").append('&lt;/urlset&gt;');
          jQuery(".genCount").html('<br/>Sitemap generated for ' + countLinks + ' links!');
          return false;
        }
        }
    });
}

function htmlspecialchars_fix(string, quoteStyle) {

  var strTemp = 0,
    i = 0,
    noquotes = false;
  if (typeof quoteStyle === 'undefined') {
    quoteStyle = 2;
  }
  string = string.toString()
    .replace(/&lt;/g, '<')
    .replace(/&gt;/g, '>');
  var OPTS = {
    'ENT_NOQUOTES': 0,
    'ENT_HTML_QUOTE_SINGLE': 1,
    'ENT_HTML_QUOTE_DOUBLE': 2,
    'ENT_COMPAT': 2,
    'ENT_QUOTES': 3,
    'ENT_IGNORE': 4
  };
  if (quoteStyle === 0) {
    noquotes = true;
  }
  if (typeof quoteStyle !== 'number') { 
    quoteStyle = [].concat(quoteStyle);
    for (i = 0; i < quoteStyle.length; i++) {
      if (OPTS[quoteStyle[i]] === 0) {
        noquotes = true;
      } else if (OPTS[quoteStyle[i]]) {
        strTemp = strTemp | OPTS[quoteStyle[i]];
      }
    }
    quoteStyle = strTemp;
  }
  if (quoteStyle & OPTS.ENT_HTML_QUOTE_SINGLE) {
    string = string.replace(/&#0*39;/g, "'");
  }
  if (!noquotes) {
    string = string.replace(/&quot;/g, '"');
  }
  
  return string;
}
$(document).ready(function() {
document.getElementById('saveXMLFile').onclick = function() {                  

var xmlData = 'data:application/xml;charset=utf-8,' + encodeURIComponent(htmlspecialchars_fix(document.getElementById('resultList').innerHTML));
                        this.href = xmlData;
                        this.target = '_blank';
                        this.download = 'sitemap.xml';
};
});