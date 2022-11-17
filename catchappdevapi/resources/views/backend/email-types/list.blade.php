<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 6/6/19
 * Time: 4:18 PM
 */
$count=1;?>
@extends('backend.layouts.default')
@section('title','Email Types')
@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Email Type List</h3>
                    <a href="#" class="show-modal btn btn-xs btn-success add-email-type">
                        <i class="fa fa-plus"></i><span> Add New Email Type</span>
                    </a>

                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped data-table" style="width: 100%;">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email Address</th>
                            <th>Template</th>
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
        $(document).ready(function () {

            $('#uni_modal .modal').on('shown.bs.modal', function() {
                alert('hey');
                //            $('#summernote').summernote();
            });  });


        $(function () {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('email-types.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'email_address', name: 'email_address'},
                    {data: 'template', name: 'template'},
                    {data: 'added_on', name: 'added_on'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
        });
        $(document).on('click', '.edit-email-type', function () {
            $('.form-horizontal').show();
            $.ajax({
                type: 'POST',
                headers:{
                    "_token": "{{ csrf_token() }}",
                },
                url: '{{ \Illuminate\Support\Facades\URL::route('email-type.edit') }}',
                data: {
                    id: $(this).data('id'),
                    name: $(this).data('name'),
                    aid: $(this).data('aid'),
                    _token: "{{ csrf_token() }}",
                },

                dataType: 'json',
                success: function (data) {
                    try {
                        $('#uni_modal').html('');
                        $('#uni_modal').append(data.view);
                        $('#uni_modal .modal .modal-title').text('Edit Email Type');

                        $('#uni_modal .email-type-form #id').val(data.id);
                        $('#uni_modal .email-type-form #aid').val(data.aid);
                        $('#uni_modal .email-type-form #name').val(data.name);
                        $('#uni_modal .email-type-form #email_address').val(data.email_address);
                        $('#uni_modal .email-type-form .summernote').summernote('code', data.template);
                        $('.summernote').summernote();

                        $('#uni_modal .modal').modal('show');
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
        $(document).on('click', '.add-email-type', function () {
            $('.form-horizontal').show();
            $.ajax({
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ \Illuminate\Support\Facades\URL::route('email-type.create') }}',
                dataType: 'json',
                success: function (data) {
                   // try {
                        $('#uni_modal').html('');
                        $('#uni_modal').append(data.view);
                        $('#uni_modal .modal .modal-title').text('Add New Email Type');
                        $('#uni_modal .modal').modal('show');
                        $('.summernote').summernote();
//                    }
//                    catch (e) {
//                        alert('Exception while request..');
//                    }
                },
                error: function (data) {
                    console.log('Error:', data.responseText);
                }
            });
        });

        $(document).on('click', '.form-submit', function(e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ \Illuminate\Support\Facades\URL::route('email-type.save') }}',
                data: $('form.email-type-form').serialize(),

                success: function (data) {
                    try {
                     if(data.errors){
                         $.each(data.errors, function (key, value) {
                             $('.error-block').show();
                             $('.error-block').html('');
                             $('.error-block').append('<p>' + value + '</p>');
                         });
                         $('#uni_modal .modal').modal('show');
                     }
                     else
                     {location.reload();}
                    }
                    catch (e) {
                        alert('Exception while request..');
                    }
                },
                error: function (data) {
                    console.log('Error:', data.responseText);
                }
            });
            return false;
        });
    </script>


@endsection
