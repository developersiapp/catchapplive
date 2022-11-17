@extends('backend.layouts.default')
@section('title','Feedbacks')
@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">

                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped data-table" style="width: 100%;">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>User Name</th>
                            <th>Email Address</th>
                            <th>Message</th>
                            <th>Sent On</th>
{{--                            <th>Action</th>--}}
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
                ajax: "{{ route('feedbacks.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},

                    {data: 'user_name', name: 'user_name'},
                    {data: 'email', name: 'email'},
                    {data: 'message', name: 'message'},
                    {data: 'added_on', name: 'added_on'},
                    // {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
        });
    </script>
@endsection
