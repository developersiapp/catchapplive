<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 5/6/19
 * Time: 11:20 AM
 */ ?>
<header class="main-header">
    <!-- Logo -->
    <a href="{{route('dashboard.index')}}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini">
            <img src="{{asset('/dist/img/logo.png')}}"
                 class="brandlogo-image img img-sm"></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg">
            <img src="{{asset('/dist/img/logo.png')}}"
                 class="brandlogo-image img img-sm"><strong>Catch</strong>App</span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
            @if(session('admin_id'))
                <?php $admin = \catchapp\Models\AdminUser::query()->find(session('admin_id')); ?>
                <!-- User Account Menu -->
                    <li class="dropdown user user-menu">
                        <!-- Menu Toggle Button -->
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <!-- The user image in the navbar-->
                            @if (isset($admin))
                                @if (!empty($admin->profile_image) && $admin->profile_image!= null && $admin->profile_image != " ")
                                    <img src={!! url('/uploads/admins/'.$admin->profile_image) !!}  class="user-image" alt="Admin Image">
                                @else
                                    <img class="user-image"
                                         src={!! url('/dist/img/user.png') !!}
                                                 alt="Admin">
                                @endif
                            @else
                                <img class="user-image"
                                     src={!! url('/dist/img/user.png') !!}
                                             alt="Admin">
                        @endif

                        {{--                            <img src={!! url(isset($admin)?($admin->profile_image!=''?'/uploads/admins/'.$admin->profile_image:'/dist/img/user.png'):'/dist/img/user.png') !!} class="user-image" alt="User Image">--}}
                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                            <span class="hidden-xs">{{$admin?$admin->name:'ADMIN'}}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- The user image in the menu -->
                            <li class="user-header">


                                @if (isset($admin))
                                    @if (!empty($admin->profile_image) && $admin->profile_image!= null && $admin->profile_image != " ")
                                        <img src={!! url('/uploads/admins/'.$admin->profile_image) !!}  class="img-circle" alt="Admin Image">
                                    @else
                                        <img class="img-circle custom-img"
                                             src={!! url('/dist/img/user.png') !!}
                                                     alt="Admin">
                                    @endif
                                @else
                                    <img class="img-circle custom-img"
                                         src={!! url('/dist/img/user.png') !!}
                                                 alt="Admin">
                                @endif





                                {{--                                <img src={!! url(isset($admin)?($admin->profile_image!=''?'/uploads/admins/'.$admin->profile_image:'/dist/img/user.png'):'/dist/img/user.png') !!}  class="img-circle custom-img" alt="User Image">--}}
                                <p>
                                    {{$admin?$admin->name:'ADMIN'}}
                                    {{--<small>Member since Nov. 2012</small>--}}
                                </p>
                            </li>
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="{{url('' . env('APP_URL') . '/dashboard/admin-profile')}}" class="btn btn-default">Profile</a>
                                </div>
                                <div class="pull-right">
                                    <a href="{{'' . env('APP_URL') . '/admin-log-out'}}" class="btn btn-default">Sign out</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
</header>
