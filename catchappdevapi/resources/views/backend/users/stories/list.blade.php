@extends('backend.layouts.default')
@section('title','User Stories')
@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">User Stories</h3>
                                   </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table class="table table-bordered table-striped data-table" style="width: 100%;">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th> User</th>
                            <th> Story Type</th>
                            <th> Story Status</th>
                            <th>Added On</th>
                            <th>Block Story</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

        <div class="modal fade" id="view_story" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">User Story</h4>
                    </div>
                    <div class="modal-body" id="modal-body">
                        @include('backend.users.stories.postedStory')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script type="text/javascript">

        //$('table .toggle_status').on('change',function (e) {
        $(document).on('change', '.toggle_status', function(e) {
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
                url: '{{ \Illuminate\Support\Facades\URL::route('user.userStory') }}',
                data: {
                    is_active: is_active,
                    id: id
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

        $('#view_story').on('show.bs.modal', function (event) {
            var button =$(event.relatedTarget);
            var story_id = button.data('storyid');
            var modal = $(this);
           var ele_id= modal.find('.modal-body #id');
            ele_id.val(story_id);
            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ \Illuminate\Support\Facades\URL::route('user.viewStory') }}',
                data: {
                    story_id: story_id
                },
                dataType: 'json',
                success: function (data) {
                    try{
                        var div_story= modal.find('.modal-body #posted_story');
                        div_story.html(data.response_html);
                    }
                    catch(e) {
                        alert('Exception while request..');
                    }
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
                    {data: 'story_type', name: 'story_type'},
                    {data: 'story_status', name: 'story_status'},
                    {data: 'added_on', name: 'added_on'},
                    {data: 'block/unblock', name: 'block/unblock', orderable: false, searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
        });

    </script>
@endsection
