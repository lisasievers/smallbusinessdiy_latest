<?php
defined('APP_NAME') or die(header('HTTP/1.0 403 Forbidden'));

/*
 * @author Balaji
 * @name: A to Z SEO Tools
 * @copyright © 2016 ProThemes.Biz
 *
 */
?>
     <!-- Main Footer -->
      <footer class="main-footer">
        <!-- To the right -->
        <div class="pull-right hidden-xs">
          Your Version <?php echo VER_NO; ?>
        </div>
        <!-- Default to the left -->
        <strong>Copyright &copy; 2016 <a href="http://prothemes.biz/">ProThemes.Biz</a></strong> All rights reserved.
      </footer>

      <!-- Add the sidebar's background. This div must be placed
           immediately after the control sidebar -->
      <div class='control-sidebar-bg'></div>
    </div><!-- ./wrapper -->

    <!-- REQUIRED JS SCRIPTS -->
    <!-- jQuery 2.1.4 -->
    <script src="<?php echo $theme_path; ?>plugins/jQuery/jQuery-2.1.4.min.js"></script>
    
    <!-- Bootstrap 3.3.2 JS -->
    <script src="<?php echo $theme_path; ?>bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo $theme_path; ?>dist/js/app.min.js" type="text/javascript"></script>
    <!-- Morris.js charts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="<?php echo $theme_path; ?>plugins/morris/morris.min.js" type="text/javascript"></script>
    <!-- DATA TABES SCRIPT -->
    <script src="<?php echo $theme_path; ?>plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="<?php echo $theme_path; ?>plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>
    <!-- date-range-picker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js" type="text/javascript"></script>
    <script src="<?php echo $theme_path; ?>plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
    <!-- CK Editor -->
    <script src="<?php echo $theme_path; ?>plugins/ckeditor/ckeditor.js"></script>
    <!-- Select2 -->
    <script src="<?php echo $theme_path; ?>plugins/select2/select2.full.min.js"></script>
    
    <?php if(isset($addToFooter)){  echo $addToFooter; } ?>
    
