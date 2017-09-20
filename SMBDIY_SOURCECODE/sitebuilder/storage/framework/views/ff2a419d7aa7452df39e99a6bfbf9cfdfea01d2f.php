<link rel="stylesheet" href="<?php echo e(asset('src/css/jquery.dataTables.min.css')); ?>">
<style>
#slider_image1{width: 200px;height: auto;}
</style>
<div class="row">

  <div class="sitelist col-md-12">
<h2 class="page-header"><i class="fa fa-list-ul" aria-hidden="true"></i> Users, Featured from <span class="diy-color">Do IT For Me</span></h2>
  <table class="table table-bordered table-striped" id="users-table">
              <thead>
                <tr>
                    <th>Site Name</th>
                    <th>Proposed User</th>
                    <th>Post Date</th>
                    <th>Site Categroy</th>
                    <th>Site Template</th>
                    <th>Payment</th>
                    <th>Get Started</th>
                  <th>Actions</th>
               </tr>
              </thead>
            </table>


  </div> 
</div>
<div class="row">
  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Website information to create..</h4>
        </div>
        <div class="modal-body">
           <table class="table">
            <tr>
              <td>Site Name<td><td id="site_name"></td>
            </tr>
             <tr>
              <td>Site Category<td><td id="site_category"></td>
            </tr>
             <tr>
              <td>Home Title<td><td id="home_title"></td>
            </tr>
            <tr>
              <td>Home Text<td><td id="home_text"></td>
            </tr>
            <tr>
              <td>Prodcuts Title<td><td id="products_title"></td>
            </tr>
            <tr>
              <td>Contact Address<td><td id="contact_address"></td>
            </tr>
            <tr>
              <td>Google Map Location<td><td id="google_map"></td>
            </tr>
            <tr>
              <td>Slider Images<br>( <span class="moresteps">Click on the image and download</span> )<td><td><a id="sdown1" href="" download><img id="slider_image1" /></a></td>
            </tr>
           </table>
        </div>
        
      </div>
      
    </div> <!-- Modal -->
</div>  

</div>
<script src="<?php echo e(asset('src/js/jquery.dataTables.min.js')); ?>"></script> 
<script>
  $(function() {
      $('#users-table').DataTable({
          processing: true,
          serverSide: true,
          ajax: '<?php echo e(route('doitsitedatax')); ?>',
          columns: [
              { data: 'site_name', name: 'po.site_name' },
              { data: 'first_name',name: 'ur.first_name',
                  render : function(data,type,full) {
                     return full.first_name +' '+ full.last_name;
                  }
               },
              { data: 'created_at', name: 'po.created_at' },
              { data: 'name', name: 'sc.name' },
              { data: 'orgsitename', name: 'si.site_name' },
              { data: 'status',name: 'br.status',
                  render : function(data) {
                     return data == '1' ? 'Paid' : 'Not paid';
                  }
               },
              { data: 'new_site_id',name: 'po.new_site_id',
                  render : function(data) {
                     return data != '0' ? 'Started' : 'Not Yet';
                  }
               },
              { data: 'id', name: 'po.id', 'searchable': false },
              
          ],
          "fnRowCallback": function( Row, data, iDisplayIndex ) {
                if ( data.new_site_id != "0" )
                {
                $('td:eq(7)', Row).html('<a href="site/'+data.new_site_id+'" target="_blank" class="fa fa-eye" title="View"></a> <a href="javascript:showitem('+data.id+')" title="Download"><i class="fa fa-file-text fa-2x" aria-hidden="true"></i></a> ');
                //$('td:eq(5)', Row).html('<a href="javascript:showitem('+data.id+')" class="fa fa-download" title="Edit">Doc.</a> ');
                return Row;
                }
                else
                {
                  $('td:eq(7)', Row).html('<a href="createsite/'+data.id+'" target="_blank" title="Clone"><i class="fa fa-cog fa-spin fa-fw"></i></a> <a href="javascript:showitem('+data.id+')" title="Download"><i class="fa fa-file-text fa-2x" aria-hidden="true"></i></a> ');
                return Row;
                }
            },  
      });
  });
function showitem(pid)
{
 /* var formdata = {"itemid": pid };
    $.ajax({
       headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       },

       type: "POST",
       url: "<?php echo e(route('siteforminfo')); ?>",  
       data: formdata,
       dataType: 'json',
       success: function(res){
        $('#itemid').val(pid);  
        $('#site_name').html(res[0]['site_name']);
        $('#site_category').html(res[0]['name']);
        $('#home_title').html(res[0]['home_title']);
        $('#home_text').html(res[0]['home_text']);
        $('#products_title').html(res[0]['products_title']);
        $('#contact_address').html(res[0]['contact_address']);
        $('#google_map').html(res[0]['google_map']);
        var imglink2= "<?php echo e(asset('src/uploads/doitforme')); ?>/"+res[0]['sliderFile'];
        $('#slider_image1').attr('src',imglink2);
        $('#sdown1').attr('href',imglink2);
       // $('#slider_image1').attr(res[0]['sliderFile']);
        //$('#site_category').html(res[0]['name']);
        //$('#site_category').html(res[0]['name']);   
          }
    });
    
  $("#myModal").modal();
  */
  window.location.href = baseUrl+'viewwebdata/'+pid;
}
</script>   

