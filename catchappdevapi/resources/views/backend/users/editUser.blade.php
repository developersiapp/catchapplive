<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 26/6/19
 * Time: 10:54 AM
 */ ?>
@extends('backend.layouts.modal')
@section('content')
<form action="{{'/user/save-user'}}" method="post">
    {{ csrf_field() }}
    <input type="hidden" name="id" id="{{isset($user)?$user->id:''}}">
    <div class="form-group">
        <label class="control-label">
            Profile Image
        </label>
        <div>
            <img class="img-circle img-lg img-bordered" id="img"
                 src={!! url(isset($user)?($user->profile_image!=''?'/uploads/users/'.$user->profile_image:'/dist/img/user.png')    :'/dist/img/user.png') !!}  class="img-circle"
                 alt="User Image">
        </div>
        <div class="browse_image">
            <input type="file" name="user_image" class="form-control mr-5px">
        </div>
    </div>
    <div class="form-group">
        <div class="col-xs-6">
            <label class="control-label">
                First Name
            </label>
            <input type="text" class="form-control" name="first_name" id="first_name"
                   value="{{ isset($user)?$user->first_name:old('first_name') }}"
                   placeholder="Enter First Name"/>
        </div>
        <div class="col-xs-6">
            <label class="control-label">
                Last Name
            </label>
            <input type="text" class="form-control"
                   value="{{ isset($user)?$user->last_name:old('last_name') }}"
                   name="last_name" placeholder="Enter Last Name" id="last_name">
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-xs-6">
                <label class="control-label">
                    Gender
                </label>
                <div class="radio">
                    <label class="radio-inline">
                        <input type="radio" name="gender" id="female"
                               value="female" {{isset($user)?($user->gender=="female"?'checked':''):''}}>Female
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="gender" id="male"
                               value="male" {{isset($user)?($user->gender=="male"?'checked':''):''}}>Male
                    </label>
                </div>
            </div>
            <div class="col-xs-6">
                <label class="control-label">
                    Birth Date :
                </label>
                <input id="birthdate" value="{{ isset($user)?$user->birth_date:old('birthDate') }}"
                       type="date" class="form-control" name="birthDate">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-xs-6">
                <label class="control-label">
                    Registeration Type :
                </label>
                <select class="form-control countries" name="registeration_type"
                        id="registeration_types">
                    @foreach(\catchapp\Models\User::$registeration_type as $key => $type)
                        <option value="{{$key}}" {{isset($user)?($user->registeration_type==$key?'selected':''):''}}>{{$type}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-xs-6">
                <label class="control-label">
                    Email Address :
                </label>
                <input type="text" class="form-control" name="email" id="email" autocomplete="off"
                       value="{{ isset($user)?$user->email:old('email') }}"
                       {{ isset($user->email)?'readonly':'' }}
                       placeholder="ex : example@abc.com">
            </div>
            <div class="col-xs-6">
                <label class="control-label">
                    Password :
                </label>
                <input autocomplete="off" id="password"
                       value="{{isset($user)?$user->password:old('password') }}" type="password"
                       class="form-control" name="password"
                       placeholder="Enter Password ">
            </div>

        </div>

    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
@endsection
