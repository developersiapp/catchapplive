@extends('backend.layouts.default')
@section('title','Clubs')

@section('content')
    <?php $count=1; ?>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <p class="pull-right">
                    <a href="{{url('dashboard/clubs/add-new')}}"
                       class="btn btn-xs btn-success">
                        <i class="fa fa-plus"></i><span> Add New Club</span>
                    </a>
                </p>

            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="table-responsive no-padding">
                    <table class="table table-hover">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Country</th>
                            <th>State</th>
                            <th>City</th>
                            <th>ZIP</th>
                            <th>Registered On</th>
                            <th>Action</th>
                        </tr>
                        @if($clubs->count()>0)
                            @foreach($clubs as $club)
                                <tr>
                                    <td>{{$count++}}</td>
                                    <td>
                                        <img class="img-circle img-md img-bordered"
                                             src={!! url(isset($club)?($club->profile_image!=''?'/uploads/clubs/'.$club->profile_image:'/dist/img/club.png'):'/dist/img/club.png') !!}  class="img-circle"
                                             alt="Club Image">
                                        {{$club->name}}</td>
                                    <td>{{$club->street_address}}</td>
                                    <td>{{\catchapp\Models\Club::$countries{$club->country} }}</td>
                                    <td>{{\catchapp\Models\Club::$states{$club->state}['Title'] }}</td>
                                    <td>{{\catchapp\Models\Club::$cities{$club->city}['Title'] }}</td>
                                    <td>{{$club->zip}}</td>
                                    <td>{{ date('F d, Y', strtotime($club->created_at)) }}</td>
                                    <td>
                                        <a href="{{url('dashboard/clubs/edit-club/'. $club->id)}}"
                                           class="btn btn-xs btn-primary">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                    <a href="{{url('dashboard/clubs/delete-club/'. $club->id)}}"
                                       onclick="return confirm('Do you really want to delete it?')"
                                       class="btn btn-xs btn-danger">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="9" class="alert_info">
                                    <h4> NO RECORD FOUND</h4>
                                </td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection




