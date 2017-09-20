<script type="text/javascript">
function papulateErrors (obj, errors) {
	for(var e in errors) {
		if(typeof(errors[e]) == 'object')
			papulateErrors(obj, errors[e])
		else
			obj.append(errors[e] + '<br/>');
	}
}

function request() {
    var domain = $("#domain");
    domain.val(domain.val().replace(/^https?:\/\//i,'').replace(/\/$/i, ''));
	var data = $("#website-form").serialize(),
			button = $("#submit"),
			errObj = $("#errors");
	errObj.hide();
	errObj.html('');
	button.attr("disabled", true);

    $("#progress-bar").toggleClass("hide");

    $.getJSON('<?php echo $this -> createUrl('parse/index') ?>', data, function(response) {
		button.attr("disabled", false);
        $("#progress-bar").toggleClass("hide");

		// If response's type is string then all is ok, redirect to statistics
		if(typeof(response) == 'string') {
			document.location.href = response;
			return true;
		}
		// If it's object, then display errors
		papulateErrors(errObj, response);
		errObj.show();
	}).error(function(xhr, ajaxOptions, thrownError) {
		/*console.log(
		'xhr.status = ' + xhr.status + '\n' +
		'thrown error = ' + thrownError + '\n' +
		'xhr.responseText = ' + xhr.responseText + '\n' +
		'xhr.statusText = '  + xhr.statusText
		);*/
	});
}

$(document).ready(function() {
	var prourl= $('#prourl').val()+"/sitebuilder/public/apireviewuser";
	var thisuser=$('#thisuser').val();
//if(thisuser=="" || thisuser==0){window.location.href = $('#prourl').val();}
	var formdata = {"userid": thisuser};
    	  $.ajax({
    	  	 type: "GET",
    	  	 url: prourl,
    	  	 data: formdata,
    	  	 dataType: 'json',
    	  	 success: function(response){
        	  	 if(response=="" || response=="0" || response=="null"){ 
				 $('.usersites').html("");
				 }
				else { 
				//console.log(response['message']);
				var trHTML = '';
    	        $.each(response['message'], function (i, item) {
    	            trHTML += '<option value="'+ item.site_name +'">' + item.site_name + '</option>';
    	        });
    	        var st="<select id='domain' name='Website[domain]' class='website-input'>";
    	        var end='</select>';
    	        
     	        $('.usersites').html(st+trHTML+end);
				}
				}
			  });
			  
	$("#submit").click(function() {
		request();
		return false;
	});

	$("#website-form input").keypress(function(e) {
		if (e.keyCode == 13) {
			e.preventDefault();
			request();
			return false;
		}
	});
});
</script>
<style>
.usersites select{padding:0 !important;height:50px;}
</style>
<?php
if(isset(Yii::app()->request->cookies['user_id']->value)){$userid=Yii::app()->request->cookies['user_id']->value;}else{$userid=0;}
?>		
<input type="hidden" id="thisuser" value="<?php echo $userid; ?>" />
<input type="hidden" id="prourl" value="<?php echo Yii::app() -> params['project_url']; ?>" />
<form id="website-form">
<div class="input-append control-group">
<span class="usersites">
</span>
<!--<input class="website-input" name="Website[domain]" id="domain" placeholder="example.com" type="text">-->
<button class="btn btn-large btn-success analyseBtn" id="submit" type="button"><?php echo Yii::t("app", "Analyse") ?></button>
<div id="progress-bar" class="hide">
    <br/>
    <div class="progress progress-striped active">
        <div class="bar" style="width: 100%;"></div>
    </div>
</div>
</div>

<span id="upd_help" class="help-inline"> &larr; <?php echo Yii::t("app", "Click to update") ?></span>
<div class="clearfix"></div>
<div class="error alert alert-error span4<?php echo isset($errorClass) ? " $errorClass" : null ?>" id="errors" style="display:none"></div>
</form>