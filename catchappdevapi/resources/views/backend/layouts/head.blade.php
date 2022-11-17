<meta charset="utf-8">
<meta name="csrf-token" content="{{ csrf_token() }}">

<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>CatchApp</title>
<!-- Tell the browser to be responsive to screen width -->
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

<link rel="stylesheet" href="{{asset('bower_components/bootstrap/dist/css/bootstrap.min.css')}}">
<!-- Font Awesome -->
<link rel="stylesheet" href="{{asset('bower_components/font-awesome/css/font-awesome.min.css')}}">
<!-- Ionicons -->
<link rel="stylesheet" href="{{asset('bower_components/Ionicons/css/ionicons.min.css')}}">
<!-- Theme style -->
<script src="{{asset('dist/js/jquery.js')}}"></script>
<script src="{{asset('dist/js/bootstrap.min.js')}}"></script>
{{--<script src="{{asset('dist/js/jquery.dataTables.min.js')}}"></script>--}}
{{--<link href="{{asset('dist/css/jquery.dataTables.min.css')}}"></link>--}}

<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{asset('dist/css/AdminLTE.min.css')}}">
<link rel="stylesheet" href="{{asset('dist/css/skins/_all-skins.min.css')}}">


<!-- include summernote css/js-->
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote.js"></script>
<!-- include summernote css/js-->
<link href="{{asset('dist/css/summernote.css')}}" rel="stylesheet">
<script src="{{asset('dist/js/summernote.js')}}"></script>
<link rel="stylesheet" href="{{asset('dist/css/select2.min.css')}}"/>
<script src="{{asset('dist/js/select2.min.js')}}"></script>

<!-- Morris -->
<link rel="stylesheet" href="{{asset('dist/css/morris.css')}}">
<script src="{{asset('dist/js/raphael-min.js')}}"></script>
<script src="{{asset('dist/js/morris.min.js')}}"></script>

<!-- Date Picker -->
<link rel="stylesheet" href="{{asset('dist/css/jquery-ui.css')}}">
<script src="{{asset('dist/js/jquery-ui.js')}}"></script>

<link rel="stylesheet" href="{{asset('dist/css/charts/export.css')}}">
<link rel="stylesheet" href="{{asset('dist/css/charts/c3.min.css')}}">
<link rel="stylesheet" href="{{asset('dist/css/charts/plottable.css')}}">
<link rel="stylesheet" href="{{asset('dist/css/charts/morris.css')}}">
<link rel="stylesheet" href="{{asset('dist/css/charts/jquery-ui.css')}}">







<style>
    .note-frame .in , .modal-backdrop {
        display: none !important;
    }
    .modal-backdrop, .modal-backdrop.in{
        z-index: -1;
    }
</style>
<script>


    $(document).ready(function () {
        $(function() {
            $( ".datepicker" ).datepicker({
                changeMonth: true,
                changeYear: true,
                maxDate: new Date(),
                dateFormat: 'dd M, yy',
//            yearRange: '2000:2019', // specifying a hard coded year range
                yearRange: "-100:+0",
            });
        });
    });
</script>