<?php if(isset($mailTemplates)) { ?>
      <script type="text/javascript">
      $(function () {
        // Replace the <textarea id="editor1"> with a CKEditor
        // instance, using default configuration.
        CKEDITOR.replace('reminder',{ filebrowserBrowseUrl : '/core/library/filemanager/dialog.php?type=2&editor=ckeditor&fldr=', filebrowserUploadUrl : '/core/library/filemanager/dialog.php?type=2&editor=ckeditor&fldr=', filebrowserImageBrowseUrl : '/core/library/filemanager/dialog.php?type=1&editor=ckeditor&fldr=' });
        CKEDITOR.replace('invoice',{ filebrowserBrowseUrl : '/core/library/filemanager/dialog.php?type=2&editor=ckeditor&fldr=', filebrowserUploadUrl : '/core/library/filemanager/dialog.php?type=2&editor=ckeditor&fldr=', filebrowserImageBrowseUrl : '/core/library/filemanager/dialog.php?type=1&editor=ckeditor&fldr=' });      
      });
    </script>

<?php } if(isset($neworder)){  ?> 

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" />
<style>
.ui-autocomplete {
z-index: 100 !important;
}
</style>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script type="text/javascript">
$(document).ready (function(){

$('#plan').autocomplete({
	source: function( request, response ) {
  		$.ajax({
  			url : '/admin/?route=premium_ajax&getPlans',
  			dataType: "json",
			data: {
			   term: request.term,
			   type: 'plan_table'
			},
			 success: function( data ) {
				 response( $.map( data, function( item ) {
				 	var code = item.split("|");
					return {
						label: code[0],
						value: code[0],
						data : item
					}
				}));
			}
  		});
  	},
  	autoFocus: true,	      	
  	minLength: 0,
  	select: function( event, ui ) {
		var names = ui.item.data.split("|");						
		$('#planID').val(names[1]);
        if(names[2] == '0'){
            $('#billDis').val('One Time Billing');
      		$('#billType').val('0');
      		$('#amount').val(names[3]);
      		$('#total').val(names[3]);
            jQuery("#recTypeBox").css({"display":"none"});
        }else{
            jQuery("#recTypeBox").css({"display":"block"});
            $('#billDis').val('Recurring Billing');
      		$('#billType').val('1');

            if(names[4] != '0'){
          		$('#recType').append('<option value="'+names[4]+'">Monthly</option>');  
                $('#rec1Am').val(names[5]);
            }
            if(names[6] != '0'){
          		$('#recType').append('<option value="'+names[6]+'">Every 3 months</option>');  
                $('#rec2Am').val(names[7]);
            }
            if(names[8] != '0'){
          		$('#recType').append('<option value="'+names[8]+'">Every 6 months</option>');
                $('#rec3Am').val(names[9]);  
            }
            if(names[10] != '0'){
          		$('#recType').append('<option value="'+names[10]+'">Every year</option>');  
                $('#rec4Am').val(names[11]);  
            }
            
            $("#recType option:first").attr('selected','selected');
            var myAmount = 0;
            var myTotal = 0;
            if($("#recType").val()== 'rec1'){
                myAmount = myTotal =  $('#rec1Am').val();
            }
            if($("#recType").val()== 'rec2'){
                myAmount = myTotal =  $('#rec2Am').val();
            }
            if($("#recType").val()== 'rec3'){
                myAmount = myTotal =  $('#rec3Am').val();
            }
            if($("#recType").val()== 'rec4'){
                myAmount = myTotal =  $('#rec4Am').val();
            }
            $('#amount').val(myAmount);
      		$('#total').val(myTotal);
        }
	}		      	
});

$(document).on('change','#recType',function(){
    var myAmount = 0;
    var myTotal = 0;
    if($("#recType").val()== 'rec1'){
        myAmount = myTotal =  $('#rec1Am').val();
    }
    if($("#recType").val()== 'rec2'){
        myAmount = myTotal =  $('#rec2Am').val();
    }
    if($("#recType").val()== 'rec3'){
        myAmount = myTotal =  $('#rec3Am').val();
    }
    if($("#recType").val()== 'rec4'){
        myAmount = myTotal =  $('#rec4Am').val();
    }
    $('#amount').val(myAmount);
	$('#total').val(myTotal);
});

$('#username').autocomplete({
	source: function( request, response ) {
  		$.ajax({
  			url : '/admin/?route=premium_ajax&getCustomers',
  			dataType: "json",
			data: {
			   term: request.term,
			   type: 'users_table'
			},
			 success: function( data ) {
				 response( $.map( data, function( item ) {
				 	var code = item.split("|");
					return {
						label: code[0],
						value: code[0],
						data : item
					}
				}));
			}
  		});
  	},
  	autoFocus: true,	      	
  	minLength: 2,
  	select: function( event, ui ) {
		var names = ui.item.data.split("|");						
		$('#emailID').val(names[1]);
		$('#since').val(names[2]);
		$('#userID').val(names[3]);
	}		      	
});
});
</script>

<?php } if(isset($premiumDash)){ ?>

 <script>
 
   /* Morris.js Charts */
  // Sales chart
  var CountX = -1;
  var area = new Morris.Area({
    element: 'sales-chart',
    resize: true,
    <?php echo
    "data: [
      {y: '$date7', item1: $sale7},
      {y: '$date6', item1: $sale6},
      {y: '$date5', item1: $sale5},
      {y: '$date4', item1: $sale4},
      {y: '$date3', item1: $sale3},
      {y: '$date2', item1: $sale2},
      {y: '$date1', item1: $sale1}
    ],";
    ?>
    xkey: 'y',
    ykeys: ['item1'],
    labels: ['Sales'],
    lineColors: ['#a0d0e0'],
    hideHover: 'auto',
    preUnits: '<?php echo $currencySymbol[0]; ?>',
    parseTime: false,
    xLabelFormat: function(d) {
    CountX = CountX+1;
    <?php echo "return ['$date7','$date6','$date5','$date4','$date3','$date2','$date1'][CountX];"; ?>
    }
  });

 </script>  
 
