<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 6/6/19
 * Time: 4:18 PM
 */
$count=1;?>
@extends('backend.layouts.default')
@section('title','Djs')
@section('content')
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <p class="pull-right">
                    <a href="{{url('' . env('APP_URL') . '/dashboard/djs/add-new')}}"
                       class="btn btn-xs btn-success">
                        <i class="fa fa-plus"></i><span> Add New DJ</span>
                    </a>
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="table-responsive no-padding">
                    <table class="table table-bordered data-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>User Name</th>
                            <th>Email</th>
                            <th>Assigned Club</th>
                            <th>Registered On</th>
                            <th>Action</th>
                        </tr></thead>
                        <tbody></tbody>
                        @if($djs->count()>0)
                            @foreach($djs as $dj)
                                <tr>
                                    <td>{{$count++}}</td>
                                    <td>{{$dj->name}}</td>
                                    <td>{{$dj->user_name}}</td>
                                    <td>{{$dj->email}}</td>
                                    <td>{{$dj->assigned_club->name}}</td>
                                    <td>{{ date('F d, Y', strtotime($dj->created_at)) }}</td>
                                    <td>
                                        <a href="{{url('dashboard/djs/edit-dj/'. $dj->id)}}"
                                           class="btn btn-xs btn-primary">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                      <a href="{{url('dashboard/djs/change-password/'. $dj->id)}}"
                                           class="btn btn-xs btn-primary">
                                            <i class="fa fa-key"><span> Change Password</span></i>
                                        </a>
                                        <a href="{{url('dashboard/djs/assign-club/'. $dj->id)}}"
                                           class="btn btn-xs btn-success">
                                            <i class="fa fa-bars"><span> Assign Club</span></i>
                                        </a>
                                        <a href="{{url('dashboard/djs/delete-dj/'. $dj->id)}}"
                                           onclick="return confirm('Do you really want to delete this DJ?')"
                                           class="btn btn-xs btn-danger">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr >
                                <td colspan="7" class="alert_info">
                                    <h4> NO RECORD FOUND</h4>
                                </td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection