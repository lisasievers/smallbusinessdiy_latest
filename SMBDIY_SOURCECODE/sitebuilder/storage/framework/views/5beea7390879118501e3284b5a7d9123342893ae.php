<link rel="stylesheet" href="<?php echo e(asset('src/css/jquery.dataTables.min.css')); ?>">

<div class="row">
  <div class="sitelist col-md-12">
<h2 class="page-header"><i class="fa fa-list-ul" aria-hidden="true"></i> Users, Featured from <span class="diy-color">I Will Do IT</span></h2>
  <table class="table table-bordered" id="users-table">
              <thead>
                <tr>
                    <th>Site Name</th>
                    <th>Belong User</th>
                    <th>Created Date</th>
                    <th>Payment</th>
                    <th>Publish</th>
                  <th>Action</th>
               </tr>
              </thead>
            </table>


  </div> 
</div>
<script src="<?php echo e(asset('src/js/jquery.dataTables.min.js')); ?>"></script> 
<script>
  $(function() {
      $('#users-table').DataTable({
          processing: true,
          serverSide: true,
          ajax: '<?php echo e(route('sitedatax')); ?>',
          columns: [
              { data: 'site_name', name: 'po.site_name' },
              //{ data: 'first_name', name: 'ur.first_name' },
              { data: 'first_name',name: 'ur.first_name',
                  render : function(data,type,full) {
                     return full.first_name+' '+full.last_name;
                  }
               },
              { data: 'created_at', name: 'po.created_at' },
              // { data: 'status', name: 'br.status',  'searchable': false },
              { data: 'status',name: 'br.status',
                  render : function(data) {
                     return data == '1' ? 'Paid' : 'Not paid';
                  }
               },
            //  { data: 'ftp_published', name: 'po.ftp_published', 'searchable': false },
              { data: 'ftp_published',name: 'po.ftp_published',
                  render : function(data) {
                     return data == '1' ? 'Published' : 'Yet to Publish';
                  }
               },
              { data: 'id', name: 'po.id', 'searchable': false },
              
          ],
          "fnRowCallback": function( Row, data, iDisplayIndex ) {
                $('td:eq(5)', Row).html('<a href="site/'+data.id+'" class="fa fa-eye fa-2x" title="View"></a> ');
                return Row;
            },
      });
  });
</script>   

