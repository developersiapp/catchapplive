<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="{{asset('dist/js/jquery.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/require.js/2.3.6/require.min.js"></script>

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    <div class="content">


        <script>
            function showMessage(messageHTML) {
                $('#chat-box').append('<hr><p><b>'+messageHTML+'</b></p>');
            };

            $(document).ready(function(){
                var SECURE = true;

                var port = 8080;
                var host;
                var ws;
                if (SECURE === true)
                {
                    // host = '14.98.71.171';
                    host = 'catchapp.iapplabz.co.in';
                    ws = new WebSocket('wss://'+host+':'+port);
                }
                else
                {
                    host = '0.0.0.0';
                    ws = new WebSocket('ws://'+host+':'+port);
                }
                ws.onopen = function(event) {
                    showMessage("Connection is established!");
                    console.log(host+' is connected on port : '+port);
                };
                ws.onmessage = function(event) {
                    var Data = JSON.parse(event.data);
                    console.log(Data);
                    showMessage(Data.message);
                };

                ws.onerror = function(event){
                    showMessage("Problem due to some Error");
                };

                ws.onclose = function(event){
                    showMessage("Connection Closed");
                };

                $('#frmChat').on("submit",function(event){
                    event.preventDefault();
                    var messageJSON = {
                        story_id: $('#chat-message').val(),
                        user_id : $('#chat-message-type').val()
                    };
                    console.log(messageJSON);
                    message =JSON.stringify(messageJSON);
                    sendData(message);
                });


                function waitForConnection(callback, interval) {
                    console.log(host+' is trying to connect on : '+port);
                    if (ws.readyState === 1) {
                        callback();
                    } else {
                        setTimeout(function () {
                            waitForConnection(callback, interval);
                        }, interval);
                    }
                }
                function sendData(messageJSON, callback)
                {
                    waitForConnection(function () {
                        ws.send(message);
                        if (typeof callback !== 'undefined') {
                            callback();
                        }
                    }, 1000);
                }
            });
        </script>
        </head>
        <body>
        <form name="frmChat" id="frmChat">
            <div id="chat-box">

            </div>
            <input type="text" name="chat-message-type" id="chat-message-type" placeholder="Enter User ID"  class="chat-input chat-message" required />
            <input type="text" name="chat-message" id="chat-message" placeholder="Enter Story ID"  class="chat-input chat-message" required />
            <input type="submit" id="btnSend" name="send-chat-message" value="Send" >

        </form>
        </body>
    </div>
</div>
</body>
</html>
