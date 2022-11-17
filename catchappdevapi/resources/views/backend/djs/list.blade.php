<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 6/6/19
 * Time: 4:18 PM
 */
$count=1;?>
@extends('backend.layouts.default')
@section('title','Djs')
@section('content')

    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">DJs List</h3>
                    <a href="{{url('' . env('APP_URL') . '/dashboard/djs/add-new')}}"
                       class="btn btn-xs btn-success">
                        <i class="fa fa-plus"></i><span> Add New DJ</span>
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
                            <th>User Name</th>
                            <th>Gender</th>
                            <th>Birth Date</th>
                            <th>Registration Type</th>
                            <th>Assigned Club</th>
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
                ajax: "{{ route('djs.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'logo', name: 'logo'},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'user_name', name: 'user_name'},
                    {data :'gender',name:'gender'},
                    {data :'birth_date',name:'birth_date'},
                    {data :'reg_type',name:'reg_type'},
                    {data :'club_names',name:'club_names'},
                    {data: 'added_on', name: 'added_on'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
        });
    </script>
@endsection
