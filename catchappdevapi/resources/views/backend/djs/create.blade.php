<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 6/6/19
 * Time: 4:30 PM
 */
?>
@extends('backend.layouts.default')
@section('title','Djs')
@section('content')

<div class="row">
    <div class="col-lg-6 col-md-9">
        <!-- general form elements -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">{{isset($dj)?'Edit DJ':'Create New DJ'}}</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form action="{{url('' . env('APP_URL') . '/dashboard/djs/save-dj')}}" method="post"
                  enctype="multipart/form-data">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{isset($dj)?$dj->id:''}}">
                <div class="box-body">
                    <div class="form-group position-relative">
                        <label class="control-label">
                            Profile Image
                        </label>
                        <div>
                            @if (isset($dj))
                            @if (!empty($dj->profile_image) && $dj->profile_image!= null && $dj->profile_image != " ")
                            <img class="img-circle img-lg img-bordered" id="blah"
                                 src={!! url('/uploads/djs/'.$dj->profile_image) !!}  class="img-circle"
                            alt="Dj Image">
                            @else
                            <img class="img-circle img-lg img-bordered" id="blah"
                                 src={!! url('/dist/img/user.png') !!}  class="img-circle"
                            alt="Dj Image">
                            @endif
                            @else
                            <img class="img-circle img-lg img-bordered" id="blah"
                                 src={!! url('/dist/img/user.png') !!}  class="img-circle"
                            alt="Dj Image">
                            @endif

                        </div>

                        <div class="browse_image">
                            <input type="file" name="dj_image" class="form-control mr-5px"  onchange="readURL(this);">
                            @error('dj_image')
                            <span class="error-block">{{ $message }}</span>
                            @enderror
                            @if(isset($dj))
                            @if (!empty($dj->profile_image) && $dj->profile_image!= null && $dj->profile_image != " ")
                            <a href="{{url('' . env('APP_URL') . '/dashboard/djs/remove-profile-image/'. $dj->id)}}"
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
                            <div class="col-md-6">
                                <label class="control-label">
                                    Name
                                </label>
                                <input type="text" class="form-control" name="name"
                                       value="{{ isset($dj)?$dj->name:old('name') }}"
                                       placeholder="Enter DJ's Name"/>
                                @error('name')
                                <span class="error-block">{{ $message }}</span>
                                @enderror
                            </div>  <div class="col-xs-6">
                                <label class="control-label">
                                    Birth Date :
                                </label>
                                <input type="text" value="{{ isset($dj)?$dj->birth_date!=null?\Carbon\Carbon::parse($dj->birth_date)->format('d M, Y'):'':old('birth_date') }}"
                                       class="form-control datepicker" readonly name="birth_date">

                                @error('birth_date')
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
                                                {{isset($dj)?($dj->gender=="female"?'checked':''):old('gender')=="female" ? 'checked='.'"'.'checked'.'"' : ''}}>Female
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="gender" id="male" value="male"
                                               {{isset($dj)?($dj->gender=="male"?'checked':''):old('gender')=="male" ? 'checked='.'"'.'checked'.'"' : ''}}>Male
                                    </label>
                                    @error('gender')
                                    <span class="error-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">
                                    User Name
                                </label>
                                <input type="text" class="form-control" name="user_name"
                                       onkeypress="return AvoidSpace(event)"
                                       value="{{ isset($dj)?$dj->user_name:old('user_name') }}"
                                       minlength="5"
                                       placeholder="Enter DJ's User Name"/>
                                @error('user_name')
                                <span class="error-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label">
                                    Email
                                </label>
                                <input type="email" class="form-control" name="email"
                                       value="{{ isset($dj)?$dj->email:old('email') }}"
                                       {{ isset($dj)?'readonly':'' }}
                                placeholder="ex : example@abc.com"/>
                                @error('email')
                                <span class="error-block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">
                                    Password
                                </label>
                                <input value="{{isset($dj)?$dj->password:old('password') }}" type="password"
                                       class="form-control" name="password" minlength="6"
                                       placeholder="Enter Password ">

                                @error('password')
                                <span class="error-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    </div>

                    <div class="form-group">
                        <label class="control-label">
                            Assign Club
                        </label>
                        <select class="form-control sel-status" name="club[]" multiple>
                            <option value="">Select Club</option>
                            <?php $count = 1;?>
                            @foreach($clubs as $club)
                            @if(isset($dj))
                            <?php $club_ids = \catchapp\Models\Pivot_Dj_Club::query()->where('dj_id','=', $dj->id)->pluck('club_id');
                            $sel_clubs=[];
                            foreach ($club_ids as $id){
                                array_push($sel_clubs, $id);
                            }
                            ?>
                            <option value="{{$club->id}}" {{isset($dj)?(in_array($club->id, $sel_clubs)?'selected':''):''}}>{{$count++}}
                                . {{$club->name}}</option>
                            @else
                            <option value="{{$club->id}}" {{old('club')?(in_array($club->id, old('club'))?'selected':''):''}}>{{$count++}}
                            . {{$club->name}}</option>
                            @endif
                            @endforeach
                        </select>
                        @error('club')
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
        <div class="col-xs-3">

        </div>
    </div>

</div>
<script type="text/javascript">
    function AvoidSpace(event) {
        var k = event ? event.which : window.event.keyCode;
        if (k == 32) return false;
    }

    $(document).ready(function () {

        $(".sel-status").select2();

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
@endsection
