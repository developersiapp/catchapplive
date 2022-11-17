@extends('frontend.layouts.app')
@section('title',isset($page)?($page->id==1?'Privacy Policy':'Terms & Conditions'):'Page')
<link rel="stylesheet" href="{{asset('bower_components/bootstrap/dist/css/bootstrap.min.css')}}">
<!-- Font Awesome -->
<link rel="stylesheet" href="{{asset('bower_components/font-awesome/css/font-awesome.min.css')}}">
<!-- Ionicons -->
<link rel="stylesheet" href="{{asset('bower_components/Ionicons/css/ionicons.min.css')}}">
<!-- Theme style -->
<link rel="stylesheet" href="{{asset('dist/css/AdminLTE.min.css')}}">
<link rel="stylesheet" href="{{asset('dist/css/skins/_all-skins.min.css')}}">

@section('content')
    <div>
        <p>
        <h1>     {!! $page->page_content !!}
        </h1></p>
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
