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
        <div class="col-md-6 col-md-12">
            <form action="{{url('' . env('APP_URL') . '/dashboard/djs/update-club')}}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{isset($dj)?$dj->id:''}}">
                <div class="box-body">
                    <div class="col-xs-12 form-group">
                        <label class="control-label">
                            Assign Club :
                        </label>
                        <select class="form-control" name="club">
                            <option value="">Select Club</option>
                            <?php $count=1;?>
                            @foreach($clubs as $club)
                                <option value="{{$club->id}}" {{isset($dj)?($dj->assigned_clubs==$club->id?'selected':''):''}}>{{$count++}}. {{$club->name}}</option>
                            @endforeach
                        </select>
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
