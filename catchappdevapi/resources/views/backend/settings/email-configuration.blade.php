<?php
if (isset($email)){
    $email_address= \catchapp\Models\EmailAddress::query()->where('email_type','=', $email->email_type)->first();
}
$email_type_ids = \catchapp\Models\EmailAddress::query()->where('email_address','!=','')->select('email_type')->get();
$email_types = \catchapp\Models\EmailType::query()->whereIn('id',$email_type_ids)->get();
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 10/6/19
 * Time: 10:18 AM
 */ ?>
@extends('backend.layouts.default')
@section('title','Settings')
@section('content')


    <div class="row">
        <div class="col-lg-6 col-md-9">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Create New E-mail</h3>
            </div>
            <form action="{{url('' . env('APP_URL') . '/dashboard/settings/save-email')}}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{isset($email)?$email->id:''}}">
                <div class="box-body">
                    <div class="form-group ">
                        <label class="control-label">
                            Email-type
                        </label>
                        <select class="form-control email_type" name="email_type" id="email_type">
                            <option value="">Select</option>
                            <?php $type_count=1; ?>
                            @foreach($email_types as $email_type)
                                <option value="{{$email_type->id}}"
                                        {{isset($email)?(($email->email_type==$email_type->id)?'selected':''):''}}> {{$type_count++}}. {{$email_type->name}}</option>
                            @endforeach
                        </select>
                        @error('email_type')
                        <span class="error-block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="control-label">
                            To
                        </label>
                        <div class="input-group">
                       <span class="input-group-addon">
                           <i class="fa fa-envelope">
                           </i>
                       </span>
                        <input type="email" value="{{isset($email)?$email->mail_to:old('mail_to') }}" class="form-control" name="mail_to" placeholder="Recipient">
                            @error('mail_to')
                            <span class="error-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                <div class="form-group">
                        <label class="control-label">
                            Name :
                        </label>
                        <input type="text" value=" " id="name" class="form-control" name="name" placeholder="Enter name here">
                        @error('name')
                        <span class="error-block">{{ $message }}</span>
                        @enderror
                        <span class="text-muted"> Enter a name here which will be replaced by "<i>{name}</i>" in template.</span>
                    </div>

                    {{--<div class="form-group">--}}
                        {{--<label class="control-label">--}}
                            {{--From--}}
                        {{--</label>--}}
                        {{--<div class="input-group">--}}
                       {{--<span class="input-group-addon">--}}
                           {{--<i class="fa fa-envelope">--}}
                           {{--</i>--}}
                       {{--</span>--}}
                        {{--<input type="email" readonly value="{{isset($email)?$email->mail_from:old('mail_from') }}"  class="form-control" name="mail_from" id="mail_from" placeholder="Sender">--}}
                            {{--@error('mail_from')--}}
                            {{--<span class="error-block">{{ $message }}</span>--}}
                            {{--@enderror--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    <div class="form-group">
                        <label class="control-label">
                            Subject
                        </label>
                        <input type="text" value="{{isset($email)?$email->mail_subject:old('mail_subject')}}"  class="form-control" name="mail_subject" placeholder="Subject">
                        @error('mail_subject')
                        <span class="error-block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group ">
                        <label class="control-label">
                            Email Message
                        </label>
                        <textarea maxlength="8" id="template" class="form-control summernote" name="email_message">
                            {!! isset($email)?$email->mail_content:'' !!}
                        </textarea>
                        @error('email_message')
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

    <script type="text/javascript">
        $(document).ready(function () {

            $(document).on('keypress', '#name', function(e) {

            });


            var mail_from = $('#mail_from');
            $('.email_type').change(function (e) {
                e.preventDefault();
                var email_type_id = $(this).val();
                if (email_type_id) {
                    mail_from.html('');
                    $.ajax({
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: '{{ \Illuminate\Support\Facades\URL::route('email-configuration.get-template') }}',
                        data: {
                            email_type_id: email_type_id
                        },
                        dataType: 'json',
                        success: function (data) {
                            mail_from.html('');
                            $('.summernote').summernote('code','');
                            $('.summernote').summernote('code',data.email_config.template);
                           mail_from.val(data.email_config.email_address);
                        },
                        error: function (data) {
                            console.log('Error:', data.responseText);
                        }
                    });
                }
            });
        });


    </script>

@endsection
