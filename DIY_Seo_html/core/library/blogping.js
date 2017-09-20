
//Online Blog Ping Tool


var myArr = new Array();

function getDomain(linkSt)
{
	return linkSt.replace(/(http:\/\/[^\/]*)+([^$]*)/g, '$1');
}
function make(domainID,sqURL) {
    var blogNameData=jQuery("#blogNameData").val();
    var myBlogUpdateUrlData=jQuery("#myBlogUpdateUrlData").val();
    var myBlogRSSFeedUrlData=jQuery("#myBlogRSSFeedUrlData").val();
	if(domainID >= myArr.length)
	{
		jQuery(".percentimg").fadeOut();
		return;
	}
    var c_link = myArr[domainID];
	//do the post load
	jQuery.post('../?route=ajax',{blogPing:'1',pingUrl:c_link,blogUrl:sqURL,blogName:blogNameData,myBlogUpdateUrl:myBlogUpdateUrlData,myBlogRSSFeedUrl:myBlogRSSFeedUrlData},function(data){
        if(data=='Thanks for the ping.'){
        jQuery("#status-"+domainID).html('<b style="color:green">'+data+'</b>');  
        }else{
        jQuery("#status-"+domainID).html('<b style="color:orange">'+data+'</b>');
        }

		window.setTimeout("make("+(domainID+1)+",'"+sqURL+"')", 1000);
	});
}

jQuery(document).ready(function(){
    jQuery("#checkButton").click(function()
    {
    var myURL=jQuery("#myurl").val();
    var blogNameData=jQuery("#blogNameData").val();
    var myBlogUpdateUrlData=jQuery("#myBlogUpdateUrlData").val();
    var myBlogRSSFeedUrlData=jQuery("#myBlogRSSFeedUrlData").val();

    myURL=jQuery.trim(myURL);
   	if(myURL==''||myURL==null)
	{
		alert(msgDomain);
		return;
	}
   	if(blogNameData==''||blogNameData==null)
	{
		alert(msgName);
		return;
	}
	if(myBlogUpdateUrlData==''||myBlogUpdateUrlData==null)
	{
		alert(msgUpdate);
		return;
	}
	if(myBlogRSSFeedUrlData==''||myBlogRSSFeedUrlData==null)
	{
		alert(msgRss);
		return;
	}
	if (myURL.indexOf("https://") == 0){
	}
    else if (myURL.indexOf("http://") == 0){
    }
    else{
        myURL = "http://" + myURL;
    }

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
            jQuery.get('../core/library/blogPing.db',function(data){
 			myArr = data.split('\n');
			if(myArr.length < 2)
			{
				alert(smErr);
				return;
			}
			var listHTML = '<br><table class="table table-bordered"><thead><tr><th>#</th><th>'+msgTab1+'</th><th>'+msgTab2+'</th></tr></thead><tbody>';
			for(i=0; i < myArr.length; i++)
			{
				var classTr = i % 2 == 0?'even':'odd';
                var pingLink = myArr[i];
  	            if (pingLink.indexOf("https://") == 0){pingLink=pingLink.substring(8);}
                if (pingLink.indexOf("http://") == 0){pingLink=pingLink.substring(7);}
				listHTML+= '<tr class="'+classTr+'"><td align="center">'+(i+1)+'</td><td id="link-'+i+'">'+pingLink+'</td><td align="center" id="status-'+i+'">&nbsp;</td></tr>';
			}
			listHTML+= '</tbody></table>';
			jQuery("#results").html(listHTML);
			jQuery("#results").slideDown();
            setTimeout(function(){
			var pos = $('#results').offset();
			$('body,html').animate({ scrollTop: pos.top });
			}, 1500);
			window.setTimeout("make(0,'"+myURL+"')", 2000);
        });
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
    jQuery.get('../core/library/blogPing.db',function(data){
 			myArr = data.split('\n');
		if(myArr.length < 2)
			{
				alert(smErr);
				return;
			}
			var listHTML = '<br><table class="table table-bordered"><thead><tr><th>#</th><th>'+msgTab1+'</th><th>'+msgTab2+'</th></tr></thead><tbody>';
			for(i=0; i < myArr.length; i++)
			{
				var classTr = i % 2 == 0?'even':'odd';
                var pingLink = myArr[i];
  	            if (pingLink.indexOf("https://") == 0){pingLink=pingLink.substring(8);}
                if (pingLink.indexOf("http://") == 0){pingLink=pingLink.substring(7);}
				listHTML+= '<tr class="'+classTr+'"><td align="center">'+(i+1)+'</td><td id="link-'+i+'">'+pingLink+'</td><td align="center" id="status-'+i+'">&nbsp;</td></tr>';
			}
			listHTML+= '</tbody></table>';
			jQuery("#results").html(listHTML);
			jQuery("#results").slideDown();
            setTimeout(function(){
			var pos = $('#results').offset();
			$('body,html').animate({ scrollTop: pos.top });
			}, 1500);
			window.setTimeout("make(0,'"+myURL+"')", 2000);
        });
        
        }
    });
});