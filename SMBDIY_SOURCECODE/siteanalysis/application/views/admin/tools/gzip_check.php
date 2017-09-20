<style>

table {
	color: #000;
}

table tr {
	display: table-row;
    vertical-align: inherit;
}

table tr th {
    background-color: #efefef;
    background: -webkit-linear-gradient(top,#F9F9F9 0,#efefef 100%);
    background: -moz-linear-gradient(top,#F9F9F9 0,#efefef 100%);
    background: -o-linear-gradient(top,#F9F9F9 0,#efefef 100%);
    background: linear-gradient(to bottom,#F9F9F9 0,#efefef 100%);
    background-repeat: repeat-x;	
	border-width: 0 1px 1px 0;
	border-color: #c0c0c0;
	border-style: solid;    
}

table td {
	border-width: 0 1px 1px 0;
	border-color: #c0c0c0;
	border-style: solid;
}

</style>

<?php $this->load->view('admin/theme/message'); ?>

<section class='content'>

	<div class='row'>
		<div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2 well">

			<div class='text-center'><h4 class="text-info"><strong><?php echo $this->lang->line("Gzip Check");?></strong></h4></div>			

			<div class='form-group'>
				<?=form_textarea(array('name' => 'urls', 'placeholder' => 'Put your URLs (comma separated)', 'style' => 'width:100%;padding:10px;', 'rows' => 3, 'id' => 'urls')); ?>
			</div> <!-- end form-group -->

			<div class='form-group'>
				<button id='searchGzip' class='btn btn-info'>Check</button>				
			</div>

			<div class='row'>
				<div class="col-xs-12" class="text-center" id="progress_msg">
					<div class="progress" style="display: none;" id="progress_bar_con"> 
						<div style="width:100%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="3" role="progressbar" class="progress-bar progress-bar-success progress-bar-striped" id="scale"><span id='status'></span></div> 
					</div>
				</div>  
			</div>				

			<table class='table table-bordered'>
				<tr>
					<th style='width: 30%'>URL</th>
					<th style='width: 20%'>Gzip Enable</th>
					<th style='width: 25%'>Gzip Page Size</th>
					<th style='width: 25%'>Normal Page Size</th>
				</tr>
			</table>

		</div>
	</div> <!-- end row -->

</section>

<script>

    $('#searchGzip').click(function() {

        urls = $('#urls').val();

        if(urls == '') {
        	alert('You have not enter any URL');
        	return false;
        }    	

    	$('#progress_bar_con').show();
    	$('#status').html('Checking');
    	$('#scale').addClass('active');

        $.post('gzip_check_action',{urlvalues: urls},function(response) {

            response.url_lists.forEach( function (arrayItem) {
                if(arrayItem.gzip_enable == 1) {
                	var enable = 'Yes';
                } else {
                	var enable = 'No';
                }
                var $row = $('<tr>'+
                      '<td>'+arrayItem.url+'</td>'+
                      '<td>'+enable+'</td>'+
                      '<td>'+arrayItem.gzip_page_size+'</td>'+
                      '<td>'+arrayItem.normal_page_size+'</td>'+
                      '</tr>');    
                $('table> tbody:last').append($row);                
            });

            $('#status').html('Complete');
            $('#scale').removeClass('active');

        });     

    });    

</script>