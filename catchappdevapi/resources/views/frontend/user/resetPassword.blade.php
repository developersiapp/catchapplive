@extends('frontend.layouts.app')
@section('title','Reset Password')
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

        <div class="logo_icon"><img alt="catchapp" src="{{asset('/dist/img/logo.png')}}"></div>

        @if(session()->has('error'))
            <div class="login-logo">
                <a href="javascript:;" style="color: #fd4b6a;"><b>Catch</b>APP</a>
            </div>
            <div class="login-box-body" id="formContent">
                <p class="login-box-msg" style="color: #080808;text-shadow: #FEFFFD;">{{ session()->get('error') }}</p>
            </div>
        @elseif(session()->has('success'))
            <div class="login-logo">
                <a href="javascript:;" style="color: #fd4b6a;"><b>Catch</b>APP</a>
            </div>
            <div class="login-box-body" id="formContent">
                <p class="login-box-msg" style="color: #080808;text-shadow: #FEFFFD;">{!! session()->get('success') !!}</p>
            </div>

        @else
            <div class="login-logo">
                <a href="javascript:;" style="color: #fd4b6a;"><b>Catch</b>APP</a>
                <a href="javascript:;" style="color: #080808;text-shadow: #FEFFFD;">{{isset($user)?($user->is_dj==1?'Hi DJ, ':'Hi User, '):''}}Reset Your Password</a>
            </div>
            <!-- /.login-logo -->
            <div class="login-box-body" id="formContent">
                <p class="login-box-msg"   style="color: #fd4b6a;">Set your new password here!</p>

                @if(session()->has('message'))
                    <div class="alert alert-danger" role="alert" style="text-align: center;color: #080808;text-shadow: #FEFFFD;">
                        {{ session()->get('message') }}
                    </div>
                @endif
                <form action="{{'' . env('APP_URL') . '/user/update-password'}}" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{isset($user)?$user->id:''}}">
                    <input type="hidden" name="is_dj" value="{{isset($user)?$user->is_dj:''}}">
                    <div class="form-group has-feedback">
                        <input type="password" name="new_password" value="{{old('new_password')}}" autocomplete="off"
                               id="login" class="form-control" placeholder="Enter new password">
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        @error('new_password')
                        <span class="error-block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group has-feedback">
                        <input type="password" autocomplete="off" value="{{old('confirm_password')}}"
                               id="confirm_password" name="confirm_password" class="form-control"
                               placeholder="Confirm new password">
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        @error('confirm_password')
                        <span class="error-block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="row">
                        <!-- /.col -->
                        <div class="col-xs-12">
                            <button type="submit" class="btn btn-danger btn-block">Set Password</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
            </div>
    @endif
    <!-- /.login-box-body -->
    </div>
@endsection

<style>


    body {
        height: 100vh;
        /*background: rgb(253, 74, 105);*/
        /*background: -moz-linear-gradient(left, rgba(253, 74, 105, 1) 0%, rgba(241, 165, 125, 1) 100%);*/
        /*background: -webkit-linear-gradient(left, rgba(253, 74, 105, 1) 0%, rgba(241, 165, 125, 1) 100%);*/
        /*background: linear-gradient(to right, rgba(253, 74, 105, 1) 0%, rgba(241, 165, 125, 1) 100%);*/
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fd4a69', endColorstr='#f1a57d', GradientType=1);
    }

    #formContent {
        -webkit-box-shadow: 0 30px 60px 0 rgba(0, 0, 0, 0.3);
        box-shadow: 0 30px 60px 0 rgba(0, 0, 0, 0.3);
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
