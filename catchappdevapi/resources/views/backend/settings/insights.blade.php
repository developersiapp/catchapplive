<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 7/6/19
 * Time: 4:13 PM
 */
$insight = \catchapp\Models\Insight::query()->first();
?>
@extends('backend.layouts.default')
@section('title','Settings')
@section('content')
    <div class="row">
        <div class="col-lg-6 col-md-9">
            <div class="row">
                @if(isset($insight))
                <div class="col-lg-4 col-xs-6">
                  <!-- small box -->
                  <div class="small-box bg-success">
                    <div class="inner">
                      <h3>{{$insight->slow_count}}</h3>
                      <div>Slow</div>
                    </div>
                    <div class="icon">
                      <i class="ion ion-stats-bars"></i>
                    </div>
                  </div>
                </div>
                <div class="col-lg-4 col-xs-6">
                  <!-- small box -->
                  <div class="small-box bg-warning">
                    <div class="inner">
                      <h3>{{$insight->normal_count}}</h3>
                      <div>Normal</div>
                    </div>
                    <div class="icon">
                      <i class="ion ion-stats-bars"></i>
                    </div>
                  </div>
                </div>
                <div class="col-lg-4 col-xs-6">
                  <!-- small box -->
                  <div class="small-box bg-danger">
                    <div class="inner">
                      <h3>{{$insight->hype_count}}</h3>
                      <div>Hype</div>
                    </div>
                    <div class="icon">
                      <i class="ion ion-stats-bars"></i>
                    </div>
                  </div>
                </div>
                @endif
            </div>
            </div>
        </div>
        <div class="row">
        <div class="col-lg-6 col-md-9">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Counts</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form action="{{url('' . env('APP_URL') . '/dashboard/insight/save-insights')}}" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{isset($insight)?$insight->id:''}}">
                    <div class="box-body">
                        <div class="form-group">
                            <label class="control-label">
                                Slow Count
                            </label>
                            <input type="number" class="form-control" name="slow_count"
                                   value="{{ old('slow_count')?old('slow_count'):$insight->slow_count}}"
                                   placeholder="Slow Count">
                            @error('slow_count')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="control-label">
                                Normal Count
                            </label>
                            <input type="number" class="form-control" name="normal_count"
                                   value="{{old('normal_count')?old('normal_count'):$insight->normal_count }}"
                                   placeholder="Normal Count > Slow Count">
                            @error('normal_count')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="control-label">
                                Hype Count
                            </label>
                            <input type="number" class="form-control" name="hype_count"
                                   value="{{ old('hype_count')?old('hype_count'):$insight->hype_count }}"
                                   placeholder="Hype Count > Normal Count">

                            @error('hype_count')
                            <span class="text-danger">{{ $message }}</span>
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

@endsection
