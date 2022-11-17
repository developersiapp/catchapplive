<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 10/6/19
 * Time: 1:07 PM
 */ $count=1;?>
@extends('backend.layouts.default')
@section('title','Settings')
@section('content')
{{csrf_field()}}
{{method_field('POST')}}
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">E-mails</h3>
                    <a href="{{url('' . env('APP_URL') . '/dashboard/settings/new-email')}}"
                       class="btn btn-xs btn-primary pull-left">
                        <i class="fa fa-plus"></i><span> Create New Email</span>
                    </a>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped data-table" style="width: 100%;">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>To</th>
                            <th>Subject</th>
                            <th>Mail Content</th>
                            <th>Email Type</th>
                            <th>Created On</th>
                            <th>Action</th>
                        </tr>
                        </thead>
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
                ajax: "{{ route('emails.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'mail_to', name: 'mail_to'},
                    {data: 'mail_subject', name: 'mail_subject'},
                    {data: 'content', name: 'content'},
                    {data: 'type', name: 'type'},
                    {data: 'added_on', name: 'added_on'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
        });


        $(document).on('click', '.send-email', function () {
            $('.form-horizontal').show();
            $.ajax({
                type: 'POST',
                headers:{
                    "_token": "{{ csrf_token() }}",
                },
                url: '{{ \Illuminate\Support\Facades\URL::route('email.send') }}',
                data: {
                    id: $(this).data('id'),
                    _token: "{{ csrf_token() }}",
                },

                dataType: 'json',
                success: function (data) {
                    try {
                        if(data.success) {
                            alert(data.success);
                         }else{
                            alert(data.error);
                        }
                        var url = '{{route('emails.index')}}';
                        window.location= url;
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