<?php } if ($p_title == "Dashboard"){ ?>

 <script>
 
   /* Morris.js Charts */
  // Sales chart
  var CountX = -1;
  var area = new Morris.Area({
    element: 'pageviews-chart',
    resize: true,
    <?php echo
    "data: [
      {y: '$ldate[6]', item1: $tvisit[6], item2: $tpage[6]},
      {y: '$ldate[5]', item1: $tvisit[5], item2: $tpage[5]},
      {y: '$ldate[4]', item1: $tvisit[4], item2: $tpage[4]},
      {y: '$ldate[3]', item1: $tvisit[3], item2: $tpage[3]},
      {y: '$ldate[2]', item1: $tvisit[2], item2: $tpage[2]},
      {y: '$ldate[1]', item1: $tvisit[1], item2: $tpage[1]},
      {y: '$ldate[0]', item1: $tvisit[0], item2: $tpage[0]}
    ],";
    ?>
    xkey: 'y',
    ykeys: ['item1', 'item2'],
    labels: ['Unique Visitorss', 'Page View'],
    lineColors: ['#a0d0e0', '#3c8dbc'],
    hideHover: 'auto',
    parseTime: false,
    xLabelFormat: function(d) {
    CountX = CountX+1;
    <?php echo "return ['$ldate[6]','$ldate[5]','$ldate[4]','$ldate[3]','$ldate[2]','$ldate[1]','$ldate[0]'][CountX];"; ?>
    }
  });

 </script>  
 <?php } elseif($p_title == 'Manage SEO Tools') { ?> 
 
 
    <script type="text/javascript">
      $(function () {
        $('#seoToolTable').DataTable({
          "paging": true,
          "lengthChange": true,
          "searching": true,
          "ordering": true,
          "info": true,
          "autoWidth": false
        });
      });
    </script>
    
 <?php } elseif ($p_title == 'Manage Pages') { ?>
 
         <script>
         $(function () {
         $('#postedDate').daterangepicker({singleDatePicker: true, timePicker: true, format: 'MM/DD/YYYY h:mm A'});
         });
      
        var mainLink = "<?php echo 'http://' . $_SERVER['HTTP_HOST'] ."/page/"; ?>";
        
        $("#pageUrlBox").focus(function (){
            fixLinkBox()
            });
        $("#pageUrlBox").keypress(function (){
            fixLinkBox()
            });
        $("#pageUrlBox").blur(function (){
            fixLinkBox(); 
            });
        $("#pageUrlBox").click(function (){
            fixLinkBox()
            });
            
        function fixLinkBox(){
            var pageUrl= jQuery.trim($('input[name=page_url]').val());
            var ref = pageUrl.toLowerCase().replace(/[^a-z0-9]+/g,'-');
            $("#linkBox").html(" (" + mainLink + ref + ") "); 
        }
        
        function finalFixedLink(){
            var pageUrl= jQuery.trim($('input[name=page_url]').val());
            var ref = pageUrl.toLowerCase().replace(/[^a-z0-9]+/g,'-');
            $("#pageUrlBox").val(ref); 
            return true;
        }
        
        </script>
 
 <?php } elseif ($p_title == 'New Blog Post') { ?>
  
         <script>
      
         $(function () {
         $('#postedDate').daterangepicker({singleDatePicker: true, timePicker: true, format: 'MM/DD/YYYY h:mm A'});
         });
                     
        var mainLink = "<?php echo 'http://' . $_SERVER['HTTP_HOST'] ."/blog/"; ?>";
        
        $("#pageUrlBox").focus(function (){
            fixLinkBox()
            });
        $("#pageUrlBox").keypress(function (){
            fixLinkBox()
            });
        $("#pageUrlBox").blur(function (){
            fixLinkBox(); 
            });
        $("#pageUrlBox").click(function (){
            fixLinkBox()
            });
            
        function fixLinkBox(){
            var pageUrl= jQuery.trim($('input[name=post_url]').val());
            var ref = pageUrl.toLowerCase().replace(/[^a-z0-9]+/g,'-');
            $("#linkBox").html(" (" + mainLink + ref + ") "); 
        }
        
        function finalFixedLink(){
            var pageUrl= jQuery.trim($('input[name=post_url]').val());
            var ref = pageUrl.toLowerCase().replace(/[^a-z0-9]+/g,'-');
            $("#pageUrlBox").val(ref); 
            return true;
        }
        
        </script>
 
 <?php } if (isset($editPage)) { ?>
 
         <script>

        var mainLink = "<?php echo 'http://' . $_SERVER['HTTP_HOST'] ."/"; ?>";
        
        $("#toolUrlBox").focus(function (){
            fixLinkBox()
            });
        $("#toolUrlBox").keypress(function (){
            fixLinkBox()
            });
        $("#toolUrlBox").blur(function (){
            fixLinkBox(); 
            });
        $("#toolUrlBox").click(function (){
            fixLinkBox()
            });
            
        function fixLinkBox(){
            var pageUrl= jQuery.trim($('input[name=tool_url]').val());
            var ref = pageUrl.toLowerCase().replace(/[^a-z0-9]+/g,'-');
            $("#linkBox").html(" (" + mainLink + ref + ") "); 
        }
        
        function finalFixedLink(){
            var pageUrl= jQuery.trim($('input[name=tool_url]').val());
            var ref = pageUrl.toLowerCase().replace(/[^a-z0-9]+/g,'-');
            $("#toolUrlBox").val(ref); 
            return true;
        }
        
        </script>
 
<?php } if(isset($reviewerSys)){  ?>
    <script> 
       $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
        });
    </script>
