@extends('backend.layouts.default')
@section('title','Users')
@section('content')
    <?php $count=1; ?>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <p class="pull-right">
                    <a href="{{url('dashboard/users/add-new')}}"
                       class="btn btn-xs btn-primary">
                        <i class="fa fa-user-plus"></i><span> Create New User</span>
                    </a>
                </p>
            </div>
        </div>
        <div class="row">
            <form method="get" class="pagination-form">
                <input type="hidden" name="offset" value="<?=$paginator->getOffset()?>">
                <div class="box-body">
                    <div class="col-md-3 col-xs-5">
                        <label class="control-label">Search : </label>
                        <input class="form-control" type="text" name="search_text" placeholder="Search..."
                               value="{{$search_text}}">
                    </div>
                    <div class="col-md-1 col-xs-4">
                        <label class="control-label">Per Page</label>
                        <select class="form-control" name="limit">
                            <option value="">Select</option>
                            <option {{\App\Helpers\SelectHelper::select(10, $paginator->getPerPage())}}>10</option>
                            <option {{\App\Helpers\SelectHelper::select(20, $paginator->getPerPage())}}>20</option>
                            <option {{\App\Helpers\SelectHelper::select(30, $paginator->getPerPage())}}>30</option>
                            <option {{\App\Helpers\SelectHelper::select(40, $paginator->getPerPage())}}>40</option>
                            <option {{\App\Helpers\SelectHelper::select(50, $paginator->getPerPage())}}>50</option>
                        </select>
                    </div>
                    <div class="col-xs-3 col-md-1">
                        <label class="control-label">Search</label>
                        <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
                    </div>

                </div>
            </form>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="table-responsive no-padding">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Birth Date</th>
                            <th>Gender</th>
                            <th>Registered On</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($users->count()>0)
                            @foreach($users as $user)
                                <tr class="user{{$user->id}}">
                                    <td>{{$count++}}</td>
                                    <td>{{$user->first_name?$user->first_name:'-'}}</td>
                                    <td>{{$user->last_name?$user->last_name:'-'}}</td>
                                    <td>{{$user->email?$user->email:'-'}}</td>
                                    <td>{{ date('F d, Y', strtotime($user->birth_date)) }}</td>
                                    <td>{{$user->gender}}</td>
                                    <td>{{ date('F d, Y', strtotime($user->created_at)) }}</td>
                                    <td>
                                        <a href="{{url('dashboard/users/edit-user/'. $user->id)}}"
                                           class="btn btn-xs btn-primary">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <a href="{{url('dashboard/users/delete-user/'. $user->id)}}"
                                           onclick="return confirm('Do you really want to delete it?')"
                                           class="btn btn-xs btn-danger">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="alert_info">
                                    <h4>NO RECORD FOUND</h4>
                                </td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            {!! $paginator->links() !!}
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $('.toggle_status').change(function (e) {
                e.preventDefault();
                var is_active = 0;
                var id = $(this).attr('value');
                if ($(this).is(':checked')) {
                    is_active = 1;
                }
                $.ajax({
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: 'user/user-story',
                    data: {
                        is_active: is_active,
                        id: id
                    },
                    dataType: 'json',
                    success: function (data) {
                        location.reload();
//                        console.log(data);
                    },
                    error: function (data) {
                        console.log('Error:', data.responseText);
                    }
                });
            });



            $(".js-pagination a").on("click", function () {
                let offset = $(this).data('offset');
                $("[name='offset']").val(offset);
                $(".pagination-form").submit();
            });
        });
    </script>


    {{--<div class="modal fade" id="favoritesModal"--}}
    {{--tabindex="-1" role="dialog"--}}
    {{--aria-labelledby="favoritesModalLabel">--}}
    {{--<div class="modal-dialog" role="document">--}}
    {{--<div class="modal-content">--}}
    {{--<div class="modal-header">--}}
    {{--<button type="button" class="close"--}}
    {{--data-dismiss="modal"--}}
    {{--aria-label="Close">--}}
    {{--<span aria-hidden="true">&times;</span></button>--}}
    {{--<h4 class="modal-title"--}}
    {{--id="favoritesModalLabel">Add New User</h4>--}}
    {{--</div>--}}
    {{--@if(session()->has('message'))--}}
    {{--<div class="alert alert-success">--}}
    {{--<h1>   {{ session()->get('message') }}--}}
    {{--</div>--}}
    {{--@endif--}}
    {{--<form action="{{'/user/save-user'}}" method="post">--}}
    {{--<div class="modal-body">--}}
    {{--{{ csrf_field() }}--}}
    {{--<div class="col-xs-12 form-group">--}}
    {{--<div class="col-xs-6">--}}
    {{--<label class="control-label">--}}
    {{--First Name :--}}
    {{--</label>--}}
    {{--<input type="text" class="form-control" name="first_name"--}}
    {{--placeholder="Enter First Name">--}}
    {{--</div>--}}
    {{--<div class="col-xs-6">--}}
    {{--<label class="control-label">--}}
    {{--Last Name :--}}
    {{--</label>--}}
    {{--<input type="text" class="form-control" name="last_name" placeholder="Enter Last Name">--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div class="col-xs-12 form-group">--}}
    {{--<div class="col-xs-6">--}}
    {{--<label class="control-label pull-left">--}}
    {{--Gender :--}}
    {{--</label>--}}
    {{--<input type="radio" name="gender" id="female" value="female" checked="">Female--}}
    {{--<input type="radio" name="gender" id="male" value="male" checked="">Male--}}
    {{--</div>--}}
    {{--<div class="col-xs-6">--}}
    {{--<label class="control-label">--}}
    {{--Birth Date :--}}
    {{--</label>--}}
    {{--<input type="date" class="form-control" name="birthDate">--}}

    {{--</div>--}}
    {{--</div>--}}
    {{--<div class="col-xs-12 form-group">--}}
    {{--<div class="col-xs-6">--}}
    {{--<label class="control-label">--}}
    {{--Email Address :--}}
    {{--</label>--}}
    {{--<input type="text" class="form-control" name="email"--}}
    {{--placeholder="ex : example@abc.com">--}}
    {{--</div>--}}

    {{--<div class="col-xs-6">--}}
    {{--<label class="control-label">--}}
    {{--Password :--}}
    {{--</label>--}}
    {{--<input type="password" class="form-control" name="password"--}}
    {{--placeholder="Enter Password ">--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--<div class="row">--}}
    {{--<div class="modal-footer">--}}
    {{--<span>--}}
    {{--<button type="submit" class="btn btn-primary">--}}
    {{--Save--}}
    {{--</button>--}}
    {{--</span>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</form>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    <script>
        $(function () {
            $("#example").DataTable();
        });
    </script>
@endsection
