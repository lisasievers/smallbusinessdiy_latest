@extends ('layouts.dashboard')

@section('section')
<div class="col-sm-12 ">
<style>
table.table tbody td:nth-child(4){text-align: right;width:12%;}
</style>
<div class="row">
  <div class="sitelist col-md-10">
<h2 class="page-header"> My Payments </h2>
  <table class="table table-bordered  table-striped" id="users-table">
              <thead>
                <tr>
                  <th>Payment Ref. No.</th>
                    <th>Function A/c</th>
                    
                    <th>Paid date</th>
                    <th>Paid Amt. ($)</th>
               </tr>
              </thead>
              <tbody>
               
                @foreach($data['user_sub'] as $usc)
                 <tr>
                  <td>{{$usc->stripeToken}}</td>
                    <td><b>{{$usc->name}}</b> Subscription - Website Reports</td>
                    
                    <td>{{$usc->date_time}}</td>
                    <td>{{$usc->amount}}</td>
               </tr>
               <span style="visibility:hidden">{{ $sum=$sum+$usc->amount }}</span>
                @endforeach
                @foreach($data['user_sites'] as $usc)
                 <tr>
                  <td>{{$usc->stripeToken}}</td>
                    <td><b>{{$usc->site_name}}</b> Website Builder</td>
                    
                    <td>{{$usc->date_time}}</td>
                    <td>{{$usc->amount}}</td>
               </tr>
             <span style="visibility:hidden">{{ $sum=$sum+$usc->amount }}</span>
                @endforeach
              </tbody>
              
              <tfoot>
                  <tr>
                <td></td><td></td><td>Grand Total ($)</td><td>{{$sum}}</td>
              </tr>
              </tfoot>
            </table>


  </div> 
</div>
  
</div>
   </div>
   <div class="clear"></div>       
</div>

@stop
