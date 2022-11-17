<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 6/6/19
 * Time: 5:36 PM
 */ ?>

@extends('backend.layouts.default')
@section('title','Users')
@section('content')
    <div class="row">
        <div class="col-xs-3"></div>
        <div class="col-md-6 col-md-12">
            <form action="{{url('' . env('APP_URL') . '/dashboard/user/update-password')}}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{isset($user)?$user->id:''}}">
                <div class="box-body">
                    <div class="col-xs-12 form-group">
                        <div class="col-xs-6">
                            <label class="control-label">
                                New Password :
                            </label>
                            <input value="{{old('new_password') }}" type="password"
                                   class="form-control" name="new_password"
                                   placeholder="Enter New Password ">

                            @error('new_password')
                            <span class="error-block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-xs-6">
                            <label class="control-label">
                                Re-enter New Password :
                            </label>
                            <input value="{{old('repeat_password') }}" type="password"
                                   class="form-control" name="repeat_password"
                                   placeholder="Re-enter Your New Password ">

                            @error('repeat_password')
                            <span class="error-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="footer">
                    <button type="submit" class="btn btn-primary">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
