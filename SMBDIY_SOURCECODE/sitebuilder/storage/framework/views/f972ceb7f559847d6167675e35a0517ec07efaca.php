<div class="signupstage">
<?php $__env->startSection('page_heading',''); ?>
</div>
<?php $__env->startSection('section'); ?>
<div class="col-sm-12 ">
	<h2>Google Mobile Friendly Report</h2>
	<div class="col-md-6">
		<div class="tools-box">
		<br>URL: <input type="text" id="url" size="40">
<button onclick="runInspection();">Go</button>
<br><br>Result:
<div id="result" style="border: solid thin black; height:5em; overflow:auto;"></div>
Screenshot:
<img id="image" style="border: solid thin black;"/>
	</div>
	</div>
     <div class="res"></div>
</div>
<script>
/*
$(document).ready(function () {

 $('#qrsubmit').click(function() {
        var pid = $('#qrurl').val();
        
        var formdata = {"url": pid };
    $.ajax({
       type: "POST",
      // url: "<?php echo e(route('user.gmobiletest')); ?>",  
       url: "https://searchconsole.googleapis.com/v1/urlTestingTools/mobileFriendlyTest:run?fields=mobileFriendliness%2CmobileFriendlyIssues%2Cscreenshot%2CtestStatus&key=AIzaSyCGjEFXd_Ro-HjtOa-71uIKTfJUb39YYA0",
       data: formdata,
       dataType: 'jsonp',
       success: function(res){
        $('.res').html(res);  
        //$('#site_name').html(res[0]['site_name']);
  
        }
    });


  });

});    */   
</script>
<script>
function listMobileFriendlyIssues(responseJson) {
  var resultEl = document.getElementById('result');
  console.log(responseJson);
  if (responseJson.mobileFriendlyIssues) {
    resultEl.innerHTML += "Mobile friendly issues:<br>";
    responseJson.mobileFriendlyIssues.forEach(function(issue) {
        resultEl.innerHTML += "<li>" + issue.rule + "</li>"});
  } else {
    resultEl.innerHTML += "No mobile friendliness issues found!";
  }
}
function runInspection() {
  var apiUrl = 'https://searchconsole.googleapis.com/v1/urlTestingTools/mobileFriendlyTest:run?key=AIzaSyCGjEFXd_Ro-HjtOa-71uIKTfJUb39YYA0';
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.open("POST", apiUrl);
  xmlhttp.setRequestHeader("Content-Type", "application/json");
  xmlhttp.send(
      '{"url": "' + document.getElementById("url").value + '", "requestScreenshot": true}');
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4) {
      var responseJson = JSON.parse(this.responseText);
      if (this.status == 200) {
        listMobileFriendlyIssues(responseJson);
        document.getElementById('image').src =
            "data:" + responseJson.screenshot.mimeType + ';base64,' + responseJson.screenshot.data;
      } else {
        document.getElementById('result').innerHTML += "An error occured!<br>Error code: "
            + responseJson.error.code + "<br>Error message: " + responseJson.error.message;
      }
    }
  };
};
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>