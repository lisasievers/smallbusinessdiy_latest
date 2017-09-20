<div class="row">
                
                @if ($message = Session::get('error'))
                <div class="custom-alerts alert alert-danger fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                    {!! $message !!}
                </div>
                <?php Session::forget('error');?>
                @endif
        <div class="col-xs-12 col-md-6 col-md-offset-2">
            <form class="form-horizontal" method="post" id="payment-form" role="form" action="{{ route('addmoney.stripe') }}" >
                        {{ csrf_field() }}
            <input id="email" type="hidden" class="form-control" name="email" value="{{ Session::get('email') }}" />  
            <input id="site" type="hidden" class="form-control" name="site" value="{{$site_id}}" />
            <input id="sitename" type="hidden" class="form-control" name="sitename" value="{{ $site_name }} " />
                                      
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Payment Details
                    </h3>
                    <!--<div class="checkbox pull-right">
                        <label>
                            <input type="checkbox" />
                            Remember
                        </label>
                    </div>-->
                </div>
                <div class="panel-body">
                    
                    <div class="form-group">
                        <label for="cardNumber">
                            CARD NUMBER</label>
                        <div class="input-group{{ $errors->has('card_no') ? ' has-error' : '' }}">
                             <input type="hidden" name="_token" value="{{ Session::token() }}">
                             <input id="card_no" type="text" class="form-control" placeholder="Valid Card Number" name="card_no" value="{{ old('card_no') }}" autofocus>
                                @if ($errors->has('card_no'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('card_no') }}</strong>
                                    </span>
                                @endif
                            <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-8 col-md-8">
                            <label for="expityMonth">
                                    EXPIRY DATE</label>
                            <div class="form-group">
                                
                                <div class="col-xs-6 col-lg-6 pl-ziro{{ $errors->has('ccExpiryMonth') ? ' has-error' : '' }}">
                                    <select id="ccExpiryMonth" class="form-control" name="ccExpiryMonth" autofocus >
                                        <option>Month</option>
                                        <option value="01">Jan (01)</option>
                                        <option value="02">Feb (02)</option>
                                        <option value="03">Mar (03)</option>
                                        <option value="04">Apr (04)</option>
                                        <option value="05">May (05)</option>
                                        <option value="06">June (06)</option>
                                        <option value="07">July (07)</option>
                                        <option value="08">Aug (08)</option>
                                        <option value="09">Sep (09)</option>
                                        <option value="10">Oct (10)</option>
                                        <option value="11">Nov (11)</option>
                                        <option value="12">Dec (12)</option>
                                      </select>
                                      @if ($errors->has('ccExpiryMonth'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('ccExpiryMonth') }}</strong>
                                    </span>
                                @endif
                                </div>
                                <div class="col-xs-6 col-lg-6 pl-ziro{{ $errors->has('ccExpiryYear') ? ' has-error' : '' }}">
                                    <select id="ccExpiryYear" class="form-control" name="ccExpiryYear" autofocus>
                                            <option value="">Year</option>
                                              <?php
                                                  for($i = 2017; $i < date("Y")+20; $i++){
                                                      echo '<option value="'.$i.'">'.$i.'</option>';
                                                  }
                                            ?>
                                            </select>
                                   @if ($errors->has('ccExpiryYear'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('ccExpiryYear') }}</strong>
                                    </span>
                                @endif         
                            </div>
                        </div>
                        </div>
                        <div class="col-xs-4 col-md-4 pull-right">
                            <div class="form-group{{ $errors->has('cvvNumber') ? ' has-error' : '' }}">
                                <label for="cvvNumber">
                                    CV CODE</label>
                                <input id="cvvNumber" type="text" class="form-control" name="cvvNumber" value="{{ old('cvvNumber') }}" autofocus>
                                @if ($errors->has('cvvNumber'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('cvvNumber') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                       <div class="form-group{{ $errors->has('amount') ? ' has-error' : '' }}">
                            
                            <div class="col-md-6">
                                <input id="amount" type="hidden" class="form-control" name="amount" value="{{ $ncost }}" />
                                @if ($errors->has('amount'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('amount') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    

                    
                </div>
            </div>
        </div>
            <ul class="nav nav-pills nav-stacked">
                <li class="active"><a href="#"><span class="badge pull-right"><span class="glyphicon glyphicon-usd"></span>{{$ncost}}</span> Final Payment</a>
                </li>
            </ul>
            <br/>
            <button type="submit" class="btn btn-success btn-lg btn-block">Pay</button>
            </form>
        </div>
    </div>

