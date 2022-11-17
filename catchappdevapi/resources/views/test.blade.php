<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

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


        <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <script>
            function showMessage(messageHTML) {
                $('#chat-box').append(messageHTML);
            }

            $(document).ready(function(){
                var websocket = new WebSocket("ws://localhost:8090/demo/php-socket.php");
                websocket.onopen = function(event) {
                    showMessage("<div class='chat-connection-ack'>Connection is established!</div>");
                }
                websocket.onmessage = function(event) {
                    var Data = JSON.parse(event.data);
                    console.log(Data);
                    if(Data.message_type == 10 || true){
                        showMessage("<div class='"+Data.message_type+"'>"+Data.message+"</div>");
                    }

                    $('#chat-message').val('');
                };

                websocket.onerror = function(event){
                    showMessage("<div class='error'>Problem due to some Error</div>");
                };
                websocket.onclose = function(event){
                    showMessage("<div class='chat-connection-ack'>Connection Closed</div>");
                };

                $('#frmChat').on("submit",function(event){
                    event.preventDefault();
                    $('#chat-user').attr("type","hidden");
                    var messageJSON = {
                        chat_user: $('#chat-user').val(),
                        chat_message: $('#chat-message').val(),
                        user_id : $('#chat-message-type').val()
                    };
                    console.log(messageJSON);
                    websocket.send(JSON.stringify(messageJSON));
                });
            });


        </script>
        </head>
        <body>
        <form name="frmChat" id="frmChat">
            <div id="chat-box"></div>
            <input type="text" name="chat-user" id="chat-user" placeholder="Name" class="chat-input" required />
            <input type="text" name="chat-message" id="chat-message" placeholder="Message"  class="chat-input chat-message" required />
            <input type="text" name="chat-message-type" id="chat-message-type" placeholder="Message"  class="chat-input chat-message" required />
            <input type="submit" id="btnSend" name="send-chat-message" value="Send" >
        </form>
        </body>


    </div>
</div>
</body>
</html>
