<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 21/6/19
 * Time: 6:02 PM
 */
if(\Illuminate\Support\Facades\Session::has('error')){
$classModal='modal-primary';
    }
if(\Illuminate\Support\Facades\Session::has('success')){
 $classModal="modal-danger";
}
?>
@if( \Illuminate\Support\Facades\Session::has('error') ||  \Illuminate\Support\Facades\Session::has('success'))
    <script type="text/javascript">
        $(document).ready(function () {
            $('#popupmodal').modal();
        });
    </script>
    <div class="modal fade " id="popupmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" id="closeModal" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Notification :</h4>
                </div>
                <div class="modal-body">

                        @if(\Illuminate\Support\Facades\Session::has('error'))
                            {{ \Illuminate\Support\Facades\Session::get('error') }}
                        @endif
                        @if(\Illuminate\Support\Facades\Session::has('success'))
                            {{ \Illuminate\Support\Facades\Session::get('success') }}
                        @endif
                                    </div>
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                </div>
            </div>
        </div>
        </div>
@endif
