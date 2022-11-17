<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 6/6/19
 * Time: 4:18 PM
 */
$count=1;?>
@extends('backend.layouts.default')
@section('title','Email Addresses')
@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Email Addresses List</h3>
                    {{--<a href="#" class="show-modal btn btn-xs btn-success add-email-type">--}}
                        {{--<i class="fa fa-plus"></i><span> Add New Email Address</span>--}}
                    {{--</a>--}}


                    <a href="{{url('' . env('APP_URL') . '/dashboard/email-address/add-new')}}"
                    class="btn btn-xs btn-success">
                    <i class="fa fa-plus"></i><span> Add New Email Address</span>
                    </a>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped data-table" style="width: 100%;">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email Type</th>
                            <th>Email Address</th>
                            <th>Email Template</th>
                            <th>Added On</th>
                            <th>Action</th>
                        </tr></thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="uni_modal" id="uni_modal">

    </div>

    <script type="text/javascript">

        $(function () {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('email-addresses.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'email_type_name', name: 'email_type_name'},
                    {data: 'email_address', name: 'email_address'},
                    {data: 'template', name: 'template'},
                    {data: 'added_on', name: 'added_on'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
        });
    </script>


@endsection
