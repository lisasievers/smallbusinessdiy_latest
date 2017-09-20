<link rel="stylesheet" href="{{ asset('src/css/jquery.dataTables.min.css') }}">
<style>
table.dataTable tbody td:nth-child(5){text-align: right;width:12%;}
</style>
<div class="row">
  <div class="sitelist col-md-12">
<h2 class="page-header"> Do IT For Me - Websites | Update Requirement Form </h2>
  <table class="table table-bordered  table-striped" id="users-table">
              <thead>
                <tr>
                    <th>Site Name</th>
                    <th>Paid Amt.</th>
                    <th>Payment Status</th>
                    <th>Posted at</th>
                    <th>Development State</th>
                  <th>Action</th>
               </tr>
              </thead>
            </table>


  </div> 
</div>
<script src="{{ asset('src/js/jquery.dataTables.min.js') }}"></script> 
<script>
  $(function() {
      $('#users-table').DataTable({
          processing: true,
          serverSide: true,
          ajax: '{{ route('MyDoITformeDatax') }}',
          columns: [
              { data: 'site_name', name: 'po.site_name' },
              { data: 'amount', name: 'br.amount', 'searchable': true },
              { data: 'status', name: 'br.status', 'searchable': false },
              { data: 'created_at', name: 'po.created_at',  'searchable': false },
              { data: 'new_site_id',name: 'po.new_site_id',
                  render : function(data) {
                     return data != '0' ? 'Started' : 'Not Yet';
                  }
               },
              { data: 'id', name: 'po.id', 'searchable': false }
              
          ],
          "fnRowCallback": function( Row, data, iDisplayIndex ) {
                if ( data.new_site_id != "0" )
                {
                $('td:eq(5)', Row).html('<a href="site/'+data.new_site_id+'" target="_blank" class="fa fa-eye fa-2x" title="View"></a> <a href="javascript:showitem('+data.id+')" title="Update"><i class="fa fa-edit fa-2x" aria-hidden="true"></i></a> ');
                }
                else
                {
                  $('td:eq(5)', Row).html('<a href="#" title="Idle"><i class="fa fa-tag"></i></a> <a href="javascript:showitem('+data.id+')" title="Update"><i class="fa fa-edit fa-2x" aria-hidden="true"></i></a> ');
                }
                 if ( data.status != "1" )
                {
                $('td:eq(2)', Row).html('Pending <a class="btn btn-primary btn-mini" href="paymentodo/'+data.id+'" title="pay">Pay</a> ');
                }
                else
                {
                  $('td:eq(2)', Row).html('Paid');
                }
               return Row;   
            },
 
      });
  });
function showitem(pid)
{
  window.location.href = baseUrl+'getwebdataform/'+pid;
}
</script>   

