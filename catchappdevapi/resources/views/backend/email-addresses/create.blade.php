<?php
$email_types = \catchapp\Models\EmailType::query()->get();
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 6/6/19
 * Time: 12:31 PM
 */ ?>
@extends('backend.layouts.default')
@section('title','Email Addresses')
@section('content')

    <div class="row">
        <div class="col-lg-6 col-md-9">
            <!-- general form elements -->
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">New Email Address</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form action="{{url('' . env('APP_URL') . '/dashboard/email-addresses/save')}}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{isset($email_address)?$email_address->id:''}}">
                    <div class="box-body">

                        <div class="form-group">
                            <label class="control-label">
                                Email Type
                            </label>
                            <select class="form-control" name="email_type">
                                <option value="">Select Email Type</option>
                                <?php $count=1;?>
                                @foreach($email_types as $email_type)
                                    <option value="{{$email_type->id}}" {{isset($email_address)?(($email_address->email_type==$email_type->id)?'selected':''):old('email_type')}}>{{$count++}}. {{$email_type->name}}</option>
                                @endforeach
                            </select>
                            @error('email_type')
                            <span class="error-block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="control-label">
                                Email Address :
                            </label>
                            <input type="email" class="form-control" name="email_address"  autocomplete="off"
                                   value="{{ isset($email_address)?$email_address->email_address:old('email_address') }}"
                                   placeholder="ex : example@abc.com" >
                            @error('email_address')
                            <span class="error-block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group ">
                            <label class="control-label">
                                Email Template
                            </label>
                            <textarea maxlength="5" class="form-control summernote" id="summernote" name="email_template">{!! isset($email_address)?$email_address->template:'' !!}</textarea>
                            @error('email_template')
                            <span class="error-block">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
