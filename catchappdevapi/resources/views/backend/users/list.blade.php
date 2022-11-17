@extends('backend.layouts.default')
@section('title','Users')
@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Users List</h3>
                    <a href="{{url('' . env('APP_URL') . '/dashboard/users/add-new')}}"
                       class="btn btn-xs btn-success">
                        <i class="fa fa-plus"></i><span> Add New User</span>
                    </a>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped data-table" style="width: 100%;">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>User Name</th>
                            <th>Gender</th>
                            <th>Birth Date</th>
                            <th>Registeration Type</th>
                            <th>Registered On</th>
                            <th>Action</th>
                        </tr></thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(function () {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('users.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'logo', name: 'logo'},
                    {data: 'first_name', name: 'first_name'},
                    {data: 'last_name', name: 'last_name'},
                    {data: 'email', name: 'email'},
                    {data: 'user_name', name: 'user_name'},
                    {data :'gender',name:'gender'},
                    {data :'birthdate',name:'birthdate'},
                    {data :'reg_type',name:'reg_type'},
                    {data: 'added_on', name: 'added_on'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
        });
    </script>
@endsection
