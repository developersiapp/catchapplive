<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 1/7/19
 * Time: 11:37 AM
 */ ?>
@extends('backend.layouts.default')
@section('title','Static Pages')
@section('content')
    <div class="row">

        <div class="col-xs-12 privacy-policy">
            <div class="privacy-policy-content box">
                <!-- Chat box -->
                <div class="box">
                    <div class="box-header">

                        <h3 class="box-title">
                            {{isset($page)?($page->title!=''?$page->title:'Terms & Conditions'):'Terms & Conditions'}}
                        </h3>

                        <div class="box-tools pull-right" data-toggle="tooltip">
                            <div class="btn-group" data-toggle="btn-toggle">
                                <button id="edit_tnc"
                                        class="btn btn-xs btn-success edit_tnc">
                                    <i class="fa fa-file-text "></i>
                                    <span>
                                        {{isset($page)?($page->title!=''?$page->title:'Terms & Conditions'):'Terms & Conditions'}}
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="box-body"
                         style="margin:4px;padding:4px; overflow-x: hidden;overflow-x: auto; text-align:justify; ">
                        <div class="tnc" id="tnc"
                             style="margin:4px;height: 475px; padding:4px; overflow-x: hidden;overflow-x: auto; text-align:justify; ">
                            {!! isset($page)?($page->page_content!='')?$page->page_content:'<span class="text-muted"><i> Add terms & conditions first!</i></span>' :'<span class="text-muted"><i> Add terms & conditions first!</i></span>' !!}

{{--                            {!! isset($page)?($page->page_content!='')?$page->page_content:'<span class="text-muted"><i> Add content first!</i></span>' :'<span class="text-muted"><i> Add content first!</i></span>' !!}--}}
                        </div>
                        <div class="editor" id="editor" style="display: none;">

                            <form action="{{url('' . env('APP_URL') . '/dashboard/pages/save-tnc')}}"
                                  method="post">
                                {{ csrf_field() }}
                                <input type="hidden" name="id" value="{{isset($page)?$page->id:''}}">
                                <div class="box-body">
                                    <div class="form-group ">
                                        <label class="control-label">
                                            Title :
                                        </label>
                                        <input type="text" class="form-control" name="title"
                                               value="{{ isset($page)?$page->title:old('title') }}"
                                               placeholder="Enter Page's Title"/>
                                        @error('title')
                                        <span class="error-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                <div class="form-group ">
                                        <label class="control-label">
                                            Terms & Conditions :
                                        </label>
                                        <textarea maxlength="8" id="tnc-editor" class="form-control summernote"
                                                  name="page_content">{!! isset($page)?$page->page_content:'' !!} </textarea>
                                        @error('page_content')
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
                <!-- /.item -->
            </div>


        </div>
    </div>
    <script>
        $(document).ready(function () {
            $('.summernote').summernote();
            $("#edit_tnc").click(function () {
                $("#editor").slideToggle("slow");
                $('.tnc').slideToggle("slow");
            });
        });

    </script>
@endsection
