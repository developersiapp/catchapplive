@extends('frontend.layouts.app')
@section('title','Log In Here')
@section('sidebar')
    @parent
    {{--<p>This is appended to the master sidebar.</p>--}}
@endsection
<link rel="stylesheet" href="{{asset('bower_components/bootstrap/dist/css/bootstrap.min.css')}}">
<!-- Font Awesome -->
<link rel="stylesheet" href="{{asset('bower_components/font-awesome/css/font-awesome.min.css')}}">
<!-- Ionicons -->
<link rel="stylesheet" href="{{asset('bower_components/Ionicons/css/ionicons.min.css')}}">
<!-- Theme style -->
<link rel="stylesheet" href="{{asset('dist/css/AdminLTE.min.css')}}">
<link rel="stylesheet" href="{{asset('dist/css/skins/_all-skins.min.css')}}">

@section('content')

    <div class="login-box fadeInDown">
        <div class="logo_icon"><img src="{{asset('/dist/img/logo.png')}}"></div>
        <div class="login-logo">
            <a href="javascript:;"><b>Catch</b>APP</a>
        </div>
        <!-- /.login-logo -->
        <div class="login-box-body" id="formContent">
            <p class="login-box-msg">Sign in to start your session</p>

            @if(session()->has('message'))
                <div class="alert alert-danger" role="alert">
                    {{ session()->get('message') }}
                </div>
            @endif

            <form action="{{'' . env('APP_URL') . '/admin-login'}}" method="post" >
                {{ csrf_field() }}
                <div class="form-group has-feedback">
                    <input type="email" name="email" autocomplete="off" id="login" class="form-control" placeholder="Email">
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" autocomplete="off" id="password" name="password" class="form-control" placeholder="Password">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <!-- /.col -->
                    <div class="col-xs-12">
                        <button type="submit" class="btn btn-danger btn-block">Sign In</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

        </div>
        <!-- /.login-box-body -->
    </div>
@endsection

<style>



    body {
        height: 100vh;
        background: rgb(253,74,105);
        background: -moz-linear-gradient(left, rgba(253,74,105,1) 0%, rgba(241,165,125,1) 100%);
        background: -webkit-linear-gradient(left, rgba(253,74,105,1) 0%,rgba(241,165,125,1) 100%);
        background: linear-gradient(to right, rgba(253,74,105,1) 0%,rgba(241,165,125,1) 100%);
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fd4a69', endColorstr='#f1a57d',GradientType=1 );
    }

    #formContent {
        -webkit-box-shadow: 0 30px 60px 0 rgba(0,0,0,0.3);
        box-shadow: 0 30px 60px 0 rgba(0,0,0,0.3);
        text-align: center;
    }

    /* Simple CSS3 Fade-in-down Animation */
    .fadeInDown {
        -webkit-animation-name: fadeInDown;
        animation-name: fadeInDown;
        -webkit-animation-duration: 1s;
        animation-duration: 1s;
        -webkit-animation-fill-mode: both;
        animation-fill-mode: both;
    }

    @-webkit-keyframes fadeInDown {
        0% {
            opacity: 0;
            -webkit-transform: translate3d(0, -100%, 0);
            transform: translate3d(0, -100%, 0);
        }
        100% {
            opacity: 1;
            -webkit-transform: none;
            transform: none;
        }
    }

    @keyframes fadeInDown {
        0% {
            opacity: 0;
            -webkit-transform: translate3d(0, -100%, 0);
            transform: translate3d(0, -100%, 0);
        }
        100% {
            opacity: 1;
            -webkit-transform: none;
            transform: none;
        }
    }


</style>
