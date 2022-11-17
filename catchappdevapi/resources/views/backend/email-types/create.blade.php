<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 26/6/19
 * Time: 10:54 AM
 */ ?>
@extends('backend.layouts.modal')
@section('content')
<form class="email-type-form" id="email-type-form"  action="{{url('' . env('APP_URL') . '/dashboard/email-type/save')}}" method="post">
    {{ csrf_field() }}
    <input type="hidden" id="id" class="id" name="id" value="{{isset($emailType)?$emailType->id:''}}">
    <input type="hidden" id="aid" class="aid" name="aid" value="">
    <div class="form-group">
            <label class="control-label">
                 Title
            </label>
            <input type="text" class="form-control name" name="name" id="name"
                   value="{{ isset($emailType)?$emailType->name:old('name') }}"
                   placeholder="Enter Email Type"/>
        <span class="error-block"></span>
            </div>
    <div class="form-group">
        <label class="control-label">
            Email Address :
        </label>
        <input type="email" class="form-control" name="email_address"  autocomplete="off" id="email_address"
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
        {{--<textarea class="form-control" id="summary-ckeditor" name="summary-ckeditor"></textarea>--}}
        <textarea class="form-control summernote" id="summernote" name="email_template">{!! isset($email_address)?$email_address->template:'' !!}</textarea>
        @error('email_template')
        <span class="error-block">{{ $message }}</span>
        @enderror
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary form-submit">Save</button>
    </div>
</form>

@endsection