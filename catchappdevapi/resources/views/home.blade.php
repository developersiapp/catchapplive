@extends('backend.layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Live streaming</div>

                    <div class="card-body">
                        <!-- Use this if you only support Safari!!
      <div id="player">
          <video id="video" autoplay="true" controls="controls">
              <source src="http://ip-address-of-web-server/live/mystream.m3u8" />
              Your browser does not support HTML5 streaming!
          </video>
      </div>
  -->

                        <div id='wowza_player'></div>
                        <script id='player_embed' src='//player.cloud.wowza.com/hosted/xbm9fhkb/wowza.js' type='text/javascript'></script>

                        {{--<video autoplay="true" id="videoElement">

                        </video>
                        <script>

                            var startTime = Date.now();
                            var detectPermissionDialog = function(allowed) {
                                if (Date.now() - startTime > timeThreshold) {
                                    // dialog was shown
                                }
                            };
                            var successCallback = function(error) {
                                detectPermissionDialog(true);
                            };
                            var errorCallback = function(error) {
                                if ((error.name == 'NotAllowedError') ||
                                    (error.name == 'PermissionDismissedError')) {
                                    detectPermissionDialog(false);
                                }
                            };
                            navigator.mediaDevices.getUserMedia()
                                .then(successCallback, errorCallback);


                            var video = document.getElementById("videoElement");

                            if (navigator.mediaDevices.getUserMedia) {
                                navigator.mediaDevices.getUserMedia({ video: true,audio:true })
                                    .then(function (stream) {
                                        video.srcObject = stream;
                                    })
                                    .catch(function (err0r) {
                                        console.log("Something went wrong!");
                                    });
                            }
                        </script>--}}

{{--                        <video id="video" autoplay="true" controls="controls"></video>--}}
{{--                        <div style="display:none;visibility:hidden" id="uip">{{$_SERVER['SERVER_ADDR']}}</div>--}}
{{--                        <script>--}}


{{--                            if (Hls.isSupported()) {--}}
{{--                                var ip_server = document.getElementById('uip').innerHTML;--}}
{{--                                console.log(ip_server,'herere');--}}
{{--                                var video = document.getElementById('video');--}}
{{--                                var hls = new Hls();--}}
{{--                                // bind them together--}}
{{--                                hls.attachMedia(video);--}}
{{--                                hls.on(Hls.Events.MEDIA_ATTACHED, function () {--}}
{{--                                    console.log("video and hls.js are now bound together !");--}}
{{--                                    hls.loadSource("https://wowzaprod270-i.akamaihd.net/hls/live/1003290/64c27d37/playlist.m3u8");--}}
{{--                                    hls.on(Hls.Events.MANIFEST_PARSED, function (event, data) {--}}
{{--                                        console.log("manifest loaded, found " + data.levels.length + " quality level");--}}
{{--                                    });--}}
{{--                                });--}}
{{--                            }--}}
{{--                        </script>--}}
                    </div>
                </div>
            </div>
        </div>
{{--        <video autoplay="true" id="videoElement">--}}

{{--        </video>--}}
{{--        <script>--}}

{{--            var startTime = Date.now();--}}
{{--            var detectPermissionDialog = function(allowed) {--}}
{{--                if (Date.now() - startTime > timeThreshold) {--}}
{{--                    // dialog was shown--}}
{{--                }--}}
{{--            };--}}
{{--            var successCallback = function(error) {--}}
{{--                detectPermissionDialog(true);--}}
{{--            };--}}
{{--            var errorCallback = function(error) {--}}
{{--                if ((error.name == 'NotAllowedError') ||--}}
{{--                    (error.name == 'PermissionDismissedError')) {--}}
{{--                    detectPermissionDialog(false);--}}
{{--                }--}}
{{--            };--}}
{{--            navigator.mediaDevices.getUserMedia()--}}
{{--                .then(successCallback, errorCallback);--}}


{{--            var video = document.getElementById("videoElement");--}}

{{--            if (navigator.mediaDevices.getUserMedia) {--}}
{{--                navigator.mediaDevices.getUserMedia({ video: true,audio:true })--}}
{{--                    .then(function (stream) {--}}
{{--                        video.srcObject = stream;--}}
{{--                    })--}}
{{--                    .catch(function (err0r) {--}}
{{--                        console.log("Something went wrong!");--}}
{{--                    });--}}
{{--            }--}}
{{--        </script>--}}
        <style>
            .booth{
                height: 400px;
                background: #ccc;
                border: 10px solid #ddd;
                margin: 0 auto;
            }
        </style>
@endsection
