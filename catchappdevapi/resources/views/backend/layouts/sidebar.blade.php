<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 5/6/19
 * Time: 11:21 AM
 */ ?>
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar Menu -->
        <ul class="sidebar-menu" data-widget="tree">

            @if(session('admin_id'))
                <?php $admin = \catchapp\Models\AdminUser::query()->find(session('admin_id')); ?>

                <li class="header">Manage</li>
                <li class=""><a href="{{route('dashboard.index')}}"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>

                <!-- Optionally, you can add icons to the links -->
                <li class="treeview">
                    <a href="#"><i class="fa fa-users"></i> <span>Users</span>
                        <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="{{url('' . env('APP_URL') . '/dashboard/users')}}"><i class="fa fa-user"></i> <span>Users</span></a></li>
                        <li><a href="{{url('' . env('APP_URL') . '/dashboard/stories')}}"><i class="fa fa-circle"></i> <span>User Stories</span></a></li>
                    </ul>
                </li>

                <li class=""><a href="{{url('' . env('APP_URL') . '/dashboard/clubs')}}"><i
                            class="fa fa-building"></i> <span>Clubs</span></a></li>
                <li><a href="{{url('' . env('APP_URL') . '/dashboard/djs')}}"><i class="fa fa-music"></i> <span>DJs</span></a></li>
                <li class="treeview">
                    <a href="#"><i class="fa fa-gears"></i> <span>Settings</span>
                        <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="{{url('' . env('APP_URL') . '/dashboard/admin-profile')}}"><i class="fa fa-user"></i> <span>My Profile</span></a></li>
                        <li><a href="{{url('' . env('APP_URL') . '/dashboard/email-configuration')}}"><i class="fa fa-envelope"></i> <span>Emails</span></a></li>
                        <li><a href="{{url('' . env('APP_URL') . '/dashboard/email-types')}}"><i class="fa fa-list-alt"></i> <span>Email Types</span></a></li>
                        {{--                    <li><a href="{{url('' . env('APP_URL') . '/dashboard/email-addresses')}}"><i class="fa fa-address-card"></i> <span>Email Address</span></a></li>--}}
                        <li><a href="{{url('' . env('APP_URL') . '/dashboard/insights')}}"><i class="fa fa-bar-chart"></i> <span>Insights</span></a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="#"><i class="fa fa-file-text "></i> <span>Pages</span>
                        <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="{{url('' . env('APP_URL') . '/dashboard/page/tnc-page')}}"><i class="fa fa-paperclip"></i> <span>TnC Page</span></a></li>
                        <li><a href="{{url('' . env('APP_URL') . '/dashboard/page/privacy-policy-page')}}"><i class="fa fa-paperclip"></i> <span>Privacy Policies Page</span></a></li>
                    </ul>
                </li>
                <li class=""><a href="{{url('' . env('APP_URL') . '/dashboard/feedbacks')}}"><i
                            class="fa fa-envelope"></i> <span>Feedbacks</span></a></li>

            @endif
        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>
