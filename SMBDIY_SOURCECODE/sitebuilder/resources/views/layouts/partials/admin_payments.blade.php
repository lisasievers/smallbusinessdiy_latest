<link rel="stylesheet" href="{{ asset('src/css/jquery.dataTables.min.css') }}">
<style>
table.dataTable tbody td:nth-child(5){text-align: right;width:12%;}
</style>
<div class="row">
  <div class="sitelist col-md-12">
<h2 class="page-header"> Payments</h2>
  <table class="table table-bordered  table-striped" id="users-table">
              <thead>
                <tr>
                    <th>Stripe Payment ID</th>
                    <th>Paid User</th>
                    <th>Paid Site</th>
                    <th>Payment Date</th>
                    <th>Amount ($)</th>
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
          ajax: '{{ route('paymentdatax') }}',
          columns: [
              { data: 'stripeToken', name: 'br.stripeToken' },
             { data: 'first_name',name: 'ur.first_name',
                  render : function(data,type,full) {
                     return full.first_name+' '+full.last_name;
                  }
               },
              { data: 'site_name', name: 'po.site_name', 'searchable': true },
              { data: 'date_time', name: 'br.date_time' },
              { data: 'amount', name: 'br.amount',  'searchable': false },
             
              { data: 'id', name: 'po.id', 'searchable': false },
              
          ],
          "fnRowCallback": function( Row, data, iDisplayIndex ) {
                $('td:eq(5)', Row).html('<a href="#" class="fa fa-pdf" title="Edit"><i class="fa fa-file-pdf-o fa-2x" aria-hidden="true"></i></a> ');
                return Row;
            },
      });
  });
</script>   

