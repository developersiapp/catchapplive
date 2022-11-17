<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 5/6/19
 * Time: 2:41 PM
 */ ?>

    <!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>

<head>
    @include('backend.layouts.head')

</head>
<body class="hold-transition fixed skin-blue sidebar-mini" id="main-page-body">
@if (\Illuminate\Support\Facades\Session::has('admin_id'))

    <div class="wrapper">
        @include('backend.layouts.header')
        @include('backend.layouts.sidebar')
        <div class="content-wrapper">
            <section class="content-header">
                <h1>
                    @yield('title')
                </h1>
            </section>

            <section class="content container-fluid">
                @yield('content')

            </section>
        </div>
        @include('backend.layouts.footer')
    </div>
    <!-- <script src="{{asset('bower_components/jquery/dist/jquery.min.js')}}"></script>
<script src="{{asset('bower_components/bootstrap/dist/js/bootstrap.min.js')}}"></script> -->
    <script src="{{asset('dist/js/adminlte.min.js')}}"></script>
    <script src="{{asset('js/ajax.js')}}"></script>

    <!-- include libraries(jQuery, bootstrap) -->
    <!-- <link href="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css" rel="stylesheet"> -->
    <!-- <script src="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script> -->

    <!-- include summernote css/js -->
    <!-- <a href=”http://!!asset(‘/assets/css/summernote.min.css’)!!”>http://!!asset(‘/assets/css/summernote.min.css’)!!</a>
    <a href=”http://!!asset(‘/assets/js/summernote.min.js’)!!”>http://!!asset(‘/assets/js/summernote.min.js’)!!</a> -->

    <!-- <link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.css" rel="stylesheet">
    <script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.js"></script>
    <script>
        $(document).ready(function () {
            $('.summernote').summernote();
        });
    </script> -->

    {{--@include('backend.layouts.js')--}}
    {{--    @include('backend.layouts.css')--}}

@else


    @if (!\Illuminate\Support\Facades\Session::has('admin_id'))
        {{--<h3 class="alert alert-danger text-center">Sorry, You are not authorised to view this page!</h3>--}}
        {{--<div class="page-title text-center">--}}

        {{--<h3>Please Login!!</h3></div>--}}

        <script>
            var url = '{{ env('APP_URL') }}';

            window.location = url;
        </script>
    @endif
    {{--    {{\Illuminate\Support\Facades\Route::redirect('/')}}--}}
    <?php             return view('frontend.admin.login');
    ?>
@endif

@include('backend.layouts.notification')

</body>
<script type="text/javascript">

    $('.summernote').summernote();
    $( document ).ready(function() {
        $('.summernote').summernote();
//        sidebar active links code
        var url = window.location;
        $('ul.sidebar-menu li a').filter(function () {
            return this.href == url;
        }).parent().addClass('active');
        $('ul.treeview a').filter(function () {
            return this.href == url;
        }).parent().addClass('active');

        $('ul.treeview-menu a').filter(function() {
            return this.href == url;
        }).parentsUntil(".sidebar-menu > .treeview-menu").addClass('active');


//        close modal when user clicks outside anywhere
        var modal = document.getElementsByClassName('modal');
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

    });
</script>
</html>
