<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 5/6/19
 * Time: 4:19 PM
 */ ?>
@extends('backend.layouts.default')
@section('title','Users')
@section('content')

<div class="row">
    <div class="col-lg-6 col-md-9">
        <!-- general form elements -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">{{isset($user)?'Edit User':'Create New User'}}</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form action="{{url('' . env('APP_URL') . '/dashboard/users/save-user')}}"  enctype="multipart/form-data" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{isset($user)?$user->id:''}}">
                <div class="box-body">
                    <div class="form-group position-relative">
                        <label class="control-label">
                            Profile Image
                        </label>
                        <div>
                            @if (isset($user))
                            @if (!empty($user->profile_image) && $user->profile_image!= null && $user->profile_image != " ")
                            <img class="img-circle img-lg img-bordered" id="blah"
                                 src={!! url('/uploads/users/'.$user->profile_image) !!}  class="img-circle"
                            alt="User Image">
                            @else
                            <img class="img-circle img-lg img-bordered" id="blah"
                                 src={!! url('/dist/img/user.png') !!}  class="img-circle"
                            alt="User Image">
                            @endif
                            @else
                            <img class="img-circle img-lg img-bordered" id="blah"
                                 src={!! url('/dist/img/user.png') !!}  class="img-circle"
                            alt="User Image">
                            @endif

                        </div>
                        <div class="browse_image">
                            <input type="file" name="user_image" class="form-control mr-5px" onchange="readURL(this);">
                            @error('user_image')
                            <span class="error-block">{{ $message }}</span>
                            @enderror

                            @if(isset($user))
                            @if (!empty($user->profile_image) && $user->profile_image!= null && $user->profile_image != " ")
                            <a href="{{url('' . env('APP_URL') . '/dashboard/users/remove-profile-image/'. $user->id)}}"
                               onclick="return confirm('Do you really want to delete this profile photo?')"
                               class="btn btn-xs btn-danger">
                                <i class="fa fa-image"></i><span> Remove Profile Photo</span>
                            </a>
                            @endif
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-6">
                                <label class="control-label">
                                    First Name
                                </label>
                                <input type="text" class="form-control" name="first_name" value="{{ isset($user)?$user->first_name:old('first_name') }}"
                                       placeholder="Enter First Name"/>

                                @error('first_name')
                                <span class="error-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-xs-6">
                                <label class="control-label">
                                    Last Name
                                </label>
                                <input type="text" class="form-control" value="{{ isset($user)?$user->last_name:old('last_name') }}"
                                       name="last_name" placeholder="Enter Last Name">

                                @error('last_name')
                                <span class="error-block">{{ $message }}</span>
                                @enderror
                            </div>
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
                                        <input  type="radio" name="gender" id="female" value="female"
                                                {{isset($user)?($user->gender=="female"?'checked':''):old('gender')=="female" ? 'checked='.'"'.'checked'.'"' : ''}}
                                        >Female
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="gender" id="male" value="male"
                                               {{isset($user)?($user->gender=="male"?'checked':''):old('gender')=="male" ? 'checked='.'"'.'checked'.'"' : ''}}
                                        {{--                                                    {{old('gender')=="male" ? 'checked='.'"'.'checked'.'"' : ''}}--}}

                                        >Male
                                    </label>
                                    @error('gender')
                                    <span class="error-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <label class="control-label">
                                    Birth Date :
                                </label>
                                <input type="text" value="{{isset($user)?($user->birth_date!=null?\Carbon\Carbon::parse($user->birth_date)->format('d M, Y'):''):old('birthDate') }}"
                                       class="form-control datepicker" readonly name="birthDate">

                                @error('birthDate')
                                <span class="error-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    </div>
                    <div class="form-group">
                        <div class="row">
                            @if(isset($user))
                            <div class="col-xs-6">
                                <label class="control-label">
                                    Registered Through :
                                </label>
                                <span class="info">
                                    <b> {{isset($user->registeration_type)?($user->registeration_type<=5?\catchapp\Models\User::$registeration_type{$user->registeration_type}:'Admin' ) : 'Admin'}}</b>
                                    </span>

                                {{--                                    <select class="form-control" name="registeration_type" id="registeration_types">--}}
                                    {{--                                        @foreach(\catchapp\Models\User::$registeration_type as $key => $type)--}}
                                    {{--                                            <option value="{{$key}}"--}}
                                                                                            {{--                                                    {{isset($user)?($user->registeration_type==$key?'selected':''):''}}>--}}
                                        {{--                                                {{$type}}--}}
                                        {{--                                            </option>--}}
                                    {{--                                        @endforeach--}}
                                    {{--                                    </select>--}}
                                @error('registeration_type')
                                <span class="error-block">{{ $message }}</span>
                                @enderror
                            </div>
                            @endif
                            <div class="col-md-6">
                                <label class="control-label">
                                    User Name
                                </label>
                                <input type="text" class="form-control" name="user_name"
                                       onkeypress="return AvoidSpace(event)"
                                       value="{{ isset($user)?$user->user_name:old('user_name') }}"
                                       minlength="5"
                                       placeholder="Enter User Name"/>
                                @error('user_name')
                                <span class="error-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-6">
                                <label class="control-label">
                                    Email Address :
                                </label>
                                <input type="email" class="form-control" name="email"  autocomplete="off"
                                       value="{{ isset($user)?$user->email:old('email') }}"
                                       {{--                                    {{ isset($user->email)?'readonly':'' }}--}}
                                placeholder="ex : example@abc.com" >
                                @error('email')
                                <span class="error-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-xs-6">
                                <label class="control-label">
                                    Password :
                                </label>
                                <input autocomplete="off"  value="{{isset($user)?$user->password:old('password') }}"
                                       type="password" class="form-control" name="password"
                                       minlength="6" placeholder="Enter Password ">
                                @error('password')
                                <span class="error-block">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

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

<script type="text/javascript">
    function AvoidSpace(event) {
        var k = event ? event.which : window.event.keyCode;
        if (k == 32) return false;
    }



    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#blah').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection

