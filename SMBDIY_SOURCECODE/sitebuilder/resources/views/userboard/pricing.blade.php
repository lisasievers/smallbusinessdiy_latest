@extends ('layouts.dashboard')

@section('section')
<link rel="stylesheet" href="{{ asset('src/css/pricing/pricing_v3.css') }}">
<div class="col-sm-12 ">
  
<!-- Pricing Table v3-->
      <div class="row pricing-table-v3 no-space-pricing">
       @if( isset($data['user_sub']) && count( $data['user_sub'] ) > 0 )
       <h2>Your Active Subscribe Package</h2>
        <div class="col-md-4 col-sm-4 col-xs-6">
           @foreach($data['user_sub'] as $s)
          <div class="pricing-v3 pricing-v3-dark-blue{{$s->id}}">
            <div class="pricing-v3-head text-center">
             
              <h4>{{$s->name }}</h4>
              <h5><span>$</span>{{$s->amount}}<i></i></h5>
            </div>
            <ul class="list-unstyled pricing-v3-content">
              
              <li>Paid on<i>{{$s->date_time}}</i></li>
              <li>Expire<i>{{$s->exdate_time}}</i></li>
               <li>Status<i>Active</i></li> 
            </ul>
            <p>{{$s->about}}</p>
            
          </div>
          @endforeach
        </div>
       @else
       <h2>Find a plan that's right for you</h2>
        @foreach($data['packages'] as $p)
        <div class="col-md-4 col-sm-4 col-xs-6">
          <div class="pricing-v3 pricing-v3-dark-blue{{$p['id']}}">
            <div class="pricing-v3-head text-center">
              <h4>{{$p['name']}}</h4>
              <h5><span>$</span>{{$p['price']}}<i>P/Month</i></h5>
            </div>
            <ul class="list-unstyled pricing-v3-content">
              <li>Validity<i>{{$p['validity']}}</i></li>
              <li>Months<i>3</i></li>
             
           
            </ul>
            <p>{{$p['about']}}</p>
            <div class="pricing-v3-footer text-center">
              <a href="{{ route('user-reports-addition',['sub_id' => $p['id']]) }}" class="btn-u{{$p['id']}} btn-u-dark-blue btn-block">Purchase Now</a>
            </div>
          </div>
        </div>
        @endforeach
        
        @endif
      </div><!--/row-->
  
</div>
   </div>
   <div class="clear"></div>       
</div>

@stop
