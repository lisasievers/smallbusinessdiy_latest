
//Bulk Domain Authority Checker

var linksArr = new Array();
var nlinksArr = new Array();

function make(domainID,sqURL) { 
	if(domainID >= nlinksArr.length)
	{
		jQuery(".percentimg").fadeOut();
		return;
	}
    var c_link = nlinksArr[domainID];
    //AJAX Call
	jQuery.post('../?route=ajax',{mozAuthority:'1',domainAuthority:'1',sitelink:c_link},function(data){
		if(data == '0')
		{
			jQuery("#status-"+domainID).html('<b style="color:red">'+msgTab3+'</b>');
		}
		else
        {
			jQuery("#status-"+domainID).html('<b style="color:green">'+data+'</b>');
		}
		window.setTimeout("make("+(domainID+1)+",'"+sqURL+"')", 6000);
	});
}

jQuery(document).ready(function(){
    jQuery("#checkButton").click(function()
    {
        var myUrl = "";
        var myURLs=jQuery("#linksBox").val();
        
        if(myURLs == ""){
            alert(msgDomain);
            return false;
        }
        
        linksArr = myURLs.split('\n');
        
        if ($("#capCode").length > 0){
        var sCode= jQuery.trim($('input[name=scode]').val());
        if (sCode==null || sCode=="") {
        alert("Image verification field can't empty!");
        return false;
        } else {
        jQuery.post('../?route=ajax',{capthca:'1', scode:sCode},function(data){
        if (data == '1') {
            //Okay
            jQuery("#mainbox").fadeOut();
            jQuery("#resultBox").css({"display":"block"});
            jQuery("#resultBox").show();
            jQuery("#resultBox").fadeIn();
            jQuery(".percentimg").css({"display":"block"});
            jQuery(".percentimg").show();
            jQuery(".percentimg").fadeIn();
            
            var nLoop = 0;  
            var listHTML = '<br><table class="table table-bordered"><thead><tr><th>#</th><th>'+msgTab1+'</th><th>'+msgTab2+'</th></tr></thead><tbody>';
            for(i=0; i < linksArr.length; i++)
    	    {
    	       myURL=jQuery.trim(linksArr[i]);
           	   if (myURL.indexOf("https://") == 0){myURL=myURL.substring(8);}
               if (myURL.indexOf("http://") == 0){myURL=myURL.substring(7);}
    	       if(myURL != ""){
    	        nlinksArr[nLoop] = myURL;
                var classTr = nLoop % 2 == 0?'even':'odd';
                listHTML+= '<tr class="'+classTr+'"><td align="center">'+(nLoop+1)+'</td><td id="link-'+nLoop+'"><a href="'+ "http://" + myURL +'" target="_blank">'+ myURL +'</a></td><td align="center" id="status-'+nLoop+'">&nbsp;</td></tr>';
                if(nLoop===19){
                break;
                }
                nLoop = nLoop +1;
               }
            }
            listHTML+= '</tbody></table>';
            jQuery("#results").html(listHTML);
            jQuery("#results").slideDown();
            setTimeout(function(){
            var pos = $('#results').offset();
            $('body,html').animate({ scrollTop: pos.top });
            }, 1500);
            window.setTimeout("make(0,'"+myURL+"')", 2000);
            }
            else {
                alert("Image verification code is wrong!");
                return false;
            }
        });   
        }
        }else{
        jQuery("#mainbox").fadeOut();
        jQuery("#resultBox").css({"display":"block"});
        jQuery("#resultBox").show();
        jQuery("#resultBox").fadeIn();
        jQuery(".percentimg").css({"display":"block"});
        jQuery(".percentimg").show();
        jQuery(".percentimg").fadeIn();
        
        var nLoop = 0;  
        var listHTML = '<br><table class="table table-bordered"><thead><tr><th>#</th><th>'+msgTab1+'</th><th>'+msgTab2+'</th></tr></thead><tbody>';
        for(i=0; i < linksArr.length; i++)
	    {
	       myURL=jQuery.trim(linksArr[i]);
       	   if (myURL.indexOf("https://") == 0){myURL=myURL.substring(8);}
           if (myURL.indexOf("http://") == 0){myURL=myURL.substring(7);}
	       if(myURL != ""){
	        nlinksArr[nLoop] = myURL;
            var classTr = nLoop % 2 == 0?'even':'odd';
            listHTML+= '<tr class="'+classTr+'"><td align="center">'+(nLoop+1)+'</td><td id="link-'+nLoop+'"><a href="'+ "http://" + myURL +'" target="_blank">'+ myURL +'</a></td><td align="center" id="status-'+nLoop+'">&nbsp;</td></tr>';
            if(nLoop===19){
            break;
            }
            nLoop = nLoop +1;
           }
        }
        listHTML+= '</tbody></table>';
        jQuery("#results").html(listHTML);
        jQuery("#results").slideDown();
        setTimeout(function(){
        var pos = $('#results').offset();
        $('body,html').animate({ scrollTop: pos.top });
        }, 1500);
        window.setTimeout("make(0,'"+myURL+"')", 2000);
        }
    });
});