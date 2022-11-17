@extends('backend.layouts.default')
@section('title')
    Dashboard
    <small>Analytics</small>

@endsection
@section('content')
    <div class="row">


        <div class="col-xs-12 col-md-12">
            <div class="row">
                <div class="col-xs-12 col-md-6 pull-right">

                    <h3 class="control-label" id="label_filter"></h3>
                    <div class="input-group margin">
                        <input type="text" readonly value="{{ isset($date)?(\Carbon\Carbon::parse($date)->format('d M, Y')):\Carbon\Carbon::now()->format('d M, Y') }}"
                               class="form-control datepicker" id="filter_date" name="filter_date">
                        <span class="input-group-btn">
                      <button type="button" id="filter" class="btn btn-success btn-flat">Show!</button>
                    </span>
                    </div>
                </div>
            </div>
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <!-- ./col -->
                <div class="col-lg-4 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="user_count">{{\catchapp\Models\User::query()->count()}}</h3>

                            <p>User Registrations</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="{{url('' . env('APP_URL') . '/dashboard/users')}}" class="small-box-footer">More info
                            <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3 id="club_count">{{\catchapp\Models\Club::query()->count()}}</h3>

                            <p>Club Registrations</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-building"></i>
                        </div>
                        <a href="{{url('' . env('APP_URL') . '/dashboard/clubs')}}" class="small-box-footer">More info
                            <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-4 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3 id="dj_count">{{\catchapp\Models\DJ::query()->count()}}</h3>

                            <p>Dj Registrations</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-music-note"></i>
                        </div>
                        <a href="{{url('' . env('APP_URL') . '/dashboard/djs')}}" class="small-box-footer">More info <i
                                    class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

            </div>
            <!-- /.row -->
            <div class="col-md-4 col-xs-12">
                <!-- Line chart -->
                <div class="box box-warning col-xs-6 col-md-6">
                    <div class="box-header with-border">
                        <i class="fa fa-user-plus"></i>
                        <h3 class="box-title">Users</h3>
                    </div>
                    <div class="box-body">
                        <div id="user_stats" style="height: 300px;"></div>
                    </div>
                    <!-- /.box-body-->
                </div>
                <!-- /.box -->
            </div>
            <div class="col-md-4 col-xs-12">
                <!-- Line chart -->
                <div class="box box-info col-xs-6 col-md-6">
                    <div class="box-header with-border">
                        <i class="fa fa-building"></i>
                        <h3 class="box-title">Clubs</h3>
                    </div>
                    <div class="box-body">
                        <div id="club_stats" style="height: 300px;"></div>
                    </div>
                    <!-- /.box-body-->
                </div>
                <!-- /.box -->
            </div>
            <div class="col-md-4 col-xs-12">
                <!-- Line chart -->
                <div class="box box-success col-xs-6 col-md-6">
                    <div class="box-header with-border">
                        <i class="fa fa-music"></i>
                        <h3 class="box-title">Djs</h3>
                    </div>
                    <div class="box-body">
                        <div id="dj_stats" style="height: 300px;"></div>
                    </div>
                    <!-- /.box-body-->
                </div>
                <!-- /.box -->
            </div>
        </div>
        <!-- /.row -->
        <div class="col-xs-12 col-md-12">
            <div class="box box-primary col-xs-6 col-md-6">
                <div class="box-header with-border">
                    <i class="fa fa-user-circle"></i>
                    <h3 class="box-title">Online Users</h3>
                </div>
                <div class="box-body">
                    <div id="online_users" style="height: 300px;"></div>
                </div>
                <!-- /.box-body-->
            </div>
        </div>
    </div>


    <script type="text/javascript">
        $(document).on('click', '#filter', function () {
            $.ajax({
                type: 'POST',
                headers:{
                    "_token": "{{ csrf_token() }}",
                },
                url: '{{ \Illuminate\Support\Facades\URL::route('dashboard.filterData') }}',
                data: {
                    date: $('#filter_date').val(),
                    _token: "{{ csrf_token() }}",
                },
                dataType: 'json',
                success: function (data) {
                    try {
                        console.log(data.data);
                        $('#user_count').html(data.data[0]);
                        $('#club_count').html(data.data[1]);
                        $('#dj_count').html(data.data[2]);
//                        $('#label_filter').html("Total counts for date :"+data.data[2]);
                        console.log(data.data[0]);
                    }
                    catch (e) {
                        alert('Exception while request..');
                    }
                },
                error: function (data) {
                    console.log('Error:', data.responseText);
                }
            });


        });


        $(document).ready(function () {
            $.ajax({
                url: '{{ \Illuminate\Support\Facades\URL::route('dashboard.getData') }}',
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('val')
                },
                dataType: 'json',
                success: function (data) {
                    try {
                        /* MORRIS DONUT CHART
                         ----------------------------------------*/
                        Morris.Donut({
                            colors:["#00add7","#5DBED7","#9CCCD8"],
                            element: 'club_stats',
                            data: data.data[0],
                            resize: true
                        }).select(0);

                        Morris.Donut({
                            colors:["#009551","#9ACE87","#C2DAB9"],
                            element: 'dj_stats',
                            data: data.data[2],
                            resize: true,
                        }).select(0);
                        Morris.Donut({
                            colors:["#e29900","#DCBA6B","#D7C7A1"],
                            element: 'user_stats',
                            data: data.data[1],
                            resize: true
                        }).select(0);

                        Morris.Donut({
//                            colors: ["#fd4c69","#f58776"],
                            colors: ["#fd4c69","#A4A6A2"],

                            element: 'online_users',
                            data: data.data[3],
                            resize: true,
                        }).select(0);
                    }
                    catch (e) {
                        alert('Exception while request..');
                    }
                },
                error: function (data) {
                    console.log('Error:', data.responseText);
                }
            });
        });
    </script>
@endsection



