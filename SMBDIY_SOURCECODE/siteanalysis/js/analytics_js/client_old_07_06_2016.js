/*
Title: SiteSpy Client v1.0
Author: XerOne IT
Copyright: XerOne IT
URL: http://xeroneit.net
*/

var ip_link="http://konok-pc/xeroneit/web_analytics/js_controller/get_ip";
var server_link="http://konok-pc/xeroneit/web_analytics/js_controller/server_info";
var scroll_server_link="http://konok-pc/xeroneit/web_analytics/js_controller/scroll_info";
var click_server_link="http://konok-pc/xeroneit/web_analytics/js_controller/click_info";

var jquery_link="http://konok-pc/xeroneit/web_analytics/js/analytics_js/jquery.js";
var browser_js_link="http://konok-pc/xeroneit/web_analytics/js/analytics_js/jquery-browser-plugin.js";



function get_browser_info(){
		    var ua=navigator.userAgent,tem,M=ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || []; 
		    if(/trident/i.test(M[1])){
		        tem=/\brv[ :]+(\d+)/g.exec(ua) || []; 
		        return {name:'IE',version:(tem[1]||'')};
		        }   
		    if(M[1]==='Chrome'){
		        tem=ua.match(/\bOPR\/(\d+)/)
		        if(tem!=null)   {return {name:'Opera', version:tem[1]};}
		        }   
		    M=M[2]? [M[1], M[2]]: [navigator.appName, navigator.appVersion, '-?'];
		    if((tem=ua.match(/version\/(\d+)/i))!=null) {M.splice(1,1,tem[1]);}
		    return {
		      name: M[0],
		      version: M[1]
		    };
 }
 
 /*** Creating Cookie function ***/
 function createCookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

/***Read Cookie function**/
function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

/*** Delete Cookie Function ***/
function eraseCookie(name) {
    createCookie(name,"",-1);
}


function time_difference(from_time,to_time){
	var differenceTravel = to_time.getTime() - from_time.getTime();
	var seconds = Math.floor((differenceTravel) / (1000));
	return seconds;
	
}

 
 
function ajax_call(){
	
	/*Load Jquery****/
	var x = document.createElement('script');
	x.src = jquery_link;
	document.getElementsByTagName("head")[0].appendChild(x);
	
	/***If jquery load is compelted**/
	x.onload=function(){
		/**Conflict avoid code***/
		var $dolphin= $.noConflict();
		
		/**Load browser plugin***/
		var y = document.createElement('script');
		y.src = browser_js_link;
		document.getElementsByTagName("head")[0].appendChild(y);
		
		/**after browser plugin loaded**/
		y.onload=function(){
		
				var ip;
				var device;
				var mobile_desktop;
				if($dolphin.browser.android) 				device="Android";
			    else if($dolphin.browser.blackberry) 		device="Blackberry";
			    else if($dolphin.browser.cros)				device="Cros";
			    else if($dolphin.browser.ipad)				device="iPad";
			    else if($dolphin.browser.iphone)			device="iPhone";
			    else if($dolphin.browser.ipod)				device="iPod";
			    else if($dolphin.browser.kindle)			device="Kindle";
			    else if($dolphin.browser.linux)			    device="Linux";
			    else if($dolphin.browser.mac)				device="Mac";
			    else if($dolphin.browser.playbook)			device="Playbook";
			    else if($dolphin.browser.silk)				device="Silk";
			    else if($dolphin.browser.win)				device="Windows";
			    else if($dolphin.browser["windows phone"])	device="Windows";
			    else 								        device="Undetectable";
			
			    if($dolphin.browser.desktop)				mobile_desktop="Desktop";
				else if($dolphin.browser.mobile)			mobile_desktop="Mobile";
			
				var browser_info=get_browser_info();
				var browser_name=browser_info.name;
				var browser_version=browser_info.version;
				
				
				var browser_rawdata = JSON.stringify($dolphin.browser);
				//var browser_rawdata = platform.description;	
				
				/*
				var params = $dolphin('script:first').attr('src'); //Fetch the source in the first script tag
				var website_code = params.split('?')[1]; //Get the params*/
				
				var website_code = document.getElementById("domain").getAttribute("data-name");
				
				/**Get referer Address**/
				var referrer = document.referrer;
				
				/** Get Current url **/
				var current_url = window.location.href;
				
				/*** Get cookie value , if it is already set or not **/
				
				var cookie_value=readCookie("xerone_dolphin");
				var extra_value= new Date().getTime();
				
				/**if new visitor set the cookie value a random number***/
				if(cookie_value=='' || cookie_value==null || cookie_value === undefined){
					var is_new=1;
					var random_cookie_value=Math.floor(Math.random()*999999);
					random_cookie_value=random_cookie_value+extra_value.toString();
					createCookie("xerone_dolphin",random_cookie_value,1);
					cookie_value=random_cookie_value;
				}
				
				else{
					createCookie("xerone_dolphin",cookie_value,1);
					var is_new=0;
				}
				
				
				var session_value=sessionStorage.xerone_dolphin_session;
				
				if(session_value=='' || session_value==null || session_value === undefined){
					var random_session_value=Math.floor(Math.random()*999999);
					random_session_value=random_session_value+extra_value.toString();
					sessionStorage.xerone_dolphin_session=random_session_value;
					session_value=random_session_value;
				}
				
				
				
				/**if it is a new session then create session***/
				
				
				
							/** Pass all information with ip to the server **/
							$dolphin.ajax({
									    type: 'POST',
									    url: server_link,
										/*async: false,*/
									    crossDomain: true,
									    //data: '{"some":"json"}',
										data:{website_code:website_code,browser_name:browser_name,browser_version:browser_version,
											device:device,mobile_desktop:mobile_desktop,referrer:referrer,current_url:current_url,
											cookie_value:cookie_value,is_new:is_new,session_value:session_value,browser_rawdata:browser_rawdata},
									    /*dataType: 'json',
									    success: function(responseData, textStatus, jqXHR) {
											
									    }*/
							});				
							
			
			/** Scrolling detection, if it is scrolling more than 50%  and after 5 seceond of last scroll then enter the time ****/
			
				var last_scroll_time;
				var scroll_track=0;
				var time_dif=0;
				
				$dolphin(window).scroll(function(){
					
					 var wintop = $dolphin(window).scrollTop(), docheight = $dolphin(document).height(), winheight = $dolphin(window).height();
					 var  scrolltrigger = 0.50;
					 
					 if  ((wintop/(docheight-winheight)) > scrolltrigger) {
					 
					 	scroll_track++;
						var to_time=new Date();
						
						if(scroll_track>1){
							time_dif=time_difference(last_scroll_time,to_time);
						}
						
						
						if(scroll_track==1 || time_dif>5){
							last_scroll_time=new Date();
							$dolphin.ajax({
									    type: 'POST',
									    url: scroll_server_link,
										async: false,
									    crossDomain: true,
									    //data: '{"some":"json"}',
										data:{website_code:website_code,current_url:current_url,
											cookie_value:cookie_value,session_value:session_value},
									    dataType: 'json',
									    success: function(responseData, textStatus, jqXHR) {
											
								 }
							});			
							
						}
						
						
			   	 	}
			});		
			
					
							
					
			
			/*** track each engagement record. Enagagment is calculated by click function****/
			
				
				var last_click_time;
				var click_track=0;
				var click_time_dif=0;
				
				$dolphin(document).click(function(){
						click_track++;
						var to_time=new Date();
						
						if(click_track>1){
							click_time_dif=time_difference(last_click_time,to_time);
						}
						
						if(click_track==1 || click_time_dif>5){
							last_click_time=new Date();
							$dolphin.ajax({
									    type: 'POST',
									    url: click_server_link,
										async: false,
									    crossDomain: true,
									    //data: '{"some":"json"}',
										data:{website_code:website_code,current_url:current_url,
											cookie_value:cookie_value,session_value:session_value},
									    dataType: 'json',
									    success: function(responseData, textStatus, jqXHR) {
											
								 }
							});			
							
						}
						
					
					
				});
			
					
		}
	}
}

function init(){
	ajax_call();
}

init();



