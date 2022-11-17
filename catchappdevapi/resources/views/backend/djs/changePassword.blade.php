<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 6/6/19
 * Time: 5:36 PM
 */ ?>

@extends('backend.layouts.default')
@section('title','Djs')
@section('content')
    <div class="row">
        <div class="col-xs-3"></div>
        <div class="col-md-6 col-md-12">            <form action="{{url('' . env('APP_URL') . '/dashboard/djs/update-password')}}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{isset($dj)?$dj->id:''}}">
                <div class="box-body">
                    <div class="col-xs-12 form-group">
                        <label class="control-label">
                           New Password :
                        </label>
                        <input type="password" class="form-control" name="password"
                               placeholder="Enter Password "  minlength="6">
                        @error('password')
                        <span class="error-block">{{ $message }}</span>
                        @enderror
                    </div>
              <div class="col-xs-12 form-group">
                        <label class="control-label">
                           Confirm Password :
                        </label>
                        <input type="password" class="form-control" name="confirm_password"
                               placeholder="Re-enter Your Password "  minlength="6">
                        @error('confirm_password')
                        <span class="error-block">{{ $message }}</span>
                        @enderror
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
