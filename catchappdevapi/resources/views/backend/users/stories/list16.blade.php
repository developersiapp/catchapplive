@extends('backend.layouts.default')
@section('title','User Stories')
@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">User Stories</h3>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped data-table" style="width: 100%;">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th> User Name</th>
                            <th> STATS</th>
                            <th> Added Last Story On</th>
                            <th>Block Current Stories</th>
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
        /*  $(document).on('click', '.viewStories', function () {
              var user_id = $(this).attr("data-userId");
              console.log(user_id);
              $.ajax({
                  type: 'GET',
                  headers:{
                      {{--"_token": "{{ csrf_token() }}",--}}
                },
{{--                url: '{{ \Illuminate\Support\Facades\URL::route('stories.userStories') }}',--}}
                data: {
                    user_id: user_id,
                },
                dataType: 'json',
                success: function (data) {
                    console.log('here');
                },
                error: function (data) {
                        console.log('Error:', data.responseText);
                    }
                });
        });*/
        $(document).on('change', '.toggle_status', function (e) {
            e.preventDefault();
            var is_active = 0;
            var user_id = $(this).attr('value');
            if ($(this).is(':checked')) {
                is_active = 1;
            }
            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ \Illuminate\Support\Facades\URL::route('user.userStatus') }}',
                data: {
                    is_active: is_active,
                    user_id: user_id
                },
                dataType: 'json',
                success: function (data) {
                    location.reload();
                },
                error: function (data) {
                    console.log('Error:', data.responseText);
                }
            });
        });

        $(function () {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ \Illuminate\Support\Facades\URL::route('user-stories.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'user_name', name: 'user_name'},
                    {data: 'total_stories', name: 'total_stories'},
                    {data: 'added_on', name: 'added_on'},
                    {data: 'block/unblock', name: 'block/unblock', orderable: false, searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
        });

    </script>
@endsection
