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
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Gender</th>
                            <th>Birth Date</th>
                            <th>Registeration Type</th>
                            <th>Registered On</th>
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
                ajax: "{{ route('users.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'first_name', name: 'first_name'},
                    {data: 'last_name', name: 'last_name'},
                    {data: 'email', name: 'email'},
                    {data: 'gender', name: 'gender'},
                    {data: 'birthdate', name: 'birthdate'},
                    {data: 'reg_type', name: 'reg_type'},
                    {data: 'added_on', name: 'added_on'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
        });
    </script>
    {{--
        <div class="modal fade" id="editUser"
             tabindex="-1" role="dialog"
             aria-labelledby="editUserLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close"
                                data-dismiss="modal"
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"
                            id="editUserLabel"></h4>
                    </div>
                    <div class="modal-body" id="user_detail">

                    </div>

            </div>
            </div>
        </div>--}}

    <script type="text/javascript">
        $(document).on('click', '.edit-user', function () {
            $('.form-horizontal').show();
            $('#id').val($(this).data('id'));
            var user_id = $(this).data('id');
            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ \Illuminate\Support\Facades\URL::route('user.editUser') }}',
                data: {
                    id: user_id
                },
                dataType: 'json',
                success: function (data) {
                    try {
                        var modals = $('.modal');
                        $.each('.modal', function (index, item) {
                            item.modal('hide')
                        });

                        $('#uni_modal').after(data.view);
                        $('#uni_modal').find('.modal').id = data.modal_id;
                        $('.modal-title').text('Edit User');
                        $('.modal').modal('show');
                    }
                    catch (e) {
                        alert('Exception while request..');
                    }
                },
                error: function (data) {
                    console.log('Error:', data.responseText);
                }
            });
        })
    </script>
@endsection
