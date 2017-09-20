
//Word Counter

function countData() {
            
            var myData = $.trim(document.getElementById("data").value);
            var strTime = 800;
            
            if (myData == ""){
                alert("Input Data field can't be empty!");
                return false;
            }
           			
			myData = myData.replace(/\s+/g," ");	
			myData = myData.replace(/\n /, " ");	
			
			if(myData!="") {
			 document.getElementById("wordCount").innerHTML = myData.split(' ').length;
			}
			document.getElementById("charCount").innerHTML = myData.length;	
			
			$("#result").show();
			var pos = $('#result').offset();
			$('body,html').animate({ scrollTop: pos.top },strTime);
	
}