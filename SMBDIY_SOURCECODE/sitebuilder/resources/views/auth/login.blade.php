@extends('layouts.login')

@section('title')
Login | SMBDIY
@endsection

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4 loginbox">
            
            @if( Session::has('success') )
            <div class="alert alert-success">
                <button type="button" class="close fui-cross" data-dismiss="alert"></button>
                {{ Session::get('success') }}
            </div>
            @endif
            @if( Session::has('error') )
            <div class="alert alert-error">
                <button type="button" class="close fui-cross" data-dismiss="alert"></button>
                {{ Session::get('error') }}
            </div>
            @endif
            @if( Session::has('message') )
            <div class="alert alert-error">
                <button type="button" class="close fui-cross" data-dismiss="alert"></button>
                {{ Session::get('message') }}
            </div>
            @endif
            <form role="form" action="{{ route('signin') }}" method="post">
                <div class="input-group {{ $errors->has('email') ? 'has-error' : '' }}">
                    
                    <input type="email" class="form-control" id="email" name="email" tabindex="1" autofocus placeholder="Your email address *" />
                </div>
                <div class="input-group">
                   
                    <input type="password" class="form-control" id="password" name="password" tabindex="2" placeholder="Your password *" />
                </div>
                <!--<label class="checkbox margin-bottom-20" for="checkbox1">
                    <input type="checkbox" value="1" id="remember" name="remember" tabindex="3" data-toggle="checkbox">
                    Remember me
                </label>-->
                <input type="hidden" name="_token" value="{{ Session::token() }}">
                <button type="submit" class="btn btn-primary btn-block btn-embossed" tabindex="3">Log me in<span class="fui-arrow-right"></span></button>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <a href="{{ route('forgot.password') }}">Lost your password?</a>
                    </div>
                </div><!-- /.row -->
            </form>
            <div class="divider">
                <span>OR</span>
            </div>
            <h2 class="text-center margin-bottom-25">
                <a class="btn-facebook" href="facebook"> Login with Facebook</a>
            </h2>
            <div class="text-center">Don't have account? <a href="{{ route('signup') }}" class="">Signup Now </a></div>
        </div><!-- /.col -->
    </div><!-- /.row -->
</div><!-- /.container -->
@endsection