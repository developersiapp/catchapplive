<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 10/6/19
 * Time: 1:07 PM
 */ $count=1;?>
@extends('backend.layouts.default')
@section('title','Settings')
@section('title','Emails')

@section('content')
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
               <p class="pull-right">
                   <a href="{{url('' . env('APP_URL') . '/dashboard/settings/new-email')}}"
                      class="btn btn-xs btn-primary pull-left">
                       <i class="fa fa-plus"></i><span> Create New Email</span>
                   </a>
               </p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="table-responsive no-padding">
                    <table class="table table-hover">
                        <tr>
                            <th>ID</th>
                            <th>To</th>
                            <th>Subject</th>
                            <th>Email</th>
                            <th>Email Type</th>
                            <th>Created On</th>
                            <th>Action</th>
                        </tr>
                        @if($emails->count()>0)
                            @foreach($emails as $email)
                                <tr>
                                    <td>{{$count++}}</td>
                                    <td>{{$email->mail_to}}</td>
                                    <td>{{$email->mail_subject}}</td>
                                    <td>{!! $email->mail_content !!}</td>
                                    <td>{{isset($email->email_type)?($email->email_type!=null?  \catchapp\Models\EmailConfiguration::$email_types{$email->email_type}:'-'):'' }}</td>
                                    <td>{{ date('F d, Y', strtotime($email->created_at)) }}</td>
                                    <td>                                        @if($email->is_sent!=1)

                                        <a href="{{url('' . env('APP_URL') . '/dashboard/settings/edit-mail/'. $email->id)}}"
                                           class="btn btn-xs btn-primary">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                            <a href="{{url('' . env('APP_URL') . '/dashboard/email/send-mail/'. $email->id)}}"
                                               class="btn btn-xs btn-success">
                                                <i class="fa fa-send"><span> Send Mail</span></i>
                                            </a>
                                        @endif
                                        <a href="{{url('' . env('APP_URL') . '/dashboard/settings/delete-mail/'. $email->id)}}"
                                           onclick="return confirm('Do you really want to delete this E-mail?')"
                                           class="btn btn-xs btn-danger">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="alert_info">
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