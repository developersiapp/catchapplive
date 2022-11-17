@extends('backend.layouts.default')
@section('title','Clubs')
@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Clubs List</h3>
                    <a href="{{url('' . env('APP_URL') . '/dashboard/clubs/add-new')}}"
                       class="btn btn-xs btn-success">
                        <i class="fa fa-plus"></i><span> Add New Club</span>
                    </a>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped data-table" style="width: 100%;">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Logo</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Country</th>
                            <th>State</th>
                            <th>City</th>
                            <th>ZIP</th>
                            <th>Assigned Djs</th>
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
                ajax: "{{ route('clubs.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'logo', name: 'logo'},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'street_address', name: 'street_address'},
                    {data: 'country', name: 'country'},
                    {data :'state',name:'state'},
                    {data :'city',name:'city'},
                    {data :'zip',name:'zip'},
                    {data :'dj_names',name:'dj_names'},
                    {data: 'added_on', name: 'added_on'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
        });
    </script>
@endsection