<?php } if(isset($addPlanSelectBox)) { ?>

      <script>
      $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
            var radioBox = jQuery('input[name="payment_type"]:checked').val();
            if(radioBox == 0){
                $(".recpay").hide();
                $(".onepay").fadeIn();
            }else if(radioBox == 1){
                $(".recpay").fadeIn();
                $(".onepay").hide();
            }
            
            var selVal = jQuery('select[name="allow_pdf"]').val();
            if(selVal == 'yes'){
                $('select[name="brand_pdf"]').removeAttr('disabled');
                $('input[name="max_pdf"]').removeAttr('disabled');
            }else if(selVal == 'no'){
                $('select[name="brand_pdf"]').attr('disabled','disabled');
                $('input[name="max_pdf"]').attr('disabled','disabled');
            }
        });
        
        //Payment Type
        $('input[name="payment_type"]').on('change', function() {
            var radioBox = jQuery('input[name="payment_type"]:checked').val();
            if(radioBox == 0){
                $(".recpay").hide();
                $(".onepay").fadeIn();
                $(".rp-duration").html('OneTime'); 
                $('input[name="plan_type"]').val('OneTime');
            }else if(radioBox == 1){
                $(".recpay").fadeIn();
                $(".onepay").hide();
                $(".rp-duration").html('Monthly'); 
                $('input[name="plan_type"]').val('Monthly');
            }
        });
        
        //Plan Name
        $('input[name="plan_name"]').on('change keyup', function() {
            var planName = jQuery('input[name="plan_name"]').val();
            $(".rp-plan-name").html(planName); 
        });
                
        $('input[name="plan_type"]').on('change keyup', function() {
            var planType = jQuery('input[name="plan_type"]').val();
            $(".rp-duration").html(planType); 
        });
        
        $('input[name="onepay_cost"]').on('change keyup', function() {
            var planCost = jQuery('input[name="onepay_cost"]').val();
            $(".rp-cost").html(Math.round(planCost)); 
            $('input[name="plan_price"]').val(Math.round(planCost));
        });
        
        $('input[name="plan_price"]').on('change keyup', function() {
            var planPrice = jQuery('input[name="plan_price"]').val();
            $(".rp-cost").html(planPrice); 
        });
        
        $('select[name="cur_type"]').on('change keyup', function() {
            var planSym = jQuery('select[name="cur_type"] option:selected').text().split(/-/);
            $(".rp-currency").html(planSym[1]); 
        });
        
        $('input[name="plan_sub"]').on('change keyup', function() {
            var planSub = jQuery('input[name="plan_sub"]').val();
            $(".rp-plan-description").html(planSub); 
        });
        
        $('textarea[name="plan_features"]').on('change keyup', function() {
            var lines = $('textarea[name="plan_features"]').val().split(/\n/);
            $("#plan_data").html('');
            for (var i=0; i < lines.length; i++) {
              if (/\S/.test(lines[i])) {
                $("#plan_data").append('<li><p>'+$.trim(lines[i])+'</p></li>');  
              }
            }
        });
        
        
        
        //Allow PDF Box
        $('select[name="allow_pdf"]').on('change', function() {
            var selVal = jQuery('select[name="allow_pdf"]').val();
            if(selVal == 'yes'){
                $('select[name="brand_pdf"]').removeAttr('disabled');
                $('input[name="max_pdf"]').removeAttr('disabled');
            }else if(selVal == 'no'){
                $('select[name="brand_pdf"]').attr('disabled','disabled');
                $('input[name="max_pdf"]').attr('disabled','disabled');
            }
        });
        
        //Premium URL Link
        var mainLink = "<?php echo 'http://' . $_SERVER['HTTP_HOST'] ."/premium/"; ?>";
        
        $("#planUrlBox").focus(function (){
            fixLinkBox()
            });
        $("#planUrlBox").keypress(function (){
            fixLinkBox()
            });
        $("#planUrlBox").blur(function (){
            fixLinkBox(); 
            });
        $("#planUrlBox").click(function (){
            fixLinkBox()
            });
            
        function fixLinkBox(){
            var pageUrl= jQuery.trim($('input[name=plan_url]').val());
            var ref = pageUrl.toLowerCase().replace(/[^a-z0-9]+/g,'-');
            $("#linkBox").html(" (" + mainLink + ref + ") "); 
        }
        
        function finalFixedLink(){
            var pageUrl= jQuery.trim($('input[name=plan_url]').val());
            var ref = pageUrl.toLowerCase().replace(/[^a-z0-9]+/g,'-');
            $("#planUrlBox").val(ref); 
            return true;
        }
        
    </script>
 <?php } ?>
       <script type="text/javascript">
       $(document).ready (function(){
        $(".alert-dismissable").fadeTo(1000, 500).slideUp(500, function(){
        $(".alert-dismissable").alert('close');
        });
        });
      </script>
      
      <script type="text/javascript">
      $(function () {
        // Replace the <textarea id="editor1"> with a CKEditor
        // instance, using default configuration.
        CKEDITOR.replace('editor1',{ filebrowserBrowseUrl : '/core/library/filemanager/dialog.php?type=2&editor=ckeditor&fldr=', filebrowserUploadUrl : '/core/library/filemanager/dialog.php?type=2&editor=ckeditor&fldr=', filebrowserImageBrowseUrl : '/core/library/filemanager/dialog.php?type=1&editor=ckeditor&fldr=' });
      CKEDITOR.on( 'dialogDefinition', function( ev )
   {
      // Take the dialog name and its definition from the event
      // data.
      var dialogName = ev.data.name;
      var dialogDefinition = ev.data.definition;

      // Check if the definition is from the dialog we're
      // interested on (the Link and Image dialog).
      if ( dialogName == 'link' || dialogName == 'image' )
      {
         // remove Upload tab
         dialogDefinition.removeContents( 'Upload' );
      }
   });
      
      });
    </script>
    
      <script type="text/javascript">
    function checkBTX(){
    var r = confirm("Actions is irreversible!");
    return r; 
    }
    </script>

  </body>
</html>