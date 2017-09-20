@extends ('layouts.dashboard')

@section('section')
<style>
.loading{text-align: center;margin-top:20px;}
#result{padding:5px;margin: 0 0 10px 0;}
</style>
<div class="col-sm-12 ">
	<h2>Google Mobile Friendly Report</h2>
	<div class="col-md-6">
		<div class="gtools-box">
		<br>URL: <input type="url" id="url" placeholder="Include http:// or https://" size="40">
<button onclick="runInspection();">Go</button>
 <div class="loading"></div>
<br><br>Mobile Friendly Test Result:
<div id="result" style="border: solid thin black; height:5em; overflow:auto;"></div>

	</div>
	</div>
  <div class="col-md-6">
<img id="image" style="border: solid thin black;"/>
  </div>
     
</div>

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
  $('.loading').html('<img src="'+baseUrl+'src/images/custom.gif" alt="Loading">');
  $('#result').html('');
  var apiUrl = 'https://searchconsole.googleapis.com/v1/urlTestingTools/mobileFriendlyTest:run?key=AIzaSyCGjEFXd_Ro-HjtOa-71uIKTfJUb39YYA0';
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.open("POST", apiUrl);
  xmlhttp.setRequestHeader("Content-Type", "application/json");
  xmlhttp.send(
      '{"url": "' + document.getElementById("url").value + '", "requestScreenshot": true}');
  xmlhttp.onreadystatechange = function() {
    $('.loading').html('');
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
@stop
