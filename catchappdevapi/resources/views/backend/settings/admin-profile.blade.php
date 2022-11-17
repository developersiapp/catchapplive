<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 7/6/19
 * Time: 2:55 PM
 */ ?>
@extends('backend.layouts.default')
@section('title','Settings')
@section('content')
<div class="row">
    <div class="col-lg-6 col-md-9">
        <!-- general form elements -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Profile</h3>
            </div>


            <!-- /.box-header -->
            <!-- form start -->
            <form action="{{url('' . env('APP_URL') . '/dashboard/admin/save-admin-profile')}}" enctype="multipart/form-data" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{isset($admin)?$admin->id:''}}">
                <div class="box-body">
                    <div class="form-group position-relative">
                        <label class="control-label">
                            Profile Image
                        </label>
                        <div>


                            @if (isset($admin))
                            @if (!empty($admin->profile_image) && $admin->profile_image!= null && $admin->profile_image != " ")
                            <img class="img-circle img-lg img-bordered" id="blah"
                                 src={!! url('/uploads/admins/'.$admin->profile_image) !!}  class="img-circle"
                            alt="Admin Image">
                            @else
                            <img class="img-circle img-lg img-bordered" id="blah"
                                 src={!! url('/dist/img/user.png') !!}  class="img-circle"
                            alt="Admin Image">
                            @endif
                            @else
                            <img class="img-circle img-lg img-bordered" id="blah"
                                 src={!! url('/dist/img/user.png') !!}  class="img-circle"
                            alt="Admin Image">
                            @endif
                        </div>
                        <div class="browse_image">
                            <input type="file" value="{{old('admin_image')}}"
                                   onchange="readURL(this)"
                                   name="admin_image" class="form-control mr-5px">
                            @error('admin_image')
                            <span class="error-block">{{ $message }}</span>
                            @enderror

                            @if(isset($admin))
                            @if($admin->profile_image!='')
                            <a href="{{url('' . env('APP_URL') . '/dashboard/admin/remove-profile-image/'. $admin->id)}}"
                               onclick="return confirm('Do you really want to delete this profile photo?')"
                               class="btn btn-sm btn-danger">
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
                                    Name
                                </label>
                                <input type="text" class="form-control" name="name"
                                       value="{{ old('name')?old('name'):$admin->name }}"
                                       placeholder="Enter Name"/>

                                @error('name')
                                <span class="error-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-xs-6">
                                <label class="control-label">
                                    Email Address
                                </label>
                                <input type="email" class="form-control" name="email" autocomplete="off"
                                       value="{{ old('email')?old('email'):$admin->email }}"
                                       placeholder="ex : example@abc.com">

                                @error('email')
                                <span class="error-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    </div>
                    <div class="form-group">
                            <span class="checkbox"><label><input type="checkbox" id="change_password" name="change_password">
                                    {{--<i class="fa fa-key"></i> --}}
                                    Change Password</label></span>
                    </div>
                    <div class="tab-content form-group" id="div_changePassword" style="display: none;">
                        <div class="row">
                            <div class="col-xs-6">
                                <label class="control-label">
                                    New Password
                                </label>
                                <input value="" type="password" autocomplete="off"
                                       class="form-control" name="new_password"
                                       placeholder="Enter Your New Password ">
                                @error('new_password')
                                <span class="error-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-xs-6">
                                <label class="control-label">
                                    Repeat Password
                                </label>
                                <input value="" type="password" autocomplete="off"
                                       class="form-control" name="repeat_password"
                                       placeholder="Re-enter Your New Password ">
                                @error('repeat_password')
                                <span class="error-block">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>
                <div class="box-footer">

                    <a href="{{url('#')}}" id="openModal"
                       data-toggle="modal" data-target="#view_confirm_password"
                       class="btn btn-sm btn-primary"> Save Changes
                    </a>
                </div>
                <!--- ENTER PASSWORD MODAL --->
                <div class="modal fade" id="view_confirm_password" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">

                            <div class="modal-body">
                                <div class="modal-header">
                                    <button type="button" id="closeModal" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">Enter Admin Password :</h4>
                                </div>
                                <label class="control-label">
                                    Password
                                </label>
                                <input value="{{old('old_password') }}" type="password" autocomplete="off"
                                       class="form-control" name="old_password"
                                       placeholder="Enter Your Password">
                                <span class="valid_info">Enter your password to save changes</span>
                                @error('old_password')
                                <span class="error-block">{{ $message }}</span>
                                @enderror
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">
                                        Save
                                    </button>
                                    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>

                                </div> </div>

                        </div>
                    </div>
                </div>
                <!--- END MODAL --->
            </form>
        </div>
    </div>
</div>
@if (count($errors) > 0)
<script>
    $( document ).ready(function() {
        $('#view_confirm_password').modal('show');
    });
</script>
@endif
<script>
    $(document).ready(function() {
        var x = document.getElementById("view_confirm_password");
        $("#change_password").click(function () {
            $("#div_changePassword").slideToggle("slow");
        });
    });
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

<style>
    .showMOdal{

        display: block;;
    }
    .hideModal{

        display: block;;
    }
</style>
@endsection
