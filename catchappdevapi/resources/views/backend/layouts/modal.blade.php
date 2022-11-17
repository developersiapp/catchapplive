<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 11/6/19
 * Time: 2:49 PM
 */

if (isset($size)) {
    $modal_size_cls = $size;
}
if (!isset($modal_size_cls)) {
    $modal_size_cls = "md";
}
?>

<div id="{{isset($modal_id)?$modal_id:''}}" class="modal modal fade in" role="dialog">
    <div class="modal-dialog modal-{{$modal_size_cls}} ">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body"> <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">@yield('title')</h4>
                </div>
                @yield('content')
                <div class="modal-footer">
                    @yield('footer')
                </div>
            </div>
        </div>
    </div>
</div>
